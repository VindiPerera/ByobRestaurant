<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order')->paginate(10);
        $modules = auth()->user()->role->modules()->get();

        return view('modules.categories-list', [
            'categories' => $categories,
            'modules' => $modules,
        ]);
    }

    public function create()
    {
        $modules = auth()->user()->role->modules()->get();

        return view('modules.categories-create', [
            'modules' => $modules,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['color'] = $validated['color'] ?: '#dc2626';
        $validated['icon'] = $validated['icon'] ?: 'utensils';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    public function edit(Category $category)
    {
        $modules = auth()->user()->role->modules()->get();

        return view('modules.categories-edit', [
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['color'] = $validated['color'] ?: '#dc2626';
        $validated['icon'] = $validated['icon'] ?: 'utensils';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}
