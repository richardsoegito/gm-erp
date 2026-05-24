@extends('layouts.app')

@section('title', 'Daftar Role')

@section('content')

<div class="row">
    <div class="card">
        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div class="mb-3 mb-md-0 fw-bold fs-5 text-center text-md-start">
                Daftar Role
            </div>
            
            <div class="d-flex flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-outline-info w-100" id="btn-add-permission">
                    <i class="fa fa-key"></i> Tambah Permission
                </button>

                <a href="{{ route('settings.group_user.create') }}" class="btn btn-primary w-100 text-center">
                    <i class="fa fa-plus"></i> Tambah Role Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show animate__animated animate__bounceInLeft" role="alert">
                    <i class="fa-solid fa-circle-check" style="margin-top: 5px; margin-right:5px;"></i>
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="dt-toolbar">
                <div class="dt-search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" id="dt-search-input" placeholder="Pencarian Data…" aria-label="Search">
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <label style="font-size: 12.5px; color: var(--m-text-muted); display: inline-flex; align-items: center; gap: 8px;">
                        Tampilkan
                        <select id="dt-page-size" class="form-select" style="width: 80px; height: 32px; padding: 0 24px 0 10px; font-size: 12.5px;">
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
                            <th data-sort="name">Nama Role</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="dt-body"></tbody>
                </table>
            </div>

            <div id="dt-empty" class="empty-state" style="display: none;">
                <span class="empty-state__icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <h3 class="empty-state__title">No results</h3>
                <p class="empty-state__text">Try a different search term, atau clear filter.</p>
                <div class="empty-state__actions">
                    <button type="button" class="m-btn m-btn--ghost" id="dt-clear-search">Clear search</button>
                </div>
            </div>

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
            // --- VARIABEL & ELEMENT DATATABLES ---
            const tbody    = document.getElementById('dt-body');
            const empty    = document.getElementById('dt-empty');
            const search   = document.getElementById('dt-search-input');
            const pageSize = document.getElementById('dt-page-size');
            const info     = document.getElementById('dt-info');
            const nav      = document.getElementById('dt-nav');
            const headers  = document.querySelectorAll('#dt-table th[data-sort]');
            const clearBtn = document.getElementById('dt-clear-search');

            let DATA = [];
            let state = { sort: 'id', dir: 'asc', page: 1, perPage: 10, query: '' };

            // --- HELPERS ---
            function escapeHtml(str) {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            function capitalizeFirstLetter(string) {
                if (!string) return '';
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            // --- FETCH DATA ROLE ---
            async function fetchData() {
                try {
                    // Update colspan menjadi 3 karena kolom dikurangi
                    tbody.innerHTML = `<tr><td colspan="3" class="text-center py-4">Loading data...</td></tr>`;
                    const response = await fetch("{{ route('settings.group_user.show') }}");
                    if (!response.ok) throw new Error('Failed to fetch data');
                    DATA = await response.json();
                    render();
                } catch (error) {
                    console.error(error);
                    tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger py-4">Failed to load data</td></tr>`;
                }
            }

            // --- RENDER TABLE ---
            function render() {
                const q = state.query.trim().toLowerCase();
                const filtered = q
                    ? DATA.filter((row) => String(row.name).toLowerCase().includes(q))
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
                        
                        let deleteBtn = row.name !== 'super-admin'
                            ? `<button class="item btn-delete" data-id="${row.id}" title="Delete"><i class="fa-solid fa-trash text-danger"></i></button>`
                            : '';

                        return `
                            <tr>
                                <td>${start + index + 1}</td>
                                <td><strong style="color:var(--m-text); font-weight:500;">${escapeHtml(capitalizeFirstLetter(row.name))}</strong></td>
                                <td>
                                    <div class="table-data-feature d-flex justify-content-center gap-2">
                                        <button class="item btn-edit" data-id="${row.id}" title="Edit"><i class="fa-solid fa-pen-to-square text-primary"></i></button>
                                        ${deleteBtn}
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                info.textContent = total === 0 ? 'No results' : `Menampilkan ${start + 1}–${Math.min(start + slice.length, total)} dari ${total}`;

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
                for (let p = 1; p <= totalPages; p++) make(String(p), p, { active: p === state.page });
                make('<i class="fa-solid fa-chevron-right"></i>', Math.min(totalPages, state.page + 1), { disabled: state.page === totalPages });

                headers.forEach((h) => {
                    h.classList.remove('dt-sort-asc', 'dt-sort-desc');
                    if (h.dataset.sort === state.sort) h.classList.add(state.dir === 'asc' ? 'dt-sort-asc' : 'dt-sort-desc');
                });
            }

            // --- EVENT LISTENERS DATATABLES ---
            headers.forEach((h) => {
                h.addEventListener('click', () => {
                    const col = h.dataset.sort;
                    if (state.sort === col) state.dir = state.dir === 'asc' ? 'desc' : 'asc';
                    else { state.sort = col; state.dir = 'asc'; }
                    render();
                });
            });

            search.addEventListener('input', () => { state.query = search.value; state.page = 1; render(); });
            pageSize.addEventListener('change', () => { state.perPage = parseInt(pageSize.value, 10); state.page = 1; render(); });
            if (clearBtn) clearBtn.addEventListener('click', () => { search.value = ''; state.query = ''; render(); });

            fetchData();

            // --- EVENT DELEGATION EDIT & DELETE ---
            document.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.btn-edit');
                if (editBtn) {
                    let editUrl = "{{ route('settings.group_user.edit', ':id') }}";
                    window.location.href = editUrl.replace(':id', editBtn.dataset.id);
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
                            let deleteUrl = "{{ route('settings.group_user.destroy', ':id') }}";
                            fetch(deleteUrl.replace(':id', deleteBtn.dataset.id), {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                }
                            })
                            .then(async (res) => {
                                const data = await res.json();
                                if (!res.ok) throw new Error(data.message || 'Failed to delete role');
                                return data;
                            })
                            .then((data) => {
                                Swal.fire({ icon: 'success', title: 'Success', text: data.message, timer: 1800, showConfirmButton: false });
                                fetchData();
                            })
                            .catch((error) => Swal.fire({ icon: 'error', title: 'Error', text: error.message }));
                        }
                    });
                }
            });

            document.getElementById('btn-add-permission').addEventListener('click', () => {
                Swal.fire({
                    title: 'Buat Permission Baru',
                    html: `
                        <div class="text-start mb-3 mt-3">
                            <label class="form-label" style="font-size: 14px;">Nama Permission <span class="text-danger">*</span></label>
                            <input id="swal-input-name" class="form-control" placeholder="contoh: create-user">
                        </div>
                        <div class="text-start mb-3">
                            <label class="form-label" style="font-size: 14px;">Group / Parent <span class="text-danger">*</span></label>
                            <select id="swal-input-group">
                                <option value="">-- Pilih Group / Parent --</option>
                                @foreach($parentGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan Permission',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#6f42c1',
                    focusConfirm: false,
                    // INI BAGIAN PENTING: Inisialisasi Tom Select saat popup terbuka
                    didOpen: () => {
                        new TomSelect("#swal-input-group", {
                            create: false,
                            sortField: { field: "text", direction: "asc" },
                            placeholder: "Cari atau Pilih Group..."
                        });
                    },
                    preConfirm: () => {
                        const name = document.getElementById('swal-input-name').value;
                        const parent_id = document.getElementById('swal-input-group').value;
                        
                        if (!name || !parent_id) {
                            Swal.showValidationMessage('Nama dan Group wajib diisi!');
                            return false;
                        }
                        return { name: name, parent_id: parent_id };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Panggil fungsi simpan (AJAX)
                        savePermission(result.value);
                    }
                });
            });

            // Fungsi Terpisah untuk Kirim Data
            async function savePermission(data) {
                try {
                    const response = await fetch("{{ route('settings.group_user.store_permission') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const resData = await response.json();

                    if (!response.ok) throw new Error(resData.message || 'Terjadi kesalahan');

                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: resData.message, timer: 1500 });
                    fetchData(); // Reload table data

                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: error.message });
                }
            }

        })();
    </script>
@endsection