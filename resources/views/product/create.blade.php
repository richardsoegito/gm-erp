@extends('layouts.app')

@section('title', $editing ? 'Ubah Produk' : 'Tambah Produk')
@section('styles')
    <style>
        /* Warna latar belakang untuk baris ganjil (1, 3, 5, dst) */
        #variant-wrapper .variant-item:nth-child(odd) {
            background-color: #ffffff; 
        }

        #variant-wrapper .variant-item:nth-child(even) {
            background-color: #f1f3f5; /* Warna abu-abu sangat muda (standar Bootstrap) */
        }
    </style>
@endsection
@section('content')

<form id="product-form" action="{{ $editing ? route('master.product.update', $product) : route('master.product.store') }}" method="POST" enctype="multipart/form-data">
@csrf
@if($editing)
    @method('PUT')
@endif
<div class="row">

    {{-- ========================================================= --}}
    {{-- INFORMASI PRODUK --}}
    {{-- ========================================================= --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">Informasi Produk</div>

            <div class="card-body">
                <div class="row">
                    {{-- Nama Produk --}}
                    <div class="col-md-6 mb-3">
                        <label>Nama Produk<span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ $editing ? old('name', $product->name) : old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- SKU --}}
                    <div class="col-md-3 mb-3">
                        <label>SKU</label>
                        <input type="text" name="sku" class="form-control"
                               value="{{ $editing ? old('sku', $product->sku) : old('sku') }}">
                    </div>

                    {{-- Slug --}}
                    <div class="col-md-3 mb-3 d-none">
                        <label>Slug</label>
                        <input type="text" id="slug" name="slug" class="form-control"
                               value="{{ $editing ? old('slug', $product->slug) : old('slug') }}" readonly>
                    </div>

                    {{-- Merek / Brand --}}
                    <div class="col-md-3 mb-3">
                        <label>Merek<span class="text-danger">*</span></label>
                        <select name="brand_id" id="brand_id">
                            <option value="">Pilih Merek</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ ($editing ? old('brand_id', $product->brand_id) : old('brand_id')) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="col-md-3 mb-3">
                        <label>Kategori<span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ ($editing ? old('category_id', $product->category_id) : old('category_id')) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Satuan --}}
                    <div class="col-md-3 mb-3">
                        <label>Satuan<span class="text-danger">*</span></label>
                        <select name="large_unit_id" id="large_unit_id">
                            <option value="">Pilih Satuan</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" data-decimal="{{ $unit->allow_decimal ? 1 : 0 }}"
                                    {{ ($editing ? old('large_unit_id', $product->large_unit_id) : old('large_unit_id')) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('large_unit_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Satuan Kecil --}}
                    <div class="col-md-3 mb-3">
                        <label>Satuan Kecil</label>
                        <select name="small_unit_id" id="small_unit_id">
                            <option value="">Pilih Satuan Kecil</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" data-decimal="{{ $unit->allow_decimal ? 1 : 0 }}"
                                    {{ ($editing ? old('small_unit_id', $product->small_unit_id) : old('small_unit_id')) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('small_unit_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="colly_description">Keterangan Isian Colly</label>
                        <input type="text" name="colly_description" id="colly_description" class="form-control  @error('colly_description') is-invalid @enderror" value="{{ $editing ? old('colly_description', $product->colly_description) : old('colly_description') }}" placeholder="1 Colly 10 Kotak">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12 mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror">{{ $editing ? old('description', $product->description) : old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="status" class="form-check-input" {{ old('status', $editing ? $product->status : true) ? 'checked' : '' }}>
                            <label class="form-check-label">Aktif</label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- VARIAN --}}
    {{-- ========================================================= --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Varian Produk</span>
            </div>

            <div class="card-body">

                <div id="variant-wrapper">
                    @if($editing && $product->variants->count())
                        @foreach($product->variants as $v)
                            <div class="border rounded p-3 mb-3 variant-item bg-light">
                                <div class="row g-3 align-items-end">
                                    
                                    {{-- HIDDEN INPUT UNTUK ID VARIAN LAMA --}}
                                    <input type="hidden" name="variant_ids[]" value="{{ $v->id }}">

                                    {{-- Ukuran / Varian --}}
                                    <div class="col-md-4 name-column">
                                        <label class="form-label fw-semibold mb-1">Nama Varian</label>
                                        <input type="text" name="size[]" class="form-control form-control-sm"
                                            value="{{ old('size.' . $loop->index, $v->size) }}" placeholder="Contoh: Merah, XL, dll">
                                    </div>

                                    {{-- Isi Colly --}}
                                    <div class="col-md-3 large-unit-column">
                                        <label class="form-label fw-semibold mb-1">Isi Colly</label>
                                        {{-- flex-nowrap ditambahkan di sini --}}
                                        <div class="input-group input-group-sm flex-nowrap">
                                            <input type="text" name="large_qty[]" class="form-control autonumeric-qty"        value="{{ old('large_qty.' . $loop->index, number_format($v->large_unit_qty, 2, ',', '.')) }}">
                                            <span class="input-group-text large-unit-name bg-white text-secondary">Satuan</span>
                                        </div>
                                    </div>

                                    {{-- Isi Pack / Box --}}
                                    <div class="col-md-3 small-unit-column d-none">
                                        <label class="form-label fw-semibold mb-1">Isi Pack / Box</label>
                                        {{-- flex-nowrap ditambahkan di sini --}}
                                        <div class="input-group input-group-sm flex-nowrap">
                                            <input type="text" name="small_qty[]" class="form-control autonumeric-qty"        value="{{ old('small_qty.' . $loop->index, number_format($v->small_unit_qty, 2, ',', '.')) }}">
                                            <span class="input-group-text small-unit-name bg-white text-secondary">Satuan</span>
                                        </div>
                                    </div>

                                    {{-- Tombol Hapus --}}
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-variant">
                                            &times; Hapus
                                        </button>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- default row --}}
                        <div class="border rounded p-3 mb-3 variant-item bg-light">
                            <div class="row g-3 align-items-end">
                                
                                {{-- HIDDEN INPUT KOSONG UNTUK VARIAN BARU --}}
                                <input type="hidden" name="variant_ids[]" value="">

                                <div class="col-md-4 name-column">
                                    <label class="form-label fw-semibold mb-1">Nama Varian</label>
                                    <input type="text" name="size[]" class="form-control form-control-sm"
                                        placeholder="Contoh: Merah, XL, dll" value="{{ old('size.0') }}">
                                </div>

                                <div class="col-md-3 large-unit-column">
                                    <label class="form-label fw-semibold mb-1">Isi Colly</label>
                                    <div class="input-group input-group-sm flex-nowrap">
                                        <input type="text" name="large_qty[]" class="form-control autonumeric-qty" value="{{ old('large_qty.0') }}">
                                        <span class="input-group-text large-unit-name bg-white text-secondary">Satuan</span>
                                    </div>
                                </div>

                                <div class="col-md-3 small-unit-column d-none">
                                    <label class="form-label fw-semibold mb-1">Isi Pack / Box</label>
                                    <div class="input-group input-group-sm flex-nowrap">
                                        <input type="text" name="small_qty[]" class="form-control autonumeric-qty" value="{{ old('small_qty.0') }}">
                                        <span class="input-group-text small-unit-name bg-white text-secondary">Satuan</span>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-variant">
                                        &times; Hapus
                                    </button>
                                </div>
                                
                            </div>
                        </div>
                    @endif
                </div>

                <div class="button-tambah d-flex justify-content-end">
                    <button type="button" id="add-variant" class="btn btn-sm btn-primary">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- SEO --}}
    {{-- ========================================================= --}}
    {{-- <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">Informasi SEO</div>
            <div class="card-body">
                <div class="mb-3">
                    <label>Meta Title</label>
                    <input type="text" name="meta_title"
                           class="form-control @error('meta_title') is-invalid @enderror"
                           value="{{ $editing ? old('meta_title', $product->meta_title) : old('meta_title') }}">
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Meta Description</label>
                    <textarea name="meta_description" rows="4"
                              class="form-control @error('meta_description') is-invalid @enderror">{{ $editing ? old('meta_description', $product->meta_description) : old('meta_description') }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div> --}}

    {{-- ========================================================= --}}
    {{-- THUMBNAIL --}}
    {{-- ========================================================= --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">Thumbnail Produk</div>

            <div class="card-body">

            {{-- Preview area --}}
            <div id="thumbnail-preview-wrapper"
                class="border rounded d-flex align-items-center justify-content-center bg-light mb-3"
                {{-- Tambahkan cursor: pointer dan fungsi onclick di sini --}}
                style="width:100%; height:220px; overflow:hidden; cursor: pointer;"
                onclick="document.getElementById('thumbnail-input').click()">

                <img id="thumbnail-preview"
                    @if($editing && $product->thumbnail)
                        src="{{ asset('storage/' . $product->thumbnail) }}"
                        class=""
                    @else
                        class="d-none"
                    @endif
                    style="max-width:100%; max-height:100%; object-fit:contain;"
                    alt="Pratinjau Thumbnail">

                <div id="thumbnail-placeholder"
                    class="text-center text-muted {{ $editing && $product->thumbnail ? 'd-none' : '' }}">
                    <div style="font-size:2rem;">🖼️</div>
                    <div class="small">Klik untuk memilih thumbnail</div>
                </div>
            </div>

            {{-- Tombol hapus thumbnail lama --}}
            @if($editing && $product->thumbnail)
                <div class="form-check mt-2">
                    <input type="checkbox" name="remove_thumbnail" value="1"
                        class="form-check-input" id="remove_thumbnail">
                    <label class="form-check-label text-danger small" for="remove_thumbnail">
                        Hapus thumbnail saat ini
                    </label>
                </div>
            @endif

                <input type="file"
                       id="thumbnail-input"
                       name="thumbnail"
                       class="form-control @error('thumbnail') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/webp">
                @error('thumbnail')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">JPG, PNG, atau WEBP. Maks 5 MB.</div>

            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- VIDEO --}}
    {{-- ========================================================= --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">Video Produk</div>

            <div class="card-body">

                {{-- Preview area --}}
                <div id="video-preview-wrapper"
                    class="border rounded d-flex align-items-center justify-content-center bg-light mb-3"
                    {{-- Tambahkan cursor: pointer dan fungsi onclick pembuka file di sini --}}
                    style="width:100%; height:220px; overflow:hidden; cursor: pointer;"
                    onclick="document.getElementById('video-input').click()">

                    <video id="video-preview"
                        @if($editing && $product->video)
                            src="{{ asset('storage/' . $product->video) }}"
                        @else
                            class="d-none"
                        @endif
                        style="max-width:100%; max-height:100%; object-fit:contain;"
                        controls
                        {{-- Hentikan event klik agar tombol play video tidak memicu pilih file --}}
                        onclick="event.stopPropagation()">
                    </video>

                    <div id="video-placeholder"
                        class="text-center text-muted {{ $editing && $product->video ? 'd-none' : '' }}">
                        <div style="font-size:2rem;">🎬</div>
                        {{-- Sesuaikan teks placeholder --}}
                        <div class="small">Klik untuk memilih video</div>
                    </div>
                </div>

                @if($editing && $product->video)
                    <div class="form-check mt-2">
                        <input type="checkbox" name="remove_video" value="1"
                            class="form-check-input" id="remove_video">
                        <label class="form-check-label text-danger small" for="remove_video">
                            Hapus video saat ini
                        </label>
                    </div>
                @endif

                <input type="file"
                       id="video-input"
                       name="video"
                       class="form-control @error('video') is-invalid @enderror"
                       accept="video/mp4,video/webm,video/ogg">
                @error('video')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">MP4, WEBM, atau OGG. Maks 50 MB.</div>

            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- GAMBAR PRODUK --}}
    {{-- ========================================================= --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Gambar Produk Tambahan</span>
            </div>

            <div class="card-body">

                @error('images')
                    <div class="alert alert-danger py-2 mb-3">{{ $message }}</div>
                @enderror

                <div id="image-wrapper">
                    {{-- GAMBAR EXISTING (DB)  --}}
                    @if($editing && $product->images->count())
                        @foreach($product->images->sortBy('sort_order') as $img)
                            <div class="border rounded p-3 mb-3 image-item" id="existing-image-{{ $img->id }}">
                                <input type="hidden" name="existing_image_ids[]" value="{{ $img->id }}">

                                {{-- TAMPILAN GAMBAR LAMA --}}
                                <div class="mb-3 pb-3 border-bottom">
                                    <span class="badge bg-secondary mb-2">Gambar Saat Ini</span>
                                    <div>
                                        <div class="border rounded bg-light" style="width:150px; height:100px; overflow:hidden;">
                                            <img src="{{ asset('storage/' . $img->path) }}" 
                                                 style="width:100%; height:100%; object-fit:cover;" alt="Existing">
                                        </div>
                                    </div>
                                </div>

                                {{-- FORM REPLACEMENT --}}
                                <div class="row align-items-start g-3">
                                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                                        <div class="image-preview-wrapper border rounded d-flex align-items-center justify-content-center bg-light"
                                            style="width:150px; height:100px; overflow:hidden;">
                                            <img class="image-preview d-none" style="width:100%; height:100%; object-fit:cover;" alt="Preview">
                                            <span class="image-preview-placeholder text-muted small text-center px-1">Tanpa Gambar</span>
                                        </div>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label mb-1">Nama / Label Gambar</label>
                                                <input type="text" name="existing_image_labels[]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $img->label }}">
                                            </div>
                                            
                                            <div class="col-md-8">
                                                <label class="form-label mb-1">Ganti Gambar (Opsional)</label>
                                                <input type="file" name="replace_images[{{ $img->id }}]" 
                                                    class="form-control form-control-sm image-file-input" 
                                                    accept="image/*">
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <label class="form-label mb-1">Urutan</label>
                                                <input type="number" name="existing_sort_orders[]"
                                                    value="{{ $img->sort_order }}" min="0"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto d-flex justify-content-end">
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-danger mt-4 btn-remove-existing-image" 
                                            data-id="{{ $img->id }}">
                                            &times; Hapus
                                        </button>
                                        <input type="checkbox" name="deleted_image_ids[]" value="{{ $img->id }}"
                                            class="deleted-image-checkbox d-none">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- GAMBAR BARU (upload)  --}}
                    @if(!$editing)
                        <div class="border rounded p-3 mb-3 image-item">
                            <div class="row align-items-start g-3">
                                <div class="col-md-2 d-flex align-items-center justify-content-center">
                                    <div class="image-preview-wrapper border rounded d-flex align-items-center justify-content-center bg-light"
                                        style="width:150px; height:100px; overflow:hidden;">
                                        <img class="image-preview d-none"
                                            style="width:100%; height:100%; object-fit:cover;" alt="Preview">
                                        <span class="image-preview-placeholder text-muted small text-center px-1">Tanpa Gambar</span>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label mb-1">Nama / Label Gambar</label>
                                            <input type="text" name="image_labels[]"
                                                class="form-control form-control-sm"
                                                placeholder="Contoh: Tampak Depan, Detail, dll.">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label mb-1">File Gambar</label>
                                            <input type="file" name="images[]"
                                                class="form-control form-control-sm image-file-input"
                                                accept="image/*">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-1">Urutan</label>
                                            <input type="number" name="sort_order[]" value="0" min="0"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-image mt-4">&times; Hapus</button>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="button-tambah d-flex justify-content-end">
                    <button type="button" id="add-image" class="btn btn-sm btn-primary">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- AKSI SIMPAN --}}
    {{-- ========================================================= --}}
    <div class="col-12">
        <div class="card">
            <div class="card-footer d-flex justify-content-between">

                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    Kembali
                </a>

                <button type="submit" id="submit-btn" class="btn btn-primary">
                    <span class="btn-text">{{ $editing ? 'Perbarui Produk' : 'Simpan Produk' }}</span>
                    <span class="btn-loading d-none"><i class="fa-solid fa-spinner fa-spin"></i> Memproses...</span>
                </button>

            </div>
        </div>
    </div>

</div>

</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.8.1"></script>
<script>    
    document.addEventListener('DOMContentLoaded', () => {
        function initAutoNumeric() {
            const largeSelect = document.getElementById('large_unit_id');
            const smallSelect = document.getElementById('small_unit_id');

            const largeAllowDecimal =
                largeSelect?.options[largeSelect.selectedIndex]
                    ?.dataset.decimal === '1';

            const smallAllowDecimal =
                smallSelect?.options[smallSelect.selectedIndex]
                    ?.dataset.decimal === '1';

            document.querySelectorAll('input[name="large_qty[]"]')
                .forEach(input => {

                    if (!AutoNumeric.getAutoNumericElement(input)) {

                        new AutoNumeric(input, {
                            digitGroupSeparator: '.',
                            decimalCharacter: ',',
                            decimalPlaces: largeAllowDecimal ? 2 : 0,
                            decimalPlacesRawValue: 4,
                            minimumValue: '0',
                            unformatOnSubmit: true,
                            modifyValueOnWheel: false
                        });

                    }

                });

            document.querySelectorAll('input[name="small_qty[]"]')
                .forEach(input => {

                    if (!AutoNumeric.getAutoNumericElement(input)) {

                        new AutoNumeric(input, {
                            digitGroupSeparator: '.',
                            decimalCharacter: ',',
                            decimalPlaces: smallAllowDecimal ? 2 : 0,
                            decimalPlacesRawValue: 4,
                            minimumValue: '0',
                            unformatOnSubmit: true,
                            modifyValueOnWheel: false
                        });

                    }

                });
        }

        // AutoNumeric
        initAutoNumeric()

        /*
        |--------------------------------------------------------------------------
        | TOM SELECT
        |--------------------------------------------------------------------------
        */
        const selects = [
            '#brand_id',
            '#category_id',
            '#large_unit_id',
            '#small_unit_id'
        ];

        selects.forEach(selector => {
            const el = document.querySelector(selector);

            if (el && !el.tomselect) {
                new TomSelect(el);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | DYNAMIC VARIANT LABELS (Satuan & Kecil)
        |--------------------------------------------------------------------------
        */
        function updateVariantColumns() {
            const largeSelect = document.getElementById('large_unit_id');
            const smallSelect = document.getElementById('small_unit_id');

            const largeUnit =
                largeSelect && largeSelect.selectedIndex > 0
                    ? largeSelect.options[largeSelect.selectedIndex].text
                    : '';

            const smallUnit =
                smallSelect && smallSelect.selectedIndex > 0
                    ? smallSelect.options[smallSelect.selectedIndex].text
                    : '';

            // 1. Update Satuan Besar (Sembunyikan kotak span jika kosong)
            document.querySelectorAll('.large-unit-name').forEach(el => {
                if (largeUnit) {
                    el.textContent = largeUnit;
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });

            // 2. Update Satuan Kecil & Sesuaikan Lebar Grid (Biar tetap sebaris penuh)
            document.querySelectorAll('.small-unit-column').forEach(col => {
                const smallUnitSpan = col.querySelector('.small-unit-name');
                const row = col.closest('.row');
                
                // MENGGUNAKAN CLASS SELECTOR (Lebih aman daripada index children)
                const nameCol = row.querySelector('.name-column'); 
                const largeCol = row.querySelector('.large-unit-column');

                // Proteksi jika elemen tidak ditemukan agar script tidak error
                if (!nameCol || !largeCol) return;

                if (smallUnit) {
                    // JIKA ADA SATUAN KECIL: Tampilkan dan set grid ke (4 - 3 - 3 - 2)
                    col.classList.remove('d-none');
                    if (smallUnitSpan) {
                        smallUnitSpan.textContent = smallUnit;
                    }

                    nameCol.classList.remove('col-md-6');
                    nameCol.classList.add('col-md-4');

                    largeCol.classList.remove('col-md-4');
                    largeCol.classList.add('col-md-3');
                } else {
                    // JIKA TIDAK ADA SATUAN KECIL: Sembunyikan dan set grid ke (6 - 4 - 2) agar ruang penuh
                    col.classList.add('d-none');

                    nameCol.classList.remove('col-md-4');
                    nameCol.classList.add('col-md-6');

                    largeCol.classList.remove('col-md-3');
                    largeCol.classList.add('col-md-4');
                }
            });
        }

        function updateDecimalMode() {
            const largeSelect = document.getElementById('large_unit_id');
            const smallSelect = document.getElementById('small_unit_id');

            const largeAllowDecimal =
                largeSelect?.options[largeSelect.selectedIndex]
                    ?.dataset.decimal === '1';

            const smallAllowDecimal =
                smallSelect?.options[smallSelect.selectedIndex]
                    ?.dataset.decimal === '1';

            document.querySelectorAll('input[name="large_qty[]"]')
                .forEach(input => {

                    const an = AutoNumeric.getAutoNumericElement(input);

                    if (!an) return;

                    const rawValue = parseFloat(an.getNumericString() || 0);

                    an.update({
                        decimalPlaces: largeAllowDecimal ? 2 : 0
                    });

                    if (!largeAllowDecimal) {
                        an.set(Math.floor(rawValue));
                    }

                });

            document.querySelectorAll('input[name="small_qty[]"]')
                .forEach(input => {

                    const an = AutoNumeric.getAutoNumericElement(input);

                    if (!an) return;

                    const rawValue = parseFloat(an.getNumericString() || 0);

                    an.update({
                        decimalPlaces: smallAllowDecimal ? 2 : 0
                    });

                    if (!smallAllowDecimal) {
                        an.set(Math.floor(rawValue));
                    }

                });
        }

        updateVariantColumns();
        updateDecimalMode();


        const largeSelectElem = document.getElementById('large_unit_id');
        const smallSelectElem = document.getElementById('small_unit_id');
        
        largeSelectElem?.addEventListener('change', () => {
            updateVariantColumns();
            updateDecimalMode();
        });
        
        smallSelectElem?.addEventListener('change', () => {
            updateVariantColumns();
            updateDecimalMode();
        });

        /*
        |--------------------------------------------------------------------------
        | SLUG GENERATOR
        |--------------------------------------------------------------------------
        */
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        if (nameInput && slugInput) {
            nameInput.addEventListener('input', function () {
                slugInput.value = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            });
        }


        /*
        |--------------------------------------------------------------------------
        | PREVIEW THUMBNAIL
        |--------------------------------------------------------------------------
        */
        const thumbnailInput = document.getElementById('thumbnail-input');

        if (thumbnailInput) {
            thumbnailInput.addEventListener('change', function () {
                const preview     = document.getElementById('thumbnail-preview');
                const placeholder = document.getElementById('thumbnail-placeholder');
                const file        = this.files[0];

                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                        placeholder.classList.add('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '';
                    preview.classList.add('d-none');
                    placeholder.classList.remove('d-none');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PREVIEW VIDEO
        |--------------------------------------------------------------------------
        */
        const videoInput = document.getElementById('video-input');

        if (videoInput) {
            videoInput.addEventListener('change', function () {
                const preview     = document.getElementById('video-preview');
                const placeholder = document.getElementById('video-placeholder');
                const file        = this.files[0];

                if (file && file.type.startsWith('video/')) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                } else {
                    preview.src = '';
                    preview.classList.add('d-none');
                    placeholder.classList.remove('d-none');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FUNGSI: HITUNG ULANG URUTAN GAMBAR
        |--------------------------------------------------------------------------
        */
        function updateSortOrders() {
            // Ambil semua input urutan, baik gambar dari database (existing) maupun gambar baru
            const sortInputs = document.querySelectorAll(
                '#image-wrapper input[name="sort_order[]"], #image-wrapper input[name="existing_sort_orders[]"]'
            );
            
            let currentIndex = 1; // Set urutan dimulai dari 1
            
            sortInputs.forEach(input => {
                const item = input.closest('.image-item');
                // Hanya hitung item yang TIDAK disembunyikan (tidak dihapus)
                if (item && !item.classList.contains('d-none') && item.style.display !== 'none') {
                    input.value = currentIndex;
                    currentIndex++; // Tambah angka untuk elemen berikutnya
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | TEMPLATE : IMAGE ROW
        |--------------------------------------------------------------------------
        */
        function imageRowHTML() {
            // Value input sort_order bisa dikosongkan karena akan otomatis diisi oleh updateSortOrders()
            return `
                <div class="border rounded p-3 mb-3 image-item">
                    <div class="row align-items-start g-3">
                        <div class="col-md-2 d-flex align-items-center justify-content-center">
                            <div class="image-preview-wrapper border rounded d-flex align-items-center justify-content-center bg-light"
                                style="width:150px; height:100px; overflow:hidden;">
                                <img class="image-preview d-none"
                                    style="width:100%; height:100%; object-fit:cover;"
                                    alt="Preview">
                                <span class="image-preview-placeholder text-muted small text-center px-1">
                                    Tanpa Gambar
                                </span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label mb-1">Nama / Label Gambar</label>
                                    <input type="text" name="image_labels[]" class="form-control form-control-sm"
                                        placeholder="Contoh: Tampak Depan, Detail, dll.">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label mb-1">File Gambar</label>
                                    <input type="file" name="images[]" class="form-control form-control-sm image-file-input"
                                        accept="image/*">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1">Urutan</label>
                                    <input type="number" name="sort_order[]" min="1"
                                        class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="col-auto d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-image mt-4">
                                &times; Hapus
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        /*
        |--------------------------------------------------------------------------
        | EVENT LISTENERS (TAMBAH & HAPUS)
        |--------------------------------------------------------------------------
        */
        const imageWrapper = document.getElementById('image-wrapper');
        const addImageBtn = document.getElementById('add-image');

        // 2. Saat tombol Hapus ditekan (Menggunakan Event Delegation)
        if (imageWrapper) {
            imageWrapper.addEventListener('click', function(e) {
                
                // Kasus A: Menghapus baris gambar BARU (langsung buang elemen dari HTML)
                if (e.target.closest('.btn-remove-image')) {
                    e.target.closest('.image-item').remove();
                    updateSortOrders(); // Hitung ulang supaya angkanya merapat
                }
                
                // Kasus B: Menghapus baris gambar EXISTING (sembunyikan elemen, centang checkbox delete)
                if (e.target.closest('.btn-remove-existing-image')) {
                    const btn = e.target.closest('.btn-remove-existing-image');
                    const item = btn.closest('.image-item');
                    
                    // Cari checkbox hidden dan jadikan checked
                    const checkbox = item.querySelector('.deleted-image-checkbox');
                    if (checkbox) checkbox.checked = true;
                    
                    // Sembunyikan div-nya (jangan dihapus agar id tetap terkirim ke backend untuk proses delete DB)
                    item.classList.add('d-none');
                    
                    updateSortOrders(); // Hitung ulang supaya angkanya merapat mengabaikan yang tersembunyi
                }
            });
        }

        updateSortOrders();

        /*
        |--------------------------------------------------------------------------
        | TEMPLATE : VARIANT ROW
        |--------------------------------------------------------------------------
        */
        function variantRowHTML() {
            return `
                <div class="border rounded p-3 mb-3 variant-item bg-light">
                    <div class="row g-3 align-items-end">
                        
                        {{-- HIDDEN INPUT KOSONG UNTUK VARIAN BARU --}}
                        <input type="hidden" name="variant_ids[]" value="">

                        <div class="col-md-4 name-column">
                            <label class="form-label fw-semibold mb-1">Nama Varian</label>
                            <input type="text" name="size[]" class="form-control form-control-sm"
                                placeholder="Contoh: Merah, XL, dll" value="{{ old('size.0') }}">
                        </div>

                        <div class="col-md-3 large-unit-column">
                            <label class="form-label fw-semibold mb-1">Isi Colly</label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <input type="text" name="large_qty[]" class="form-control autonumeric-qty" value="{{ old('large_qty.0') }}">
                                <span class="input-group-text large-unit-name bg-white text-secondary">Satuan</span>
                            </div>
                        </div>

                        <div class="col-md-3 small-unit-column d-none">
                            <label class="form-label fw-semibold mb-1">Isi Pack / Box</label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <input type="text" name="small_qty[]" class="form-control autonumeric-qty" value="{{ old('small_qty.0') }}">
                                <span class="input-group-text small-unit-name bg-white text-secondary">Satuan</span>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-variant">
                                &times; Hapus
                            </button>
                        </div>
                        
                    </div>
                </div>
            `;
        }

        /*
        |--------------------------------------------------------------------------
        | ADD IMAGE & VARIANT
        |--------------------------------------------------------------------------
        */
        if (addImageBtn) {
            addImageBtn.addEventListener('click', () => {
                document.getElementById('image-wrapper').insertAdjacentHTML('beforeend', imageRowHTML());
                updateSortOrders()
            });
        }

        const addVariantBtn = document.getElementById('add-variant');
        if (addVariantBtn) {
            addVariantBtn.addEventListener('click', () => {
                document.getElementById('variant-wrapper').insertAdjacentHTML('beforeend', variantRowHTML());
                initAutoNumeric()
                updateVariantColumns()
                updateDecimalMode();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | DYNAMIC IMAGE PREVIEW (UPLOAD MULTIPLE)
        |--------------------------------------------------------------------------
        */
        document.addEventListener('change', (e) => {
            const fileInput = e.target.closest('.image-file-input');
            if (!fileInput) return;

            const wrapper     = fileInput.closest('.image-item');
            const preview     = wrapper.querySelector('.image-preview');
            const placeholder = wrapper.querySelector('.image-preview-placeholder');
            const file        = fileInput.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    preview.src = ev.target.result;
                    preview.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
            }
        });

        /*
        |--------------------------------------------------------------------------
        | REMOVE ACTIONS
        |--------------------------------------------------------------------------
        */
        document.addEventListener('click', (e) => {
            const removeVarBtn = e.target.closest('.btn-remove-variant');
            if (removeVarBtn) {
                removeVarBtn.closest('.variant-item').remove();
                return;
            }

            const removeExistingBtn = e.target.closest('.btn-remove-existing-image');
            if (removeExistingBtn) {
                const row      = removeExistingBtn.closest('.image-item');
                const checkbox = row.querySelector('.deleted-image-checkbox');

                checkbox.checked = true;
                row.style.opacity = '0.5';
                row.style.pointerEvents = 'none';

                removeExistingBtn.disabled = true;
                removeExistingBtn.textContent = 'Dihapus';
            }
        });

        /*
        |--------------------------------------------------------------------------
        | SUBMIT LOADING
        |--------------------------------------------------------------------------
        */
        const form = document.getElementById('product-form');
        const submitBtn = document.getElementById('submit-btn');

        if (form && submitBtn) {
            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                submitBtn.querySelector('.btn-text').classList.add('d-none');
                submitBtn.querySelector('.btn-loading').classList.remove('d-none');
            });
        }

    });
</script>
@endsection