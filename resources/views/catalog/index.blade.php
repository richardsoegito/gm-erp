<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gunung Mas Catalog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/css/catalog.css?v=') . time() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

    {{-- Background Decoration --}}
    <div class="bg-decoration bg-1"></div>
    <div class="bg-decoration bg-2"></div>

    {{-- Header --}}
    <header class="main-header">
        <div class="header-wrapper">

            {{-- Logo --}}
            <a href="#" class="brand">

                <div class="brand-icon">
                    GM
                </div>

                <div>

                    <div class="brand-name">
                        Gunung Mas
                    </div>

                    <div class="brand-caption">
                        Product Catalog
                    </div>

                </div>

            </a>

            {{-- Menu --}}
            <div class="header-actions">

                <button class="theme-toggle" id="theme-toggle">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="22"
                         height="22"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round">

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

    {{-- Filter --}}
    <section class="filter-section">

        <div class="content">

            <div class="filter-card">

                <form action="" method="GET">

                    <div class="filter-grid">

                        {{-- Search --}}
                        <div class="filter-search">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="22"
                                 height="22"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">

                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>

                            </svg>

                            <input
                                type="text"
                                name="search"
                                placeholder="Cari produk..."
                            >

                        </div>

                        {{-- Category --}}
                        <select name="category">

                            <option value="">
                                Semua Kategori
                            </option>

                            <option>
                                Elektronik
                            </option>

                            <option>
                                Fashion
                            </option>

                            <option>
                                Furniture
                            </option>

                            <option>
                                Makanan
                            </option>

                        </select>

                        {{-- Sort --}}
                        <select>

                            <option>
                                Terbaru
                            </option>

                            <option>
                                Harga Tertinggi
                            </option>

                            <option>
                                Harga Terendah
                            </option>

                        </select>

                        {{-- Button --}}
                        <button type="submit" class="search-btn">
                            Cari
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>

    {{-- Products --}}
    <section class="product-section">

        <div class="content">

            {{-- Section Header --}}
            <div class="section-heading">

                <div>

                    <h2>
                        Featured Products
                    </h2>
                </div>

                {{-- <a href="#" class="view-all-btn">
                    Lihat Semua
                </a> --}}

            </div>

            {{-- Product Grid --}}
            <div class="product-grid">

                @for ($i = 1; $i <= 12; $i++)

                    <div class="product-card">

                        {{-- Image --}}
                        <div class="product-image-wrap">

                            <img
                                src="https://picsum.photos/700/500?random={{ $i }}"
                                alt="Product"
                            >

                        </div>

                        {{-- Content --}}
                        <div class="product-content">

                            <div class="product-category">
                                Elektronik
                            </div>
                            <div class="product-brand">
                                Huben
                            </div>

                            <h3 class="product-title">
                                Produk Premium {{ $i }}
                            </h3>

{{-- 
                            <p class="product-description">

                                Produk berkualitas tinggi dengan desain modern
                                dan performa terbaik untuk kebutuhan profesional.

                            </p> --}}

                            {{-- <div class="product-footer">

                                <a href="#" class="detail-btn">
                                    Detail
                                </a>

                            </div> --}}

                        </div>

                    </div>

                @endfor

            </div>

            {{-- Pagination --}}
            <div class="pagination-wrapper">

                <button>
                    Previous
                </button>

                <button class="active">
                    1
                </button>

                <button>
                    2
                </button>

                <button>
                    3
                </button>

                <button>
                    Next
                </button>

            </div>

        </div>

    </section>

    {{-- Theme Script --}}
    <script>

        const html = document.documentElement;
        const toggle = document.getElementById('theme-toggle');

        const theme = localStorage.getItem('theme');

        if (theme) {
            html.setAttribute('data-bs-theme', theme);
        }

        toggle.addEventListener('click', () => {

            const current = html.getAttribute('data-bs-theme');

            const next = current === 'dark'
                ? 'light'
                : 'dark';

            html.setAttribute('data-bs-theme', next);

            localStorage.setItem('theme', next);

        });

    </script>

</body>
</html>