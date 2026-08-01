<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FormationModule;
use App\Models\FormationModuleFile;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductAdminController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $data['slug'] = Str::slug($data['name']);
        $data['features'] = $this->parseFeatures($request->input('features_raw'));
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products/images', 'public');
        }
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('products/files', 'local');
        }

        $product = Product::create($data);

        if ($data['product_type'] === 'formation') {
            $this->syncModules($request, $product);
            $this->syncGalleryImages($request, $product);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product->load('modules', 'galleryImages');
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);
        $data['features'] = $this->parseFeatures($request->input('features_raw'));
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products/images', 'public');
        }
        if ($request->hasFile('file')) {
            if ($product->file_path) {
                Storage::disk('local')->delete($product->file_path);
            }
            $data['file_path'] = $request->file('file')->store('products/files', 'local');
        }

        $product->update($data);

        if ($data['product_type'] === 'formation') {
            $this->syncModules($request, $product);
            $this->syncGalleryImages($request, $product);
        } else {
            $this->deleteModulesAndGallery($product);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        $this->deleteModulesAndGallery($product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->file_path) {
            Storage::disk('local')->delete($product->file_path);
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé.');
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);
        $status = $product->is_featured ? 'mis en vedette' : 'retiré des vedettes';
        return back()->with('success', "Produit {$status}.");
    }

    public function deleteModule(Request $request, Product $product, FormationModule $module)
    {
        if ($module->product_id !== $product->id) {
            abort(404);
        }

        if ($module->file_path) {
            Storage::disk('local')->delete($module->file_path);
        }
        $module->delete();

        return back()->with('success', 'Module supprimé.');
    }

    public function deleteGalleryImage(Request $request, Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image supprimée.');
    }

    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric|min:0|lt:price',
            'currency'          => 'required|string|max:10',
            'product_type'      => 'required|in:ebook,formation,template',
            'stock'             => 'required|integer|min:-1',
            'is_active'         => 'nullable|boolean',
            'is_featured'       => 'nullable|boolean',
            'category_id'       => 'nullable|exists:categories,id',
            'image'             => 'nullable|image|max:7168',
            'file'              => 'nullable|file|max:153600',
            'preview_url'       => 'nullable|url',
            'modules'                   => 'nullable|array',
            'modules.*.title'           => 'nullable|string|max:255',
            'modules.*.description'     => 'nullable|string',
            'modules.*.external_url'    => 'nullable|string|max:500',
            'modules.*.file'            => 'nullable|file|max:153600',
            'modules.*.files'           => 'nullable|array',
            'modules.*.files.*'         => 'nullable|file|max:153600',
            'modules.*.existing_path'   => 'nullable|string',
            'gallery'           => 'nullable|array',
            'gallery.*'         => 'image|max:7168',
        ]);
    }

    private function syncModules(Request $request, Product $product): void
    {
        $modulesData = $request->input('modules', []);
        \Log::info('syncModules called', ['product_id' => $product->id, 'modules_count' => count($modulesData), 'modules_data' => $modulesData]);
        $existingIds = $product->modules()->pluck('id')->toArray();
        $seenIds = [];

        foreach ($modulesData as $index => $mod) {
            if (empty($mod['title']) && empty($mod['description']) && empty($mod['external_url'])) {
                continue;
            }
            $moduleData = [
                'title'        => $mod['title'] ?? '',
                'description'  => $mod['description'] ?? null,
                'external_url' => !empty($mod['external_url']) ? $mod['external_url'] : null,
                'sort_order'   => $index,
            ];

            // Fichier principal (compatibilité)
            $fileKey = "modules.{$index}.file";
            if ($request->hasFile($fileKey)) {
                $moduleData['file_path'] = $request->file($fileKey)->store('formations/modules', 'local');
            } elseif (!empty($mod['existing_path'])) {
                $moduleData['file_path'] = $mod['existing_path'];
            } else {
                $moduleData['file_path'] = null;
            }

            if (!empty($mod['id'])) {
                $module = FormationModule::find($mod['id']);
                if ($module && $module->product_id === $product->id) {
                    if ($request->hasFile($fileKey) && $module->file_path) {
                        Storage::disk('local')->delete($module->file_path);
                    }
                    $module->update($moduleData);
                    $seenIds[] = $module->id;
                }
            } else {
                $module = FormationModule::create(array_merge($moduleData, ['product_id' => $product->id]));
                $seenIds[] = $module->id;
            }

            // Gestion des fichiers multiples du module
            $this->syncModuleFiles($request, $module, $index);
            \Log::info('Module synced', ['module_id' => $module->id, 'title' => $module->title, 'file_path' => $module->file_path]);
        }

        $toDelete = array_diff($existingIds, $seenIds);
        foreach ($toDelete as $id) {
            $module = FormationModule::find($id);
            if ($module) {
                if ($module->file_path) {
                    Storage::disk('local')->delete($module->file_path);
                }
                foreach ($module->files as $file) {
                    Storage::disk('local')->delete($file->file_path);
                }
                $module->delete();
            }
        }
    }

    private function syncModuleFiles(Request $request, FormationModule $module, int $moduleIndex): void
    {
        $filesKey = "modules.{$moduleIndex}.files";
        $hasFiles = $request->hasFile($filesKey);
        \Log::info('syncModuleFiles', ['module_id' => $module->id, 'filesKey' => $filesKey, 'hasFiles' => $hasFiles]);
        if ($hasFiles) {
            $sortOrder = $module->files()->max('sort_order') ?? 0;
            foreach ($request->file($filesKey) as $file) {
                $path = $file->store('formations/modules', 'local');
                FormationModuleFile::create([
                    'formation_module_id' => $module->id,
                    'file_path'           => $path,
                    'original_name'       => $file->getClientOriginalName(),
                    'sort_order'          => ++$sortOrder,
                ]);
            }
        }
    }

    private function syncGalleryImages(Request $request, Product $product): void
    {
        if ($request->hasFile('gallery')) {
            $sortOrder = $product->galleryImages()->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => ++$sortOrder,
                ]);
            }
        }
    }

    public function deleteModuleFile(Request $request, Product $product, FormationModule $module, FormationModuleFile $file)
    {
        if ($file->formation_module_id !== $module->id || $module->product_id !== $product->id) {
            abort(404);
        }

        Storage::disk('local')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'Fichier supprimé.');
    }

    private function deleteModulesAndGallery(Product $product): void
    {
        foreach ($product->modules as $module) {
            if ($module->file_path) {
                Storage::disk('local')->delete($module->file_path);
            }
            foreach ($module->files as $file) {
                Storage::disk('local')->delete($file->file_path);
            }
        }
        $product->modules()->delete();

        foreach ($product->galleryImages as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $product->galleryImages()->delete();
    }

    private function parseFeatures(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }
        return array_values(array_filter(
            array_map('trim', explode("\n", $raw))
        ));
    }
}
