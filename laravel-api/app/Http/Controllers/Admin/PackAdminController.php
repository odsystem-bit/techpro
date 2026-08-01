<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Pack;
use App\Models\PackFile;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackAdminController extends Controller
{
    public function index()
    {
        $packs = Pack::with('category', 'products')->latest()->paginate(20);
        return view('admin.packs.index', compact('packs'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.packs.form', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePack($request);
        $data['slug'] = Str::slug($data['name']);
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('packs/images', 'public');
        }

        $pack = Pack::create($data);

        // Attach selected products to pack
        if ($request->has('product_ids')) {
            $pack->products()->attach($request->input('product_ids'));
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $filePath = $file->store('packs/files', 'public');
                PackFile::create([
                    'pack_id' => $pack->id,
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.packs.index')->with('success', 'Pack créé avec succès.');
    }

    public function edit(Pack $pack)
    {
        $pack->load('files');
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedProductIds = $pack->products->pluck('id')->toArray();
        return view('admin.packs.form', compact('pack', 'categories', 'products', 'selectedProductIds'));
    }

    public function update(Request $request, Pack $pack)
    {
        $data = $this->validatePack($request, $pack->id);
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('packs/images', 'public');
        }

        $pack->update($data);

        // Sync selected products to pack
        if ($request->has('product_ids')) {
            $pack->products()->sync($request->input('product_ids'));
        } else {
            $pack->products()->detach();
        }

        // Delete selected files
        if ($request->has('delete_files')) {
            $filesToDelete = PackFile::whereIn('id', $request->input('delete_files'))
                ->where('pack_id', $pack->id)
                ->get();
            
            foreach ($filesToDelete as $file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            $maxSortOrder = $pack->files()->max('sort_order') ?? 0;
            foreach ($request->file('files') as $index => $file) {
                $filePath = $file->store('packs/files', 'public');
                PackFile::create([
                    'pack_id' => $pack->id,
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.packs.index')->with('success', 'Pack mis à jour.');
    }

    public function destroy(Pack $pack)
    {
        $pack->products()->detach();
        $pack->delete();
        return redirect()->route('admin.packs.index')->with('success', 'Pack supprimé.');
    }

    private function validatePack(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric|min:0|lt:price',
            'currency'          => 'required|string|max:10',
            'is_active'         => 'nullable|boolean',
            'is_featured'       => 'nullable|boolean',
            'category_id'       => 'nullable|exists:categories,id',
            'image'             => 'nullable|image|max:7168',
            'product_ids'       => 'nullable|array',
            'product_ids.*'     => 'exists:products,id',
            'files'             => 'nullable|array',
            'files.*'           => 'file|max:51200',
        ]);
    }
}
