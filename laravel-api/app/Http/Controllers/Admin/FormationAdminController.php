<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FormationModule;
use App\Models\FormationModuleFile;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormationAdminController extends Controller
{
    public function index()
    {
        $formations = Product::where('product_type', 'formation')
            ->with('category')
            ->withCount('modules')
            ->latest()
            ->paginate(20);

        return view('admin.formations.index', compact('formations'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product = null;
        $isEdit = false;
        return view('admin.formations.form', compact('categories', 'product', 'isEdit'));
    }

    public function store(Request $request)
    {
        $data = $this->validateFormation($request);
        $data['slug'] = Str::slug($data['name']);
        $data['product_type'] = 'formation';
        $data['features'] = $this->parseFeatures($request->input('features_raw'));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['stock'] = -1;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products/images', 'public');
        }

        $product = Product::create($data);

        $this->syncModules($request, $product);
        $this->syncGalleryImages($request, $product);

        return redirect()->route('admin.formations.index')->with('success', 'Formation créée avec succès.');
    }

    public function edit(Product $product)
    {
        abort_if($product->product_type !== 'formation', 404);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product->load('modules.files', 'galleryImages');
        $isEdit = true;

        return view('admin.formations.form', compact('product', 'categories', 'isEdit'));
    }

    public function update(Request $request, Product $product)
    {
        abort_if($product->product_type !== 'formation', 404);

        $data = $this->validateFormation($request);
        $data['slug'] = Str::slug($data['name']);
        $data['features'] = $this->parseFeatures($request->input('features_raw'));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['stock'] = -1;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products/images', 'public');
        }

        $product->update($data);

        $this->syncModules($request, $product);
        $this->syncGalleryImages($request, $product);

        return redirect()->route('admin.formations.index')->with('success', 'Formation mise à jour avec succès.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->product_type !== 'formation', 404);

        $this->deleteModulesAndGallery($product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.formations.index')->with('success', 'Formation supprimée.');
    }

    public function deleteModule(Request $request, Product $product, FormationModule $module)
    {
        if ($module->product_id !== $product->id) {
            abort(404);
        }

        if ($module->file_path) {
            Storage::disk('local')->delete($module->file_path);
        }
        foreach ($module->files as $file) {
            Storage::disk('local')->delete($file->file_path);
        }
        $module->delete();

        return back()->with('success', 'Module supprimé.');
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

    public function deleteGalleryImage(Request $request, Product $product, $imageId)
    {
        $image = $product->galleryImages()->where('id', $imageId)->first();
        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        return back()->with('success', 'Image supprimée.');
    }

    private function validateFormation(Request $request): array
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
            'preview_url'       => 'nullable|string|max:500',
            'features_raw'      => 'nullable|string',
            'modules'                   => 'nullable|array',
            'modules.*.title'           => 'nullable|string|max:255',
            'modules.*.description'     => 'nullable|string',
            'modules.*.external_url'    => 'nullable|string|max:500',
            'modules.*.file'            => 'nullable|file|max:5242880',
            'modules.*.files'           => 'nullable|array',
            'modules.*.files.*'         => 'nullable|file|max:5242880',
            'modules.*.existing_path'   => 'nullable|string',
            'gallery'           => 'nullable|array',
            'gallery.*'         => 'image|max:7168',
        ], [
            'name.required'              => 'Le titre de la formation est obligatoire.',
            'price.required'             => 'Le prix est obligatoire.',
            'price.numeric'              => 'Le prix doit être un nombre.',
            'discount_price.lt'          => 'Le prix promo doit être inférieur au prix normal.',
            'image.image'                => 'L\'image de couverture doit être une image valide.',
            'image.max'                  => 'L\'image de couverture ne doit pas dépasser 7 Mo.',
            'modules.*.file.max'         => 'Le fichier principal d\'un module ne doit pas dépasser 5 Go.',
            'modules.*.files.*.max'      => 'Chaque fichier supplémentaire ne doit pas dépasser 5 Go.',
            'modules.*.title.max'        => 'Le titre d\'un module ne doit pas dépasser 255 caractères.',
            'gallery.*.image'            => 'La galerie doit contenir uniquement des images.',
            'gallery.*.max'              => 'Chaque image de la galerie ne doit pas dépasser 7 Mo.',
        ]);
    }

    private function syncModules(Request $request, Product $product): void
    {
        $modulesData = $request->input('modules', []);
        \Log::info('syncModules called', ['product_id' => $product->id, 'modules_count' => count($modulesData), 'modules_data' => $modulesData]);

        if (empty($modulesData)) {
            \Log::warning('syncModules: aucun module reçu', ['product_id' => $product->id]);
            return;
        }

        $existingIds = $product->modules()->pluck('id')->toArray();
        $seenIds = [];

        foreach ($modulesData as $index => $mod) {
            if (empty($mod['title']) && empty($mod['description']) && empty($mod['external_url'])) {
                \Log::info('syncModules: module ignoré (vide)', ['index' => $index]);
                continue;
            }

            $moduleData = [
                'title'        => $mod['title'] ?? '',
                'description'  => $mod['description'] ?? null,
                'external_url' => !empty($mod['external_url']) ? $mod['external_url'] : null,
                'sort_order'   => $index,
            ];

            $fileKey = "modules.{$index}.file";
            if ($request->hasFile($fileKey)) {
                $moduleData['file_path'] = $request->file($fileKey)->store('formations/modules', 'local');
                \Log::info('syncModules: fichier principal stocké', ['index' => $index, 'path' => $moduleData['file_path']]);
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

            $this->syncModuleFiles($request, $module, $index);
            \Log::info('Module synced', ['module_id' => $module->id, 'title' => $module->title, 'has_file' => $request->hasFile("modules.{$index}.file"), 'has_multi_files' => $request->hasFile("modules.{$index}.files")]);
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
        if ($request->hasFile($filesKey)) {
            $files = $request->file($filesKey);
            $sortOrder = $module->files()->max('sort_order') ?? 0;
            foreach ($files as $file) {
                $path = $file->store('formations/modules', 'local');
                FormationModuleFile::create([
                    'formation_module_id' => $module->id,
                    'file_path'           => $path,
                    'original_name'       => $file->getClientOriginalName(),
                    'sort_order'          => ++$sortOrder,
                ]);
            }
            \Log::info('Module files synced', ['module_id' => $module->id, 'count' => count($files), 'files' => array_map(fn($f) => $f->getClientOriginalName(), $files)]);
        } else {
            \Log::info('syncModuleFiles: aucun fichier supplémentaire', ['module_id' => $module->id, 'module_index' => $moduleIndex]);
        }
    }

    private function syncGalleryImages(Request $request, Product $product): void
    {
        if ($request->hasFile('gallery')) {
            $sortOrder = $product->galleryImages()->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('products/gallery', 'public');
                \App\Models\ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => ++$sortOrder,
                ]);
            }
        }
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
