@extends('layouts.app')

@section('title', 'Product Brand')

@section('content')

<div class="row">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__bounceInLeft" role="alert">
            <i class="fa-solid fa-circle-check" style="margin-top: 5px; margin-right:5px;"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                {{ $editing ? 'Ubah Merek' : 'Menambah Merek' }}
            </div>

            <form id="brand-form" action="{{ $editing ? route('master.brand.update', $brand) : route('master.brand.store') }}"
                method="POST">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="card-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-control-label">
                            Nama Merek<span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Masukkan Nama Merek"
                            value="{{ $editing ? old('name', $brand->name) : old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-control-label">
                            Keterangan
                        </label>
                        <textarea id="description"
                            name="description"
                            rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="">{{ $editing ? old('description', $brand->description) : old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-control-label">Status Merek</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                type="checkbox"
                                id="status"
                                name="status"
                                {{ $editing ? old('status', ($brand->status ? 'checked' : '')) : 'checked' }}>
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset
                    </button>
                    <button type="submit" id="submit-btn" class="btn btn-primary">
                        <span class="btn-text">
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ $editing ? 'Ubah Merek' : 'Simpan Merek' }}
                        </span>
                        <span class="btn-loading d-none">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Side -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>Daftar Merek</div>
            </div>

            <div class="card-body">
                <div class="dt-toolbar">
                    <div class="dt-search">
                        <i class="fa-solid fa-magnifying-glass"aria-hidden="true"></i>
                        <input type="search" id="dt-search-input" placeholder="Pencarian Data…" aria-label="Search">
                    </div>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <label style="font-size:12.5px; color:var(--m-text-muted); display:inline-flex; align-items:center; gap:8px;">
                            Tampilkan
                            <select id="dt-page-size" class="form-select" style="width:80px; height:32px; padding:0 24px 0 10px; font-size:12.5px;">
                                <option>5</option>
                                <option selected>10</option>
                                <option>20</option>
                                <option>50</option>
                            </select>
                            baris
                        </label>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="m-table dt-table" id="dt-table">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th data-sort="name">Nama Merek</th>
                                <th data-sort="status">Status Merek</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="dt-body"></tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="dt-empty" class="empty-state" style="display:none;">
                    <span class="empty-state__icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <h3 class="empty-state__title">No results</h3>
                    <p class="empty-state__text">
                        Try a different search term, or clear the filter to show everything again.
                    </p>
                    <div class="empty-state__actions">
                        <button type="button" class="m-btn m-btn--ghost" id="dt-clear-search">
                            Clear search
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="dt-pagination">
                    <span class="dt-pagination__info" id="dt-info"></span>
                    <div class="dt-pagination__nav" id="dt-nav"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    (function () {
        /*
        |--------------------------------------------------------------------------
        | Elements & State
        |--------------------------------------------------------------------------
        */
        const tbody    = document.getElementById('dt-body');
        const empty    = document.getElementById('dt-empty');
        const search   = document.getElementById('dt-search-input');
        const pageSize = document.getElementById('dt-page-size');
        const info     = document.getElementById('dt-info');
        const nav      = document.getElementById('dt-nav');
        const headers  = document.querySelectorAll('#dt-table th[data-sort]');
        const clearBtn = document.getElementById('dt-clear-search');

        let DATA = [];
        let state = {
            sort: 'id',
            dir: 'asc',
            page: 1,
            perPage: 10,
            query: ''
        };

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str ?? '';
            return div.innerHTML;
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch Data AJAX
        |--------------------------------------------------------------------------
        */
        async function fetchData() {
            try {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4">Loading data...</td></tr>`;
                const response = await fetch("{{ route('master.brand.show') }}");
                if (!response.ok) throw new Error('Failed to fetch data');
                DATA = await response.json();
                render();
            } catch (error) {
                console.error(error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Failed to load data</td></tr>`;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Render Table
        |--------------------------------------------------------------------------
        */
        function render() {
            const q = state.query.trim().toLowerCase();
            const filtered = q
                ? DATA.filter((row) => Object.values(row).some((v) => String(v).toLowerCase().includes(q)))
                : [...DATA];

            filtered.sort((a, b) => {
                const va = a[state.sort], vb = b[state.sort];
                let cmp = (typeof va === 'number' && typeof vb === 'number') 
                            ? va - vb 
                            : String(va).localeCompare(String(vb));
                return state.dir === 'asc' ? cmp : -cmp;
            });

            const total = filtered.length;
            const totalPages = Math.max(1, Math.ceil(total / state.perPage));
            if (state.page > totalPages) state.page = totalPages;

            const start = (state.page - 1) * state.perPage;
            const slice = filtered.slice(start, start + state.perPage);

            if (total === 0) {
                tbody.innerHTML = '';
                empty.style.display = 'flex';
            } else {
                empty.style.display = 'none';
                tbody.innerHTML = slice.map((row, index) => {
                    return `
                        <tr>
                            <td>${start + index + 1}</td>
                            <td>
                                <strong style="color: var(--m-text); font-weight: 600;">
                                    ${escapeHtml(row.name)}
                                </strong>
                            </td>
                            <!-- Perubahan: Status diganti menjadi dropdown (select) -->
                            <td>
                                <select class="form-select form-select-sm btn-change-status" 
                                    data-id="${row.uuid}" 
                                    data-original="${row.status}"
                                    style="width: 140px; cursor: pointer; ${row.status === 'active' ? 'border-color: #198754; color: #198754;' : 'border-color: #dc3545; color: #dc3545;'}">
                                    <option value="active" ${row.status === 'active' ? 'selected' : ''}>Aktif</option>
                                    <option value="inactive" ${row.status === 'inactive' ? 'selected' : ''}>Tidak Aktif</option>
                                </select>
                            </td>
                            <td>
                                <div class="table-data-feature">
                                    <button class="item btn-edit" data-id="${row.uuid}" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-primary"></i>
                                    </button>
                                    <button class="item btn-delete" data-id="${row.uuid}" title="Delete">
                                        <i class="fa-solid fa-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            info.textContent = total === 0 ? 'No results' : `Menampilkan ${start + 1}–${start + slice.length} dari ${total}`;
            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            nav.innerHTML = '';
            const make = (label, page, opts = {}) => {
                const btn = document.createElement('button');
                btn.innerHTML = label;
                btn.disabled = !!opts.disabled;
                if (opts.active) btn.classList.add('is-active');
                btn.addEventListener('click', () => { state.page = page; render(); });
                nav.appendChild(btn);
            };

            make('<i class="fa-solid fa-chevron-left"></i>', Math.max(1, state.page - 1), { disabled: state.page === 1 });
            for (let p = 1; p <= totalPages; p++) {
                make(String(p), p, { active: p === state.page });
            }
            make('<i class="fa-solid fa-chevron-right"></i>', Math.min(totalPages, state.page + 1), { disabled: state.page === totalPages });

            headers.forEach((h) => {
                h.classList.remove('dt-sort-asc', 'dt-sort-desc');
                if (h.dataset.sort === state.sort) h.classList.add(state.dir === 'asc' ? 'dt-sort-asc' : 'dt-sort-desc');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Events (Table Interactions)
        |--------------------------------------------------------------------------
        */
        headers.forEach((h) => {
            h.addEventListener('click', () => {
                const col = h.dataset.sort;
                state.dir = state.sort === col ? (state.dir === 'asc' ? 'desc' : 'asc') : 'asc';
                state.sort = col;
                render();
            });
        });

        search.addEventListener('input', () => { state.query = search.value; state.page = 1; render(); });
        pageSize.addEventListener('change', () => { state.perPage = parseInt(pageSize.value, 10); state.page = 1; render(); });
        if (clearBtn) clearBtn.addEventListener('click', () => { search.value = ''; state.query = ''; render(); });

        /*
        |--------------------------------------------------------------------------
        | Event Delegation for Edit, Delete, & Change Status (Dropdown)
        |--------------------------------------------------------------------------
        */
        document.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.btn-edit');
            if (editBtn) {
                let editUrl = "{{ route('master.brand.edit', ':id') }}".replace(':id', editBtn.dataset.id);
                window.location.href = editUrl;
            }

            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                Swal.fire({
                    title: 'Delete Confirmation',
                    text: 'Deleted data cannot be restored.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    confirmButtonColor: '#DC3545',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let deleteUrl = "{{ route('master.brand.destroy', ':id') }}".replace(':id', deleteBtn.dataset.id);
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                        })
                        .then(async (response) => {
                            const data = await response.json();
                            if (!response.ok) throw new Error(data.message || 'Failed to delete data');
                            return data;
                        })
                        .then((data) => {
                            Swal.fire({ icon: 'success', title: 'Success', text: data.message, timer: 1800, showConfirmButton: false });
                            fetchData();
                        })
                        .catch((error) => {
                            Swal.fire({ icon: 'error', title: 'Error', text: error.message });
                        });
                    }
                });
            }
        });

        // Event listener khusus untuk perubahan dropdown status
        document.addEventListener('change', (e) => {
            if (e.target.matches('.btn-change-status')) {
                const select = e.target;
                const id = select.dataset.id;
                const newValue = select.value; // 'active' atau 'inactive'
                const originalValue = select.dataset.original; 
                
                let toggleUrl = "{{ route('master.brand.toggleStatus', ':id') }}".replace(':id', id);

                select.disabled = true; // Disable sementara saat AJAX berproses

                fetch(toggleUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    // Kita kirim 1 jika 'active', dan 0 jika 'inactive' ke backend
                    body: JSON.stringify({ status: newValue === 'active' ? 1 : 0 })
                })
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Failed to update status');
                    return data;
                })
                .then(data => {
                    // Simpan nilai baru sebagai data original
                    select.dataset.original = newValue;
                    
                    // Ubah warna border & font agar sesuai status baru
                    if (newValue === 'active') {
                        select.style.borderColor = '#198754';
                        select.style.color = '#198754';
                    } else {
                        select.style.borderColor = '#dc3545';
                        select.style.color = '#dc3545';
                    }

                    // Update cache DATA agar pencarian & sorting tetap akurat
                    const rowToUpdate = DATA.find(r => r.uuid === id);
                    if(rowToUpdate) rowToUpdate.status = newValue;
                    
                    Swal.fire({ 
                        toast: true, position: 'top-end', icon: 'success', 
                        title: data.message || 'Status berhasil diperbarui', 
                        showConfirmButton: false, timer: 1500 
                    });
                })
                .catch(err => {
                    // Jika gagal, kembalikan select ke nilai sebelumnya (revert)
                    select.value = originalValue;
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                })
                .finally(() => {
                    select.disabled = false;
                });
            }
        });

        const form = document.getElementById('brand-form');
        const submitBtn = document.getElementById('submit-btn');
        if (form && submitBtn) {
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.querySelector('.btn-text').classList.add('d-none');
                submitBtn.querySelector('.btn-loading').classList.remove('d-none');
                form.classList.add('form-loading');
            });
        }

        // Init
        fetchData();
    })();
</script>
@endsection