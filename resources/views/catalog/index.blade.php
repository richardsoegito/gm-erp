<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gunung Mas Catalog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Link ke File CSS External -->
    <link rel="stylesheet" href="{{ asset('assets/css/catalog-2.css?v=') . time() }}">

    <link rel="icon" href="{{ asset('assets/images/logo_company.jpeg') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <!-- AOS Animation CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"/>
</head>

<body>

    {{-- Background Decoration (Efek Blur Warna-warni di belakang) --}}
    <div class="bg-decoration bg-1"></div>
    <div class="bg-decoration bg-2"></div>

    {{-- Header --}}
    <header class="main-header" data-aos="fade-down">
        <div class="header-wrapper content">
            {{-- Logo --}}
            <a href="{{ route('catalog.index') }}" class="brand">
                <div class="brand-icon">
                    <img src="{{ asset('assets/images/logo_company.jpeg') }}" alt="Logo Company">
                </div>
                <div class="brand-text">
                    <div class="brand-name">Gunung Mas Online Store</div>
                    <div class="brand-caption">Katalog Produk Resmi</div>
                </div>
            </a>

            {{-- Menu / Actions --}}
            <div class="header-actions">
                <button class="theme-toggle" id="theme-toggle" aria-label="Toggle Dark Mode">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-sun">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Filter Section --}}
    <section class="filter-section" data-aos="fade-up" data-aos-delay="100">
        <div class="content">
            <div class="filter-card">
                <form action="{{ route('catalog.index') }}" method="GET">
                    <div class="filter-grid">
                        {{-- Search --}}
                        <div class="filter-search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            {{-- Tambahkan value="request('search')" agar teks yang dicari tidak hilang saat disubmit --}}
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Temukan produk yang Anda butuhkan...">
                        </div>

                        {{-- Category Filter --}}
                        {{-- <div class="filter-select-wrapper">
                            <select name="category" class="filter-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        {{-- Brand Filter (Baru) --}}
                        {{-- <div class="filter-select-wrapper">
                            <select name="brand" class="filter-select">
                                <option value="">Semua Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        {{-- Category Filter --}}
                        <div class="filter-select-wrapper mb-3">
                            <select name="category" id="select-category" placeholder="Cari Kategori...">
                                @if(request('category'))
                                    <option value="{{ request('category') }}" selected>
                                        {{ \App\Models\Master\ProductCategories::find(request('category'))->name ?? 'Kategori Terpilih' }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        {{-- Brand Filter --}}
                        <div class="filter-select-wrapper mb-3">
                            <select name="brand" id="select-brand" placeholder="Cari Brand...">
                                @if(request('brand'))
                                    <option value="{{ request('brand') }}" selected>
                                        {{ \App\Models\Master\ProductBrand::find(request('brand'))->name ?? 'Brand Terpilih' }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        {{-- Button --}}
                        <button type="submit" class="search-btn">Cari Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Products Section --}}
    <section class="product-section">
        <div class="content">
            {{-- Section Header --}}
            <div class="section-heading" data-aos="fade-right" data-aos-delay="200">
                <h2>Katalog Produk</h2>
                <p class="section-subtitle">Koleksi terbaik dari Gunung Mas untuk Anda</p>
            </div>

            {{-- Product Grid --}}
            <div class="product-grid">
                @forelse ($products as $product)
                    <a href="{{ route('catalog.show', $product->slug) }}" class="product-card" data-aos="fade-up">
                        <div class="product-image-wrap">
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}">
                        </div>
                        <div class="product-content">
                            <div class="product-meta">
                                <span class="product-category">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                                <span class="product-brand">{{ $product->brand->name ?? '-' }}</span>
                            </div>
                            <h3 class="product-title">{{ $product->name }}</h3>
                        </div>
                    </a>
                @empty
                    <div class="col-12 text-center">
                        <p>Produk tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            <div class="pagination-wrapper" data-aos="fade-up">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>

            {{-- Pagination --}}
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Init AOS Animation
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });

        // Dark/Light Mode Toggle Logic
        const html = document.documentElement;
        const toggle = document.getElementById('theme-toggle');
        const theme = localStorage.getItem('theme') || 'light';

        html.setAttribute('data-bs-theme', theme);

        toggle.addEventListener('click', () => {
            const current = html.getAttribute('data-bs-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
        });

        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Konfigurasi untuk Kategori
            new TomSelect("#select-category", {
                valueField: 'id',       // ID yang akan dikirim ke form (sebagai value)
                labelField: 'name',     // Nama kolom dari database untuk ditampilkan
                searchField: 'name',    // Berdasarkan apa pencariannya
                preload: 'focus',
                dropdownParent: 'body',
                load: function(query, callback) {
                    // Jika kosong, fetch data awal
                    var url = "{{ route('catalog.search.categories') }}?q=" + encodeURIComponent(query);
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(json => {
                            callback(json);
                        }).catch(()=>{
                            callback();
                        });
                }
            });

            // 2. Konfigurasi untuk Brand
            new TomSelect("#select-brand", {
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                preload: 'focus',
                load: function(query, callback) {
                    var url = "{{ route('catalog.search.brands') }}?q=" + encodeURIComponent(query);
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(json => {
                            callback(json);
                        }).catch(()=>{
                            callback();
                        });
                }
            });

        });
    </script>
</body>
</html>