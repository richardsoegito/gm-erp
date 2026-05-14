<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Katalog Produk</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    {{-- Navbar --}}
    <header class="navbar navbar-expand-md navbar-light d-print-none shadow-sm">

        <div class="container">

            {{-- Company Name --}}
            <h1 class="navbar-brand navbar-brand-autodark pe-0 pe-md-3 m-0">
                GUNUNG MAS PRODUCT CATALOG
            </h1>

        </div>

    </header>

    {{-- Main Content --}}
    <div class="page-wrapper">

        <div class="page-body py-5">

            <div class="container">
                {{-- Search & Filter --}}
                <div class="card mb-5">

                    <div class="card-body">

                        <form action="" method="GET">

                            <div class="row g-3">

                                {{-- Search --}}
                                <div class="col-md-8">

                                    <div class="input-icon">

                                        <span class="input-icon-addon">

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="icon"
                                                 width="24"
                                                 height="24"
                                                 viewBox="0 0 24 24"
                                                 stroke-width="2"
                                                 stroke="currentColor"
                                                 fill="none"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round">

                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="10" cy="10" r="7"/>
                                                <line x1="21" y1="21" x2="15" y2="15"/>

                                            </svg>

                                        </span>

                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Cari produk..."
                                            name="search"
                                        >

                                    </div>

                                </div>

                                {{-- Category --}}
                                <div class="col-md-3">

                                    <select class="form-select" name="category">

                                        <option value="">
                                            Semua Kategori
                                        </option>

                                        <option value="Elektronik">
                                            Elektronik
                                        </option>

                                        <option value="Fashion">
                                            Fashion
                                        </option>

                                        <option value="Furniture">
                                            Furniture
                                        </option>

                                        <option value="Makanan">
                                            Makanan
                                        </option>

                                    </select>

                                </div>

                                {{-- Button --}}
                                <div class="col-md-1">

                                    <button class="btn btn-primary w-100">
                                        Cari
                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                {{-- Product Grid --}}
                <div class="row row-cards">

                    @for ($i = 1; $i <= 12; $i++)

                        <div class="col-sm-6 col-lg-3">

                            <div class="card h-100 shadow-sm">

                                {{-- Product Image --}}
                                <a href="#">

                                    <img
                                        src="https://picsum.photos/500/350?random={{ $i }}"
                                        class="card-img-top"
                                        alt="Product"
                                    >

                                </a>

                                {{-- Product Content --}}
                                <div class="card-body d-flex flex-column">

                                    <div class="mb-2">

                                        <span class="badge bg-blue-lt">
                                            Elektronik
                                        </span>

                                    </div>

                                    <h3 class="card-title">

                                        <a href="#" class="text-reset">
                                            Produk {{ $i }}
                                        </a>

                                    </h3>

                                    <div class="text-secondary mb-4">

                                        Deskripsi singkat produk untuk tampilan katalog perusahaan.

                                    </div>

                                    <div class="mt-auto">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div class="h2 mb-0 text-primary">
                                                Rp 250.000
                                            </div>

                                            <a href="#" class="btn btn-outline-primary btn-sm">
                                                Detail
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endfor

                </div>

                {{-- Pagination --}}
                <div class="mt-5 d-flex justify-content-center">

                    <ul class="pagination">

                        <li class="page-item disabled">
                            <a class="page-link" href="#">
                                Previous
                            </a>
                        </li>

                        <li class="page-item active">
                            <a class="page-link" href="#">
                                1
                            </a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#">
                                2
                            </a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#">
                                3
                            </a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#">
                                Next
                            </a>
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</body>
</html>