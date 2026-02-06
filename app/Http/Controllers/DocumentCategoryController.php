<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Auth::user()->companies()->first();
        
        if (!$company) {
            return redirect()->back()->with('error', 'You must be associated with a company to manage categories.');
        }

        $categories = DocumentCategory::where(function($query) use ($company) {
            $query->where('company_id', $company->id)
                  ->orWhere('is_global', true)
                  ->orWhereNull('company_id');
        })->orderBy('category')->paginate(15);

        return view('categories.index', compact('categories', 'company'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
        ]);

        $company = Auth::user()->companies()->first();
        
        if (!$company) {
            return redirect()->back()->with('error', 'You must be associated with a company to create categories.');
        }

        // Check for duplicate within the company
        $exists = DocumentCategory::where('category', $request->category)
            ->where(function($query) use ($company) {
                $query->where('company_id', $company->id)
                      ->orWhere('is_global', true)
                      ->orWhereNull('company_id');
            })->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'A category with this name already exists.')
                ->withInput();
        }

        DocumentCategory::create([
            'category' => $request->category,
            'company_id' => $company->id,
            'is_global' => false,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = DocumentCategory::findOrFail($id);
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Auth::user()->companies()->first();
        $category = DocumentCategory::findOrFail($id);

        // Only allow editing company-specific categories
        if ($category->is_global || ($category->company_id && $category->company_id !== $company->id)) {
            return redirect()->back()->with('error', 'You cannot edit this category.');
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category' => 'required|string|max:255',
        ]);

        $company = Auth::user()->companies()->first();
        $category = DocumentCategory::findOrFail($id);

        // Only allow editing company-specific categories
        if ($category->is_global || ($category->company_id && $category->company_id !== $company->id)) {
            return redirect()->back()->with('error', 'You cannot edit this category.');
        }

        // Check for duplicate
        $exists = DocumentCategory::where('category', $request->category)
            ->where('id', '!=', $id)
            ->where(function($query) use ($company) {
                $query->where('company_id', $company->id)
                      ->orWhere('is_global', true)
                      ->orWhereNull('company_id');
            })->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'A category with this name already exists.')
                ->withInput();
        }

        $category->update([
            'category' => $request->category,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Auth::user()->companies()->first();
        $category = DocumentCategory::findOrFail($id);

        // Only allow deleting company-specific categories
        if ($category->is_global || ($category->company_id && $category->company_id !== $company->id)) {
            return redirect()->back()->with('error', 'You cannot delete this category.');
        }

        // Check if category is in use
        if ($category->documents()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category that is in use by documents.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
