<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleDriveCategory;
use App\Models\GoogleDriveSubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubCategoryController extends Controller
{
    public function index(): View
    {
        $categories = GoogleDriveCategory::with('subCategories.options')->latest()->paginate(10);
        return view('admin.sub-categories.index', compact('categories'));
    }

    public function create(): View
    {
        $categories = GoogleDriveCategory::latest()->get();
        return view('admin.sub-categories.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'google_category_id' => 'required|exists:google_drive_categories,id',
            'name' => 'required|string|max:255',
            'options' => 'array',
            'options.*' => 'string|max:255',
        ], [
            'google_category_id.required' => 'Kategori wajib dipilih.',
            'name.required' => 'Nama sub-kategori wajib diisi.',
            'name.unique' => 'Sub-kategori dengan nama ini sudah ada untuk kategori yang dipilih.',
        ]);

        $category = GoogleDriveCategory::findOrFail($request->google_category_id);

        // Check uniqueness of name within the category
        if ($category->subCategories()->where('name', $request->name)->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Sub-kategori dengan nama ini sudah ada untuk kategori yang dipilih.');
        }

        $subCategory = GoogleDriveSubCategory::create([
            'google_category_id' => $request->google_category_id,
            'name' => $request->name,
        ]);

        // Create options if provided
        if ($request->has('options') && is_array($request->options)) {
            $order = 0;
            foreach ($request->options as $optionName) {
                if (!empty(trim($optionName))) {
                    $subCategory->options()->create([
                        'name' => trim($optionName),
                        'order' => $order++,
                    ]);
                }
            }
        }

        return redirect()->route('admin.sub-categories.index')
            ->with('success', 'Sub-kategori "' . $request->name . '" berhasil ditambahkan dengan ' . count(array_filter($request->options ?? [])) . ' pilihan.');
    }

    public function edit(string $id): View
    {
        $subCategory = GoogleDriveSubCategory::with('options')->findOrFail($id);
        $categories = GoogleDriveCategory::latest()->get();
        return view('admin.sub-categories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $subCategory = GoogleDriveSubCategory::findOrFail($id);

        $request->validate([
            'google_category_id' => 'required|exists:google_drive_categories,id',
            'name' => 'required|string|max:255',
            'options' => 'array',
            'options.*' => 'string|max:255',
        ], [
            'google_category_id.required' => 'Kategori wajib dipilih.',
            'name.required' => 'Nama sub-kategori wajib diisi.',
        ]);

        $category = GoogleDriveCategory::findOrFail($request->google_category_id);

        // Check uniqueness of name within the category (excluding current record)
        if ($category->subCategories()
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists()
        ) {
            return back()
                ->withInput()
                ->with('error', 'Sub-kategori dengan nama ini sudah ada untuk kategori yang dipilih.');
        }

        $subCategory->update([
            'google_category_id' => $request->google_category_id,
            'name' => $request->name,
        ]);

        // Update options
        $subCategory->options()->delete();

        if ($request->has('options') && is_array($request->options)) {
            $order = 0;
            foreach ($request->options as $optionName) {
                if (!empty(trim($optionName))) {
                    $subCategory->options()->create([
                        'name' => trim($optionName),
                        'order' => $order++,
                    ]);
                }
            }
        }

        return redirect()->route('admin.sub-categories.index')
            ->with('success', 'Sub-kategori berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $subCategory = GoogleDriveSubCategory::findOrFail($id);

        if ($subCategory->files()->count() > 0) {
            return back()->with('error', 'Sub-kategori tidak dapat dihapus karena masih memiliki ' . $subCategory->files()->count() . ' file.');
        }

        $subCategory->delete();

        return back()->with('success', 'Sub-kategori berhasil dihapus.');
    }
}
