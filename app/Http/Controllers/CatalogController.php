<?php

namespace App\Http\Controllers;

use App\Models\Master\Product;
use App\Models\Master\ProductCategories;
use App\Models\Master\ProductBrand;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query produk dengan relasi agar tidak terjadi N+1 problem
        $query = Product::query()
            ->with(['brand', 'category', 'variants', 'images'])
            ->where('status', true); // Hanya tampilkan produk aktif

        // Filter Pencarian (Search)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhereHas('brand', function ($brand) use ($search) {
                    $brand->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('category', function ($category) use ($search) {
                    $category->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter Brand (Tambahan Baru)
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Ambil data dengan pagination dan pertahankan parameter URL (withQueryString)
        $products = $query->latest()->paginate(12)->withQueryString();

        // Ambil data kategori dan brand untuk dropdown filter
        // Disarankan pakai orderBy agar urutan di dropdown rapi sesuai abjad
        $categories = ProductCategories::where('status', 1)->orderBy('name', 'asc')->get();
        $brands = ProductBrand::where('status', 1)->orderBy('name', 'asc')->get(); 

        return view('catalog.index', compact('products', 'categories', 'brands'));
    }

    public function show($slug)
    {
        // Mengambil produk berdasarkan slug
        $product = Product::where('slug', $slug)->firstOrFail();
        
        // Mengambil produk lain sebagai saran (opsional)
        $relatedProducts = Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->limit(4)
                                ->get();

        return view('catalog.show', compact('product', 'relatedProducts'));
    }
}
