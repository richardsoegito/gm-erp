@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')

<div class="row">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>Daftar Produk</div>

            <a href="{{ route('master.product.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Tambah Produk Baru
            </a>
        </div>

        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show animate__animated animate__bounceInLeft" role="alert">
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- TOOLBAR (SAMA PERSIS USER STYLE) -->
            <div class="dt-toolbar">
                <div class="dt-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" id="dt-search-input" placeholder="Search any column…" />
                </div>

                <div style="display:flex; gap:8px; align-items:center;">
                    <label style="font-size:12.5px; color:var(--m-text-muted); display:flex; align-items:center; gap:8px;">
                        Tampilkan
                        <select id="dt-page-size" class="form-select" style="width:80px; height:32px;">
                            <option>5</option>
                            <option selected>10</option>
                            <option>20</option>
                            <option>50</option>
                        </select>
                        baris
                    </label>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="m-table dt-table" id="dt-table">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th data-sort="name">Nama</th>
                            <th data-sort="brand">Merek</th>
                            <th data-sort="category">Kategori</th>
                            <th data-sort="unit">Satuan</th>
                            <th data-sort="status">Status</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="dt-body"></tbody>
                </table>
            </div>

            <!-- EMPTY STATE -->
            <div id="dt-empty" class="empty-state" style="display:none;">
                <span class="empty-state__icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <h3 class="empty-state__title">No results</h3>
                <p class="empty-state__text">
                    Try a different search term or clear filter
                </p>
                <div class="empty-state__actions">
                    <button class="m-btn m-btn--ghost" id="dt-clear-search">Clear search</button>
                </div>
            </div>

            <!-- PAGINATION -->
            <div class="dt-pagination">
                <span class="dt-pagination__info" id="dt-info"></span>
                <div class="dt-pagination__nav" id="dt-nav"></div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function () {

    const tbody    = document.getElementById('dt-body');
    const empty    = document.getElementById('dt-empty');
    const search   = document.getElementById('dt-search-input');
    const pageSize = document.getElementById('dt-page-size');
    const info     = document.getElementById('dt-info');
    const nav      = document.getElementById('dt-nav');
    const clearBtn = document.getElementById('dt-clear-search');

    let DATA = [];

    let state = {
        sort: 'id',
        dir: 'asc',
        page: 1,
        perPage: 10,
        query: ''
    };

    async function fetchData() {

        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    Loading data...
                </td>
            </tr>
        `;

        const res = await fetch("{{ route('master.product.show') }}");

        DATA = await res.json();

        render();
    }

    function render() {

        const q = state.query.toLowerCase();

        let filtered = q
            ? DATA.filter(row =>
                Object.values(row).some(v =>
                    String(v).toLowerCase().includes(q)
                )
            )
            : [...DATA];

        // SORT
        filtered.sort((a,b) => {

            const va = a[state.sort];
            const vb = b[state.sort];

            return state.dir === 'asc'
                ? String(va).localeCompare(String(vb))
                : String(vb).localeCompare(String(va));
        });

        const total = filtered.length;
        const start = (state.page - 1) * state.perPage;
        const slice = filtered.slice(start, start + state.perPage);

        // EMPTY
        if (total === 0) {
            tbody.innerHTML = '';
            empty.style.display = 'flex';
        } else {
            empty.style.display = 'none';

            tbody.innerHTML = slice.map((row, i) => {

                const statusClass = row.status === 'Aktif'
                    ? 'status--process'
                    : 'status--denied';

                return `
                    <tr>
                        <td>${start + i + 1}</td>

                        <td><strong>${row.name}</strong></td>
                        <td>${row.brand}</td>
                        <td>${row.category}</td>
                        <td>${row.unit}</td>

                        <td>
                            <span class="${statusClass}">
                                ${row.status}
                            </span>
                        </td>

                        <td>
                            <div class="table-data-feature">

                                <button class="item btn-edit"
                                    data-id="${row.uuid}"
                                    title="Edit">
                                    <i class="fa fa-pen-to-square text-primary"></i>
                                </button>

                                <button class="item btn-delete"
                                    data-id="${row.uuid}"
                                    title="Delete">
                                    <i class="fa fa-trash text-danger"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // INFO (SAMA STYLE USER)
        info.innerHTML = `
            Menampilkan ${start + 1}–${start + slice.length} dari ${total}
        `;

        // PAGINATION
        // PAGINATION (KODE BARU)
        nav.innerHTML = '';
        const pages = Math.ceil(total / state.perPage);

        // Jangan render pagination jika halaman tidak ada atau hanya 1 halaman
        if (pages <= 1) return;

        // Fungsi helper untuk membuat tombol
        function createBtn(text, pageNum, isActive = false, isDisabled = false) {
            const btn = document.createElement('button');
            btn.innerHTML = text;
            
            // Tambahkan class sesuai style Anda, misalnya 'btn', 'm-btn', dll
            // btn.classList.add('m-btn'); 

            if (isActive) btn.classList.add('is-active');
            
            if (isDisabled) {
                btn.disabled = true;
                btn.classList.add('is-disabled'); // Class untuk efek disable
            } else if (pageNum !== null) {
                btn.onclick = () => {
                    state.page = pageNum;
                    render();
                };
            }
            return btn;
        }

        // Tombol Previous
        nav.appendChild(createBtn('&laquo;', state.page - 1, false, state.page === 1));

        // Logika Smart Pagination (Sliding Window)
        let paginationItems = [];
        
        if (pages <= 7) {
            // Jika halaman sedikit, tampilkan semua
            for (let i = 1; i <= pages; i++) {
                paginationItems.push(i);
            }
        } else {
            // Jika halaman banyak, gunakan ellipsis (...)
            if (state.page <= 4) {
                paginationItems = [1, 2, 3, 4, 5, '...', pages];
            } else if (state.page >= pages - 3) {
                paginationItems = [1, '...', pages - 4, pages - 3, pages - 2, pages - 1, pages];
            } else {
                paginationItems = [1, '...', state.page - 1, state.page, state.page + 1, '...', pages];
            }
        }

        // Render angka dan ellipsis
        paginationItems.forEach(item => {
            if (item === '...') {
                const span = document.createElement('span');
                span.innerText = '...';
                span.style.padding = '0 8px';
                span.style.display = 'flex';
                span.style.alignItems = 'flex-end';
                nav.appendChild(span);
            } else {
                nav.appendChild(createBtn(item, item, item === state.page));
            }
        });

        // Tombol Next
        nav.appendChild(createBtn('&raquo;', state.page + 1, false, state.page === pages));
    }

    // SEARCH
    search.addEventListener('input', () => {
        state.query = search.value;
        state.page = 1;
        render();
    });

    // PAGE SIZE
    pageSize.addEventListener('change', () => {
        state.perPage = parseInt(pageSize.value);
        state.page = 1;
        render();
    });

    // CLEAR
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            search.value = '';
            state.query = '';
            render();
        });
    }

    document.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.btn-edit');
        const PRODUCT_EDIT_ROUTE = "{{ route('master.product.edit', ':id') }}";

        if (editBtn) {
            const id = editBtn.dataset.id;
            let url = PRODUCT_EDIT_ROUTE.replace(':id', id);
            window.location.href = url;
        }

        const deleteBtn = e.target.closest('.btn-delete');
        if (deleteBtn) {
            e.preventDefault();
            const id = deleteBtn.dataset.id;
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                confirmButtonColor: '#DC3545',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    let deleteUrl = "{{ route('master.product.destroy', ':id') }}";
                    deleteUrl = deleteUrl.replace(':id', id);
                    
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(async (response) => {
                        const data = await response.json();
                        if (!response.ok) {
                            // Pesan error default jika dari server tidak mengirimkan pesan
                            throw new Error(data.message || 'Gagal menghapus produk');
                        }
                        return data;
                    })
                    .then((data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 1800,
                            showConfirmButton: false
                        });

                        fetchData();
                    })
                    .catch((error) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message
                        });
                    });
                }
            });
        }

    });

    fetchData();

})();
</script>
@endsection