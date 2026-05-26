<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'supplierRecord'])->paginate(10);
        $modules = auth()->user()->role->modules()->get();
        return view('modules.products-list', [
            'products' => $products,
            'modules' => $modules,
        ]);
    }

    public function create()
    {
        $modules = auth()->user()->role->modules()->get();
        $categories = Category::where('status', 'active')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        return view('modules.products-create', [
            'modules' => $modules,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'product_code' => 'nullable|string|unique:products',
            'description' => 'nullable|string|max:1000',
            'cost_price' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'is_unlimited_stock' => 'nullable|boolean',
            'barcode' => 'nullable|string|unique:products',
            'image' => 'nullable|string|max:2048',
            'supplier' => 'nullable|string|max:255',
            'discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['is_unlimited_stock'] = $request->boolean('is_unlimited_stock');
        if ($validated['is_unlimited_stock']) {
            $validated['quantity'] = 0;
        }

        Product::create($validated);
        return redirect()->route('inventory.index')->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $modules = auth()->user()->role->modules()->get();
        return view('modules.products-show', [
            'product' => $product,
            'modules' => $modules,
        ]);
    }

    public function edit(Product $product)
    {
        $modules = auth()->user()->role->modules()->get();
        $categories = Category::where('status', 'active')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        return view('modules.products-edit', [
            'product' => $product,
            'modules' => $modules,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'product_code' => 'nullable|string|unique:products,product_code,' . $product->id,
            'description' => 'nullable|string|max:1000',
            'cost_price' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'is_unlimited_stock' => 'nullable|boolean',
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'image' => 'nullable|string|max:2048',
            'supplier' => 'nullable|string|max:255',
            'discount' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['is_unlimited_stock'] = $request->boolean('is_unlimited_stock');
        if ($validated['is_unlimited_stock']) {
            $validated['quantity'] = 0;
        }

        $product->update($validated);
        return redirect()->route('inventory.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('inventory.index')->with('success', 'Product deleted successfully');
    }
}
