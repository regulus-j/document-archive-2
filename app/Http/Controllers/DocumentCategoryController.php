<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentCategoryController extends Controller
{
    /**
     * Display a paginated listing of document categories.
     */
    public function index(Request $request): View
    {
        $query = DocumentCategory::withCount('documents');

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('category', 'like', '%' . $search . '%');
        }

        $categories = $query->orderBy('category')->paginate(10)->withQueryString();

        return view('document-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('document-categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category' => 'required|string|max:255|unique:document_categories,category',
        ]);

        DocumentCategory::create(['category' => $request->category]);

        return redirect()->route('document-categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(DocumentCategory $documentCategory): View
    {
        return view('document-categories.edit', compact('documentCategory'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, DocumentCategory $documentCategory): RedirectResponse
    {
        $request->validate([
            'category' => 'required|string|max:255|unique:document_categories,category,' . $documentCategory->id,
        ]);

        $documentCategory->update(['category' => $request->category]);

        return redirect()->route('document-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(DocumentCategory $documentCategory): RedirectResponse
    {
        $documentCategory->delete();

        return redirect()->route('document-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
