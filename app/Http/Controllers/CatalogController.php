<?php

namespace App\Http\Controllers;

use App\Models\Master\Product;
use App\Models\Master\ProductCategories;
use App\Models\Master\ProductBrand;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Mulai query produk dengan relasi agar tidak terjadi N+1 problem
    //     $query = Product::query()
    //         ->with(['brand', 'category', 'variants', 'images'])
    //         ->where('status', true); // Hanya tampilkan produk aktif

    //     // 1. Filter Pencarian Global (Search)
    //     if ($request->filled('search')) {
    //         $search = $request->search; // Definisikan di sini khusus scope ini

    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'like', '%' . $search . '%')
    //             ->orWhereHas('brand', function ($brand) use ($search) {
    //                 $brand->where('name', 'like', '%' . $search . '%');
    //             })
    //             ->orWhereHas('category', function ($category) use ($search) {
    //                 $category->where('name', 'like', '%' . $search . '%');
    //             });
    //         });
    //     }

    //     // 2. Filter Kategori
    //     if ($request->filled('category')) {
    //         // Karena TomSelect mengirimkan 'id', langsung tembak foreign key-nya
    //         // Pastikan nama kolom disesuaikan, misalnya 'category_id'
    //         $query->where('category_id', $request->category); 
    //     }

    //     // 3. Filter Brand
    //     if ($request->filled('brand')) {
    //         // HARUS pakai ->where() agar tidak merusak filter status=true
    //         // Sama seperti kategori, langsung tembak foreign key-nya
    //         $query->where('brand_id', $request->brand);
    //     }

    //     // 4. Logika Sortir / Pengurutan
    //     $sort = $request->query('sort', 'newest'); // 'newest' adalah default jika kosong

    //     switch ($sort) {
    //         case 'a-z':
    //             $query->orderBy('name', 'asc');
    //             break;
    //         case 'z-a':
    //             $query->orderBy('name', 'desc');
    //             break;
    //         case 'newest':
    //         default:
    //             // Urutkan berdasarkan data terbaru (ID terbesar atau created_at terbaru)
    //             $query->latest(); 
    //             break;
    //     }

    //     // Ambil data dengan pagination dan pertahankan parameter URL (withQueryString)
    //     $products = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

    //     // Ambil data kategori dan brand untuk dropdown filter
    //     $categories = ProductCategories::where('status', 1)->orderBy('name', 'asc')->get();
    //     $brands = ProductBrand::where('status', 1)->orderBy('name', 'asc')->get(); 

    //     return view('catalog.index', compact('products', 'categories', 'brands'));
    // }

    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['brand', 'category', 'variants', 'images'])
            ->where('status', true);

        // ... (Filter Pencarian, Kategori, Brand, Sortir TETAP SAMA seperti kode Anda) ...
        // 1. Filter Pencarian Global (Search)
        if ($request->filled('search')) {
            $search = $request->search; // Definisikan di sini khusus scope ini

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

        // 2. Filter Kategori
        if ($request->filled('category')) {
            // Karena TomSelect mengirimkan 'id', langsung tembak foreign key-nya
            // Pastikan nama kolom disesuaikan, misalnya 'category_id'
            $query->where('category_id', $request->category); 
        }

        // 3. Filter Brand
        if ($request->filled('brand')) {
            // HARUS pakai ->where() agar tidak merusak filter status=true
            // Sama seperti kategori, langsung tembak foreign key-nya
            $query->where('brand_id', $request->brand);
        }

        // 4. Logika Sortir / Pengurutan
        $sort = $request->query('sort', 'newest'); // 'newest' adalah default jika kosong

        switch ($sort) {
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                // Urutkan berdasarkan data terbaru (ID terbesar atau created_at terbaru)
                $query->latest(); 
                break;
        }

        // Ubah jadi 20 sesuai permintaan
        $products = $query->paginate(20)->withQueryString();

        // JIKA REQUEST BERASAL DARI TOMBOL LOAD MORE (AJAX)
        if ($request->ajax()) {
            $view = view('catalog.partials.product_list', compact('products'))->render();
            return response()->json([
                'html' => $view,
                'next_page_url' => $products->nextPageUrl() // Ambil URL halaman berikutnya
            ]);
        }

        $categories = ProductCategories::where('status', 1)->orderBy('name', 'asc')->get();
        $brands = ProductBrand::where('status', 1)->orderBy('name', 'asc')->get(); 

        return view('catalog.index', compact('products', 'categories', 'brands'));
    }

    public function searchCategories(Request $request)
    {
        $search = $request->get('q');
        // Cari data yang namanya mengandung huruf yang diketik, batasi 20 agar sangat ringan
        $categories = ProductCategories::select('id', 'name')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->limit(20) 
            ->get();

        return response()->json($categories);
    }

    public function searchBrands(Request $request)
    {
        $search = $request->get('q');
        $brands = ProductBrand::select('id', 'name')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json($brands);
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
