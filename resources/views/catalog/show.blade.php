<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Gunung Mas</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/css/catalog-2.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/catalog.css') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/images/logo_company.jpeg') }}">

    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"/>

    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-7.2.0/css/all.min.css') }}">
</head>

<body>

    {{-- Background Decoration --}}
    <div class="bg-decoration bg-1"></div>
    <div class="bg-decoration bg-2"></div>

    {{-- Header --}}
    <header class="main-header" data-aos="fade-down">
        <div class="header-wrapper content">
            <a href="{{ url('/') }}" class="brand">
                <div class="brand-icon">
                    <img src="{{ asset('assets/images/logo_company.jpeg') }}" alt="Logo Company">
                </div>
                <div class="brand-text">
                    <div class="brand-name">Gunung Mas Online Store</div>
                    <div class="brand-caption">Katalog Produk Resmi</div>
                </div>
            </a>
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

    {{-- Main Content --}}
    <main class="content" style="padding-top: 30px; padding-bottom: 50px;">
        
        {{-- Breadcrumb Navigasi --}}
        <nav aria-label="breadcrumb" class="breadcrumb-container" data-aos="fade-right">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route("catalog.index") }}"><i class="fa-solid fa-house" style="margin-right: 5px;"></i>Katalog Produk</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ul>
        </nav>

        {{-- Container Produk --}}
        <div class="product-container" data-aos="fade-up">
            
            {{-- Kiri: Galeri Media (Foto & Video) --}}
            <div class="product-gallery">
                <div class="gallery-main" id="main-media-container" onmousemove="zoomImage(event)" onmouseleave="resetZoom()">
                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" id="main-image">
                    
                    <video id="main-video" controls style="display: none; width: 100%; height: 100%;">
                        @if($product->video)
                            <source src="{{ asset('storage/' . $product->video) }}" type="video/mp4">
                        @endif
                    </video>
                </div>
                
                <div class="gallery-thumbnails">
                    {{-- 1. Thumbnail Gambar Utama --}}
                    @if($product->thumbnail)
                    <div class="thumb-item active" onclick="showImage('{{ asset('storage/' . $product->thumbnail) }}', this)">
                        <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="Thumbnail Utama">
                    </div>
                    @endif
                    
                    {{-- 2. Thumbnail Video --}}
                    @if($product->video)
                    <div class="thumb-item" onclick="showVideo(this)" style="position: relative; display:flex; justify-content:center; align-items:center; background:#000;">
                        <video src="{{ asset('storage/' . $product->video) }}" style="opacity: 0.5; object-fit: cover; width: 100%; height: 100%;"></video>
                        <svg style="position: absolute; color: white;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    @endif

                    {{-- 3. Kumpulan Relasi Images --}}
                    @if($product->images && $product->images->count() > 0)
                        @foreach($product->images as $img)
                        <div class="thumb-item" onclick="showImage('{{ asset('storage/' . $img->path) }}', this)">
                            <img src="{{ asset('storage/' . $img->path) }}" alt="Gallery Image">
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Kanan: Informasi Produk --}}
            <div class="product-info">
                <p class="product-title-detail">{{ $product->name }}</p>
                
                <div class="product-meta-grid">
                    <span class="meta-label">Kategori</span>
                    <span class="meta-value"><a href="#">{{ $product->category->name ?? '-' }}</a></span>
                    
                    <span class="meta-label">Brand</span>
                    <span class="meta-value">{{ $product->brand->name ?? '-' }}</span>
                    
                    <span class="meta-label">Satuan Besar</span>
                    <span class="meta-value">{{ $product->largeUnit->name ?? '-' }}</span>
                    
                    <span class="meta-label">Satuan Kecil</span>
                    <span class="meta-value">{{ $product->smallUnit->name ?? '-' }}</span>
                </div>

                {{-- Tabel Varian --}}
                <div class="variant-section">
                    <h3>Varian Produk</h3>
                    
                    @if($product->variants && $product->variants->count() > 0)
                        <table class="table-variants">
                            <thead>
                                <tr>
                                    <th>Ukuran / Size</th>
                                    <th>Jumlah per {{ $product->largeUnit->name ?? '-' }} ({{ $product->smallUnit->name ?? '-' }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                <tr>
                                    <td>{{ $variant->size }}</td>
                                    <td>{{ $variant->box_qty ? number_format($variant->box_qty, 0, ',', '.') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        {{-- Keterangan jika tidak ada varian --}}
                        <p style="color: var(--text-muted); font-size: 0.95rem; font-style: italic; margin-bottom: 25px;">
                            Tidak ada varian untuk produk ini.
                        </p>
                    @endif
                </div>

                {{-- Deskripsi Produk --}}
                <div class="description-section">
                    <h3>Deskripsi Produk</h3>
                    <div class="description-content">{!! $product->description ? nl2br(e($product->description)) : "<p style='color: var(--text-muted); font-size: 0.95rem; font-style: italic; margin-bottom: 25px;'>Tidak ada deskripsi untuk produk ini. </p>" !!}</div>
                </div>

                {{-- Tombol Hubungi --}}
                <div style="margin-top: 30px;">
                    <a href="https://wa.me/08123456789?text=Halo,%20saya%20tertarik%20dengan%20produk%20{{ urlencode($product->name) }}" class="search-btn" style="display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: 600; background-color: #03AC0E; color: white;">
                        <i class="fa-brands fa-whatsapp"></i>
                        Hubungi via WhatsApp
                    </a>
                </div>
            </div>
            
        </div>
    </main>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });

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

        const mainImage = document.getElementById('main-image');
        const mainVideo = document.getElementById('main-video');
        const thumbs = document.querySelectorAll('.thumb-item');

        function updateActiveThumb(clickedThumb) {
            thumbs.forEach(t => t.classList.remove('active'));
            clickedThumb.classList.add('active');
        }

        function showImage(src, element) {
            mainVideo.style.display = 'none';
            mainVideo.pause(); 
            mainImage.style.display = 'block';
            mainImage.src = src;
            
            resetZoom();
            
            updateActiveThumb(element);
        }

        function showVideo(element) {
            mainImage.style.display = 'none';
            mainVideo.style.display = 'block';
            mainVideo.play(); 
            updateActiveThumb(element);
        }

        // --- Logika Zoom ---
        function zoomImage(e) {
            if (mainImage.style.display === 'none') return;

            const container = document.getElementById('main-media-container');
            const rect = container.getBoundingClientRect();
            
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;

            mainImage.style.transformOrigin = `${x}% ${y}%`;
            mainImage.style.transform = 'scale(2)';
        }

        function resetZoom() {
            mainImage.style.transformOrigin = 'center center';
            mainImage.style.transform = 'scale(1)';
        }
    </script>
</body>
</html>