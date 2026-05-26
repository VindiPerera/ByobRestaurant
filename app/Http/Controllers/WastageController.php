<?php

namespace App\Http\Controllers;

use App\Models\Wastage;
use App\Models\Product;
use Illuminate\Http\Request;

class WastageController extends Controller
{
    public function index()
    {
        $wastages = Wastage::with('product')->paginate(10);
        $modules = auth()->user()->role->modules()->get();
        return view('modules.wastages-list', [
            'wastages' => $wastages,
            'modules' => $modules,
        ]);
    }

    public function create()
    {
        $products = Product::where('status', 'active')->get();
        $modules = auth()->user()->role->modules()->get();
        return view('modules.wastages-create', [
            'products' => $products,
            'modules' => $modules,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
            'date' => 'required|date',
        ]);

        $product = Product::find($validated['product_id']);
        if ($product->quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough quantity available']);
        }

        $product->decrement('quantity', $validated['quantity']);
        Wastage::create($validated);

        return redirect()->route('wastage.index')->with('success', 'Wastage recorded successfully');
    }

    public function show(Wastage $wastage)
    {
        $modules = auth()->user()->role->modules()->get();
        return view('modules.wastages-show', [
            'wastage' => $wastage,
            'modules' => $modules,
        ]);
    }

    public function edit(Wastage $wastage)
    {
        $products = Product::where('status', 'active')->get();
        $modules = auth()->user()->role->modules()->get();
        return view('modules.wastages-edit', [
            'wastage' => $wastage,
            'products' => $products,
            'modules' => $modules,
        ]);
    }

    public function update(Request $request, Wastage $wastage)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
            'date' => 'required|date',
        ]);

        $product = Product::find($validated['product_id']);
        $oldProduct = Product::find($wastage->product_id);

        if ($product->id === $oldProduct->id) {
            $quantityDiff = $validated['quantity'] - $wastage->quantity;
            if ($product->quantity < $quantityDiff) {
                return back()->withErrors(['quantity' => 'Not enough quantity available']);
            }
            $product->decrement('quantity', $quantityDiff);
        } else {
            $oldProduct->increment('quantity', $wastage->quantity);
            if ($product->quantity < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Not enough quantity available']);
            }
            $product->decrement('quantity', $validated['quantity']);
        }

        $wastage->update($validated);
        return redirect()->route('wastage.index')->with('success', 'Wastage updated successfully');
    }

    public function destroy(Wastage $wastage)
    {
        $product = $wastage->product;
        $product->increment('quantity', $wastage->quantity);
        $wastage->delete();

        return redirect()->route('wastage.index')->with('success', 'Wastage deleted successfully');
    }
}
