{{-- resources/views/catalog/partials/product_list.blade.php --}}
@foreach ($products as $product)
    <a href="{{ route('catalog.show', $product->slug) }}" class="product-card" data-aos="fade-up">
        <div class="product-image-wrap">
            <img 
                src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('images/no_image_available.jpg') }}"
                alt="{{ $product->name }}"
                loading="lazy"
                onerror="this.src='{{ asset('images/no_image_available.jpg') }}'">
        </div>
        <div class="product-content">
            <div class="product-meta">
                <span class="product-category">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                <span class="product-brand">{{ $product->brand->name ?? '-' }}</span>
            </div>
            <h3 class="product-title">{{ $product->name }}</h3>
        </div>
    </a>
@endforeach