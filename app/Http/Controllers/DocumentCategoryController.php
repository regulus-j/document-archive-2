<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::latest()->paginate(10);

        return view('documents.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255|unique:document_categories,category',
        ]);

        DocumentCategory::create($validated);

        return redirect()->route('document-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function update(Request $request, DocumentCategory $documentCategory)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255|unique:document_categories,category,' . $documentCategory->id,
        ]);

        $documentCategory->update($validated);

        return redirect()->route('document-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(DocumentCategory $documentCategory)
    {
        $documentCategory->delete();

        return redirect()->route('document-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
