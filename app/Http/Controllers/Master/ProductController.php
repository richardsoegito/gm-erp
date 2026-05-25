<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\ProductBrand;
use App\Models\Master\ProductCategories;
use App\Models\Master\ProductUnit;
use App\Models\Master\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Master\ProductImage;
use App\Models\Master\ProductSize;
use App\Models\Master\ProductVariant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function generateProductId(): string
    {
        $last = Product::orderByDesc('id')->value('id');
 
        if (!$last) {
            return 'PRD-00001';
        }
 
        // Expects format PRD-XXXX; falls back gracefully for other formats.
        $parts  = explode('-', $last);
        $number = (int) end($parts);
 
        return 'PRD-' . str_pad($number + 1, 5, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        return view('product.index', [
            'editing' => false,
        ]);
    }

    public function show()
    {
        $products = Product::query()
            ->with(['brand', 'category', 'largeUnit', 'smallUnit'])
            ->latest()
            ->get()
            ->map(function ($product) {
                return [
                    'id'          => $product->id,
                    'uuid'        => $product->uuid,
                    'name'        => $product->name,
                    'slug'        => $product->slug,
                    'brand'       => $product->brand->name ?? '-',
                    'category'    => $product->category->name ?? '-',
                    'unit'        => $product->smallUnit->name ?? '-',
                    'status'      => $product->status ? 'Aktif' : 'Tidak Aktif',
                    'thumbnail'   => $product->thumbnail,
                    'created_at'  => $product->created_at->format('Y-m-d'),
                ];
            });

        return response()->json($products);
    }

    public function create()
    {
        $brands = ProductBrand::where('status', 1)->get();
        $categories = ProductCategories::where('status', 1)->get();
        $units = ProductUnit::where('status', 1)->get();
        return view('product.create', [
            'brands' => $brands,
            'categories' => $categories,
            'units' => $units,
            'generateId' => $this->generateProductId(),
            'editing' => false,
        ]);
    }

    public function edit(Product $product)
    {
        $product->load(['images' => fn($q) => $q->orderBy('sort_order'), 'sizes']);

        $brands     = ProductBrand::where('status', 1)->get();
        $categories = ProductCategories::where('status', 1)->get();
        $units      = ProductUnit::where('status', 1)->get();

        return view('product.create', [
            'brands'     => $brands,
            'categories' => $categories,
            'units'      => $units,
            'editing'    => true,
            'product'    => $product,
        ]);
    }

    // public function store(Request $request)
    // {
    //     // ------------------------------------------------------------------
    //     // 1. VALIDATION
    //     // ------------------------------------------------------------------
    //     $request->validate(
    //         [
    //             // Product Information
    //             'name'             => ['required', 'string', 'max:255'],
    //             'slug'             => ['nullable', 'string', 'max:255', 'unique:products,slug'],
    //             'brand_id'         => ['nullable', 'exists:product_brands,id'],
    //             'category_id'      => ['nullable', 'exists:product_categories,id'],
    //             'unit_id'          => ['nullable', 'exists:product_units,id'],
    //             'dimensions'       => ['nullable', 'string', 'max:100'],
    //             'description'      => ['nullable', 'string'],
 
    //             // SEO
    //             'meta_title'       => ['nullable', 'string', 'max:255'],
    //             'meta_description' => ['nullable', 'string', 'max:500'],
 
    //             // Thumbnail — single file, optional
    //             'thumbnail'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
 
    //             // Video — single file, optional
    //             'video'            => ['nullable', 'file', 'mimes:mp4,webm,ogg', 'max:51200'],
 
    //             // Images — at least 1 row required
    //             'images'           => ['required', 'array', 'min:1'],
    //             'images.*'         => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    //             'image_labels'     => ['required', 'array', 'min:1'],
    //             'image_labels.*'   => ['nullable', 'string', 'max:100'],
    //             'sort_order'       => ['nullable', 'array'],
    //             'sort_order.*'     => ['nullable', 'integer', 'min:0'],
 
    //             // Sizes — optional
    //             'size_width'       => ['nullable', 'array'],
    //             'size_width.*'     => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
    //             'size_length'      => ['nullable', 'array'],
    //             'size_length.*'    => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
    //             'size_height'      => ['nullable', 'array'],
    //             'size_height.*'    => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
    //         ],
    //         // ------------------------------------------------------------------
    //         // 2. CUSTOM MESSAGES
    //         // ------------------------------------------------------------------
    //         [
    //             // Product Information
    //             'name.required'              => 'Product name is required.',
    //             'name.max'                   => 'Product name may not exceed 255 characters.',
    //             'slug.unique'                => 'This slug is already used by another product.',
    //             'brand_id.exists'            => 'Selected brand is invalid.',
    //             'category_id.exists'         => 'Selected category is invalid.',
    //             'unit_id.exists'             => 'Selected unit is invalid.',
    //             'dimensions.max'             => 'Dimensions may not exceed 100 characters.',
 
    //             // SEO
    //             'meta_title.max'             => 'Meta title may not exceed 255 characters.',
    //             'meta_description.max'       => 'Meta description may not exceed 500 characters.',
 
    //             // Thumbnail
    //             'thumbnail.image'            => 'Thumbnail must be an image file.',
    //             'thumbnail.mimes'            => 'Thumbnail must be a JPG, JPEG, PNG, or WEBP file.',
    //             'thumbnail.max'              => 'Thumbnail may not exceed 5 MB.',
 
    //             // Video
    //             'video.file'                 => 'The video upload is not a valid file.',
    //             'video.mimes'                => 'Video must be an MP4, WEBM, or OGG file.',
    //             'video.max'                  => 'Video may not exceed 50 MB.',
 
    //             // Images
    //             'images.required'            => 'At least one product image is required.',
    //             'images.min'                 => 'At least one product image is required.',
    //             'images.*.required'          => 'Please select an image file for each image row.',
    //             'images.*.image'             => 'The uploaded file must be an image.',
    //             'images.*.mimes'             => 'Image must be a JPG, JPEG, PNG, or WEBP file.',
    //             'images.*.max'               => 'Each image may not exceed 5 MB.',
    //             'image_labels.required'      => 'Image label data is missing.',
    //             'image_labels.min'           => 'At least one image label row is required.',
 
    //             // Sizes
    //             'size_width.*.numeric'       => 'Width must be a number.',
    //             'size_width.*.min'           => 'Width cannot be negative.',
    //             'size_width.*.max'           => 'Width may not exceed 9999.99 cm.',
    //             'size_length.*.numeric'      => 'Length must be a number.',
    //             'size_length.*.min'          => 'Length cannot be negative.',
    //             'size_length.*.max'          => 'Length may not exceed 9999.99 cm.',
    //             'size_height.*.numeric'      => 'Height must be a number.',
    //             'size_height.*.min'          => 'Height cannot be negative.',
    //             'size_height.*.max'          => 'Height may not exceed 9999.99 cm.',
    //         ]
    //     );
 
    //     // ------------------------------------------------------------------
    //     // 3. PERSIST — wrapped in a transaction so everything rolls back
    //     //    cleanly if an error occurs mid-way.
    //     // ------------------------------------------------------------------
    //     DB::transaction(function () use ($request) {
 
    //         // ---- Thumbnail ----
    //         $thumbnailPath = null;
    //         if ($request->hasFile('thumbnail')) {
    //             $thumbnailPath = $request->file('thumbnail')
    //                 ->store('products/thumbnails', 'public');
    //         }
 
    //         // ---- Video ----
    //         $videoPath = null;
    //         if ($request->hasFile('video')) {
    //             $videoPath = $request->file('video')
    //                 ->store('products/videos', 'public');
    //         }
 
    //         // ---- Product ----
    //         $product = Product::create([
    //             'id'               => $this->generateProductId(),
    //             'uuid'             => Str::uuid(),
    //             'name'             => $request->input('name'),
    //             'slug'             => $request->input('slug')
    //                                     ?: Str::slug($request->input('name')),
    //             'brand_id'         => $request->input('brand_id')    ?: null,
    //             'category_id'      => $request->input('category_id') ?: null,
    //             'unit_id'          => $request->input('unit_id')     ?: null,
    //             'dimensions'       => $request->input('dimensions'),
    //             'description'      => $request->input('description'),
    //             'meta_title'       => $request->input('meta_title'),
    //             'meta_description' => $request->input('meta_description'),
    //             'thumbnail'        => $thumbnailPath,
    //             'video'            => $videoPath,
    //             'status'           => $request->boolean('status'),
    //             'created_by'       => auth()->id(),
    //         ]);
 
    //         // ---- Gallery Images ----
    //         foreach ($request->file('images', []) as $index => $file) {
    //             $path = $file->store("products/{$product->id}/images", 'public');
 
    //             ProductImage::create([
    //                 'product_id' => $product->id,
    //                 'label'      => $request->input("image_labels.{$index}"),
    //                 'path'       => $path,
    //                 'sort_order' => (int) ($request->input("sort_order.{$index}") ?? 0),
    //             ]);
    //         }
 
    //         // ---- Sizes ----
    //         $widths  = $request->input('size_width',  []);
    //         $lengths = $request->input('size_length', []);
    //         $heights = $request->input('size_height', []);
 
    //         foreach ($widths as $index => $width) {
    //             ProductSize::create([
    //                 'product_id' => $product->id,
    //                 'width'      => $width,
    //                 'length'     => $lengths[$index] ?? 0,
    //                 'height'     => $heights[$index] ?? 0,
    //             ]);
    //         }
    //     });
 
    //     return redirect()
    //         ->route('master.product.index')
    //         ->with('success', 'Product saved successfully.');
    // }

    // public function update(Request $request, Product $product)
    // {
    //     // ------------------------------------------------------------------
    //     // 1. VALIDATION
    //     // ------------------------------------------------------------------
    //     $request->validate(
    //         [
    //             'name'             => ['required', 'string', 'max:255'],
    //             'slug'             => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $product->id],
    //             'brand_id'         => ['nullable', 'exists:product_brands,id'],
    //             'category_id'      => ['nullable', 'exists:product_categories,id'],
    //             'unit_id'          => ['nullable', 'exists:product_units,id'],
    //             'dimensions'       => ['nullable', 'string', 'max:100'],
    //             'description'      => ['nullable', 'string'],

    //             'meta_title'       => ['nullable', 'string', 'max:255'],
    //             'meta_description' => ['nullable', 'string', 'max:500'],

    //             // File hanya wajib divalidasi kalau ada file baru yang diupload
    //             'thumbnail'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    //             'video'            => ['nullable', 'file', 'mimes:mp4,webm,ogg', 'max:51200'],

    //             // Images baru — nullable karena user mungkin tidak menambah gambar baru
    //             'images'           => ['nullable', 'array'],
    //             'images.*'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    //             'image_labels'     => ['nullable', 'array'],
    //             'image_labels.*'   => ['nullable', 'string', 'max:100'],
    //             'sort_order'       => ['nullable', 'array'],
    //             'sort_order.*'     => ['nullable', 'integer', 'min:0'],

    //             // Existing images (yang sudah ada di DB) — update label & sort_order-nya
    //             'existing_image_ids'         => ['nullable', 'array'],
    //             'existing_image_ids.*'       => ['nullable', 'exists:product_images,id'],
    //             'existing_image_labels'      => ['nullable', 'array'],
    //             'existing_image_labels.*'    => ['nullable', 'string', 'max:100'],
    //             'existing_sort_orders'       => ['nullable', 'array'],
    //             'existing_sort_orders.*'     => ['nullable', 'integer', 'min:0'],
    //             'deleted_image_ids'          => ['nullable', 'array'],
    //             'deleted_image_ids.*'        => ['nullable', 'exists:product_images,id'],

    //             'size_width'       => ['nullable', 'array'],
    //             'size_width.*'     => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
    //             'size_length'      => ['nullable', 'array'],
    //             'size_length.*'    => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
    //             'size_height'      => ['nullable', 'array'],
    //             'size_height.*'    => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
    //         ],
    //         [
    //             'name.required'           => 'Product name is required.',
    //             'name.max'                => 'Product name may not exceed 255 characters.',
    //             'slug.unique'             => 'This slug is already used by another product.',
    //             'thumbnail.image'         => 'Thumbnail must be an image file.',
    //             'thumbnail.mimes'         => 'Thumbnail must be a JPG, JPEG, PNG, or WEBP file.',
    //             'thumbnail.max'           => 'Thumbnail may not exceed 5 MB.',
    //             'video.file'              => 'The video upload is not a valid file.',
    //             'video.mimes'             => 'Video must be an MP4, WEBM, or OGG file.',
    //             'video.max'               => 'Video may not exceed 50 MB.',
    //             'images.*.image'          => 'The uploaded file must be an image.',
    //             'images.*.mimes'          => 'Image must be a JPG, JPEG, PNG, or WEBP file.',
    //             'images.*.max'            => 'Each image may not exceed 5 MB.',
    //         ]
    //     );

    //     // ------------------------------------------------------------------
    //     // 2. PERSIST
    //     // ------------------------------------------------------------------
    //     DB::transaction(function () use ($request, $product) {

    //         // ---- Thumbnail ----
    //         $thumbnailPath = $product->thumbnail; // default: tetap pakai yang lama

    //         if ($request->boolean('remove_thumbnail')) {
    //             // User klik tombol hapus thumbnail
    //             if ($thumbnailPath) {
    //                 Storage::disk('public')->delete($thumbnailPath);
    //             }
    //             $thumbnailPath = null;

    //         } elseif ($request->hasFile('thumbnail')) {
    //             // Upload thumbnail baru — hapus yang lama dulu
    //             if ($product->thumbnail) {
    //                 Storage::disk('public')->delete($product->thumbnail);
    //             }
    //             $thumbnailPath = $request->file('thumbnail')
    //                 ->store('products/thumbnails', 'public');
    //         }

    //         // ---- Video ----
    //         $videoPath = $product->video;

    //         if ($request->boolean('remove_video')) {
    //             if ($videoPath) {
    //                 Storage::disk('public')->delete($videoPath);
    //             }
    //             $videoPath = null;

    //         } elseif ($request->hasFile('video')) {
    //             if ($product->video) {
    //                 Storage::disk('public')->delete($product->video);
    //             }
    //             $videoPath = $request->file('video')
    //                 ->store('products/videos', 'public');
    //         }

    //         // ---- Update Product ----
    //         $product->update([
    //             'name'             => $request->input('name'),
    //             'slug'             => $request->input('slug') ?: Str::slug($request->input('name')),
    //             'brand_id'         => $request->input('brand_id')    ?: null,
    //             'category_id'      => $request->input('category_id') ?: null,
    //             'unit_id'          => $request->input('unit_id')     ?: null,
    //             'dimensions'       => $request->input('dimensions'),
    //             'description'      => $request->input('description'),
    //             'meta_title'       => $request->input('meta_title'),
    //             'meta_description' => $request->input('meta_description'),
    //             'thumbnail'        => $thumbnailPath,
    //             'video'            => $videoPath,
    //             'status'           => $request->boolean('status'),
    //             'updated_by'       => auth()->id(),
    //         ]);

    //         // ---- Hapus gambar yang ditandai deleted ----
    //         $deletedIds = $request->input('deleted_image_ids', []);
    //         if (!empty($deletedIds)) {
    //             $toDelete = ProductImage::whereIn('id', $deletedIds)
    //                 ->where('product_id', $product->id) // safety: pastikan milik produk ini
    //                 ->get();

    //             foreach ($toDelete as $img) {
    //                 Storage::disk('public')->delete($img->path);
    //                 $img->delete();
    //             }
    //         }

    //         // ---- Update label & sort_order gambar yang sudah ada ----
    //         $existingIds     = $request->input('existing_image_ids', []);
    //         $existingLabels  = $request->input('existing_image_labels', []);
    //         $existingSorts   = $request->input('existing_sort_orders', []);

    //         foreach ($existingIds as $i => $imgId) {
    //             ProductImage::where('id', $imgId)
    //                 ->where('product_id', $product->id)
    //                 ->update([
    //                     'label'      => $existingLabels[$i] ?? null,
    //                     'sort_order' => (int) ($existingSorts[$i] ?? 0),
    //                 ]);
    //         }

    //         // ---- Upload gambar baru (jika ada) ----
    //         foreach ($request->file('images', []) as $index => $file) {
    //             if (!$file) continue;

    //             $path = $file->store("products/{$product->id}/images", 'public');

    //             ProductImage::create([
    //                 'product_id' => $product->id,
    //                 'label'      => $request->input("image_labels.{$index}"),
    //                 'path'       => $path,
    //                 'sort_order' => (int) ($request->input("sort_order.{$index}") ?? 0),
    //             ]);
    //         }

    //         // ---- Sizes — hapus semua lalu insert ulang ----
    //         // Pola ini lebih simpel daripada diff satu per satu
    //         $product->sizes()->delete();

    //         $widths  = $request->input('size_width',  []);
    //         $lengths = $request->input('size_length', []);
    //         $heights = $request->input('size_height', []);

    //         foreach ($widths as $index => $width) {
    //             if (is_null($width) && is_null($lengths[$index] ?? null)) continue;

    //             ProductSize::create([
    //                 'product_id' => $product->id,
    //                 'width'      => $width  ?? 0,
    //                 'length'     => $lengths[$index] ?? 0,
    //                 'height'     => $heights[$index] ?? 0,
    //             ]);
    //         }
    //     });

    //     return redirect()
    //         ->route('master.product.index')
    //         ->with('success', 'Product updated successfully.');
    // }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($request, $validated) {

            $productId = $this->generateProductId(); // Pastikan ID ini di-generate dari Controller / Form sebelumnya

            $product = Product::create([
                'id'               => $productId,
                'uuid'             => Str::uuid(),
                'name'             => $validated['name'],
                'sku'              => $validated['sku'] ?? null, // Menyimpan SKU
                'slug'             => $validated['slug'] ?? Str::slug($validated['name']),
                'brand_id'         => $validated['brand_id']      ?? null,
                'category_id'      => $validated['category_id']   ?? null,
                'large_unit_id'    => $validated['large_unit_id'] ?? null, // Mengganti unit_id
                'small_unit_id'    => $validated['small_unit_id'] ?? null, // Tambahan satuan kecil
                'description'      => $validated['description']   ?? null,
                'status'           => $request->boolean('status'),
                'meta_title'       => $validated['meta_title']       ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);

            if ($request->hasFile('thumbnail')) {
                $product->update([
                    'thumbnail' => $request->file('thumbnail')
                                        ->store("products/{$productId}/thumbnail", 'public'),
                ]);
            }

            if ($request->hasFile('video')) {
                $product->update([
                    'video' => $request->file('video')
                                    ->store("products/{$productId}/video", 'public'),
                ]);
            }

            $this->syncNewImages($request, $product);
            $this->syncVariants($request, $product);
        });

        return redirect()->route('master.product.index')
                        ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);

        DB::transaction(function () use ($request, $validated, $product) {

            $productId = $product->id; // gunakan ID yang sudah ada

            $product->update([
                'name'             => $validated['name'],
                'sku'              => $validated['sku'] ?? null, // Menyimpan SKU
                'slug'             => $validated['slug'],
                'brand_id'         => $validated['brand_id']      ?? null,
                'category_id'      => $validated['category_id']   ?? null,
                'large_unit_id'    => $validated['large_unit_id'] ?? null, // Mengganti unit_id
                'small_unit_id'    => $validated['small_unit_id'] ?? null, // Tambahan satuan kecil
                'description'      => $validated['description']   ?? null,
                'status'           => $request->boolean('status'),
                'meta_title'       => $validated['meta_title']       ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);

            // ── Thumbnail ──────────────────────────────────────────────
            if ($request->boolean('remove_thumbnail')) {
                $this->deleteFile($product->thumbnail);
                $product->update(['thumbnail' => null]);
            } elseif ($request->hasFile('thumbnail')) {
                $this->deleteFile($product->thumbnail);
                $product->update([
                    'thumbnail' => $request->file('thumbnail')
                                        ->store("products/{$productId}/thumbnail", 'public'),
                ]);
            }

            // ── Video ──────────────────────────────────────────────────
            if ($request->boolean('remove_video')) {
                $this->deleteFile($product->video);
                $product->update(['video' => null]);
            } elseif ($request->hasFile('video')) {
                $this->deleteFile($product->video);
                $product->update([
                    'video' => $request->file('video')
                                    ->store("products/{$productId}/video", 'public'), // Dirapikan path-nya
                ]);
            }

            // ── Existing images — update label, sort_order & replace file ────────────
            $existingIds    = $request->input('existing_image_ids', []);
            $existingLabels = $request->input('existing_image_labels', []);
            $existingSorts  = $request->input('existing_sort_orders', []);

            // Tangkap array file gambar pengganti (jika ada)
            $replaceImages  = $request->file('replace_images', []); 

            foreach ($existingIds as $idx => $imgId) {
                $productImage = ProductImage::where('id', $imgId)
                                            ->where('product_id', $product->id)
                                            ->first();

                if ($productImage) {
                    // 1. Update teks label dan sort order
                    $productImage->update([
                        'label'      => $existingLabels[$idx] ?? null,
                        'sort_order' => $existingSorts[$idx]  ?? 0,
                    ]);

                    // 2. Cek apakah ada file pengganti yang diupload untuk ID gambar ini
                    if (isset($replaceImages[$imgId])) {
                        // Hapus file fisik gambar yang lama
                        $this->deleteFile($productImage->path);
                        
                        // Upload file fisik yang baru
                        $newPath = $replaceImages[$imgId]->store("products/{$product->id}/images", 'public');
                        
                        // Update path di database
                        $productImage->update(['path' => $newPath]);
                    }
                }
            }

            // ── Delete marked images ───────────────────────────────────
            $deletedIds = $request->input('deleted_image_ids', []);
            if ($deletedIds) {
                $toDelete = ProductImage::whereIn('id', $deletedIds)
                                        ->where('product_id', $product->id)
                                        ->get();
                foreach ($toDelete as $img) {
                    $this->deleteFile($img->path);
                    $img->delete();
                }
            }

            // ── New images ─────────────────────────────────────────────
            $this->syncNewImages($request, $product);

            // ── Variants ───────────────────────────────────────────────
            $this->syncVariants($request, $product);
        });

        return redirect()->route('master.product.index')
                        ->with('success', 'Produk berhasil diperbarui.');
    }

    // public function destroy(Product $product)
    // {
    //     DB::transaction(function () use ($product) {

    //         // Hapus semua file storage terkait produk ini
    //         if ($product->thumbnail) {
    //             Storage::disk('public')->delete($product->thumbnail);
    //         }
    //         if ($product->video) {
    //             Storage::disk('public')->delete($product->video);
    //         }

    //         // Hapus semua gambar gallery beserta filenya
    //         foreach ($product->images as $image) {
    //             Storage::disk('public')->delete($image->path);
    //         }

    //         // Hapus seluruh folder produk kalau sudah kosong
    //         Storage::disk('public')->deleteDirectory("products/{$product->id}");

    //         // Hapus relasi — bisa juga pakai onDelete('cascade') di migration
    //         $product->images()->delete();
    //         $product->sizes()->delete();

    //         $product->delete();
    //     });

    //     return redirect()
    //         ->route('master.product.index')
    //         ->with('success', 'Product deleted successfully.');
    // }

    public function destroy(Product $product)
    {
        try {
            // Hanya hapus parent-nya saja. 
            // Karena pakai SoftDeletes, ini otomatis hanya mengisi kolom 'deleted_at'.
            // File fisik di storage dibiarkan aman.
            $product->delete();

            return redirect()->route('master.product.index')
                            ->with('success', 'Product moved to trash successfully.');
                            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function forceDestroy(Product $product)
    {
        try {
            // 1. Tentukan path folder utama produk 
            // Asumsi PRD-00003 adalah SKU produk. Ubah ke $product->uuid jika ternyata itu UUID.
            $folderPath = 'products/' . $product->id; 

            // 2. Hapus LANGSUNG SATU FOLDER FULL
            // Ini otomatis menghapus folder PRD-00003 beserta sub-folder images, thumbnail, dan videonya
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            // 3. Hapus data relasi dari database (images)
            if ($product->images()->count() > 0) {
                $product->images()->forceDelete(); 
            }

            // 4. Hapus data relasi dari database (variants)
            $product->variants()->forceDelete();

            // 5. Hapus permanen data produk utama dari database
            $product->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data dan folder produk berhasil dihapus permanen.'
            ], 200);
                             
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ], 500);
        }
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $slugRule = $product
            ? 'nullable|string|max:255|unique:products,slug,' . $product->id
            : 'nullable|string|max:255|unique:products,slug';

        $rules = [
            'name'             => 'required|string|max:255',
            'sku'              => 'nullable|string|max:255',
            'slug'             => $slugRule,
            'brand_id'         => 'required|exists:product_brands,id',
            'category_id'      => 'required|exists:product_categories,id',
            'large_unit_id'    => 'required|exists:product_units,id',
            'small_unit_id'    => 'nullable|exists:product_units,id',
            'description'      => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'video'            => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:51200',
            'images'           => 'nullable|array',
            'images.*'         => 'nullable|image|mimes:jpeg,png,webp,gif|max:5120',
            'image_labels'     => 'nullable|array',
            'sort_order'       => 'nullable|array',
            'sort_order.*'     => 'nullable|integer|min:0',
            'size'             => 'nullable|array',
            'box_qty'          => 'nullable|array',
        ];

        $messages = [
            // Name & SKU
            'name.required'             => 'Nama produk wajib diisi.',
            'name.string'               => 'Nama produk harus berupa teks.',
            'name.max'                  => 'Nama produk maksimal 255 karakter.',
            'sku.string'                => 'SKU harus berupa teks.',
            'sku.max'                   => 'SKU maksimal 255 karakter.',

            // Slug
            'slug.string'               => 'Slug harus berupa teks.',
            'slug.max'                  => 'Slug maksimal 255 karakter.',
            'slug.unique'               => 'Slug / URL ini sudah digunakan oleh produk lain.',

            // Relasi (Brand, Category, Units)
            'brand_id.required'         => 'Brand / Merek wajib dipilih.',
            'brand_id.exists'           => 'Brand yang dipilih tidak valid atau tidak terdaftar.',
            'category_id.required'      => 'Kategori produk wajib dipilih.',
            'category_id.exists'        => 'Kategori yang dipilih tidak valid atau tidak terdaftar.',
            'large_unit_id.required'    => 'Satuan besar wajib dipilih.',
            'large_unit_id.exists'      => 'Satuan besar yang dipilih tidak valid.',
            'small_unit_id.required'    => 'Satuan kecil wajib dipilih.',
            'small_unit_id.exists'      => 'Satuan kecil yang dipilih tidak valid.',

            // Deskripsi & Meta
            'description.string'        => 'Deskripsi produk harus berupa teks.',
            'meta_title.string'         => 'Meta Title harus berupa teks.',
            'meta_title.max'            => 'Meta Title maksimal 255 karakter.',
            'meta_description.string'   => 'Meta Description harus berupa teks.',

            // Media (Thumbnail & Video)
            'thumbnail.image'           => 'Thumbnail harus berupa file gambar.',
            'thumbnail.mimes'           => 'Format thumbnail harus berupa: jpeg, png, atau webp.',
            'thumbnail.max'             => 'Ukuran thumbnail maksimal 5 MB.',
            'video.mimetypes'           => 'Format video harus berupa: mp4, webm, atau ogg.',
            'video.max'                 => 'Ukuran video maksimal 50 MB.',

            // Galeri Gambar (Images)
            'images.array'              => 'Format data galeri gambar tidak valid.',
            'images.*.image'            => 'File pada galeri harus berupa gambar.',
            'images.*.mimes'            => 'Format gambar galeri harus berupa: jpeg, png, webp, atau gif.',
            'images.*.max'              => 'Ukuran masing-masing gambar pada galeri maksimal 5 MB.',

            // Data Array Lainnya (Labels, Sort, Variants)
            'image_labels.array'        => 'Format data label gambar tidak valid.',
            'sort_order.array'          => 'Format data urutan gambar tidak valid.',
            'sort_order.*.integer'      => 'Urutan gambar harus berupa angka.',
            'sort_order.*.min'          => 'Urutan gambar tidak boleh kurang dari 0.',
            'size.array'                => 'Format data ukuran varian tidak valid.',
            'box_qty.array'             => 'Format data jumlah qty tidak valid.',
        ];

        return $request->validate($rules, $messages);
    }

    private function syncNewImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $productId  = $product->id;
        $files      = $request->file('images', []);
        $labels     = $request->input('image_labels', []);
        $sortOrders = $request->input('sort_order', []);

        foreach ($files as $idx => $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $product->images()->create([
                'path'       => $file->store("products/{$productId}/images", 'public'),
                'label'      => $labels[$idx]     ?? null,
                'sort_order' => $sortOrders[$idx] ?? 0,
            ]);
        }
    }

    private function syncVariants(Request $request, Product $product): void
    {
        $variantIds = $request->input('variant_ids', []); // ID hidden input dari form
        $sizes      = $request->input('size', []);
        $boxQtys    = $request->input('box_qty', []);

        $keepVariantIds = []; // Array penampung ID yang akan dipertahankan

        foreach ($sizes as $idx => $size) {
            $size = trim($size ?? '');
            if ($size === '') {
                continue; // skip baris kosong
            }

            $vId = $variantIds[$idx] ?? null;

            if ($vId) {
                // UPDATE VARIAN LAMA
                $existingVariant = ProductVariant::where('id', $vId)
                                                 ->where('product_id', $product->id)
                                                 ->first();
                if ($existingVariant) {
                    $existingVariant->update([
                        'size'    => $size,
                        'box_qty' => $boxQtys[$idx] ?? null,
                    ]);
                    $keepVariantIds[] = $existingVariant->id;
                }
            } else {
                // CREATE VARIAN BARU
                $newVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'size'       => $size,
                    'box_qty'    => $boxQtys[$idx] ?? null,
                ]);
                $keepVariantIds[] = $newVariant->id;
            }
        }

        // Hapus varian lama yang tidak ada lagi di dalam form HTML
        $product->variants()->whereNotIn('id', $keepVariantIds)->delete();
    }

    /**
     * Hapus file dari storage publik jika ada.
     */
    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
