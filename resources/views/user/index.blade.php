@extends('layouts.app')

@section('title', 'List User')

@section('content')

<div class="row">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                List User
            </div>
            <div class="">
                <a href="{{ route('master.user.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Create New User</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show animate__animated animate__bounceInLeft" role="alert">
                    <i class="fa-solid fa-circle-check" style="margin-top: 5px; margin-right:5px;"></i>
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="dt-toolbar">
                <div class="dt-search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" id="dt-search-input" placeholder="Search any column…" aria-label="Search">
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <label style="font-size: 12.5px; color: var(--m-text-muted); display: inline-flex; align-items: center; gap: 8px;">
                        Show
                        <select id="dt-page-size" class="form-select" style="width: 80px; height: 32px; padding: 0 24px 0 10px; font-size: 12.5px;">
                            <option>5</option>
                            <option selected>10</option>
                            <option>20</option>
                            <option>50</option>
                        </select>
                        rows
                    </label>
                </div>
            </div>

            <div class="table-responsive">
                <table class="m-table dt-table" id="dt-table">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th data-sort="name">Name</th>
                            <th data-sort="email">Email</th>
                            <th data-sort="plan">Role</th>
                            <th data-sort="amount" class="num">Phone</th>
                            <th data-sort="status">Status</th>
                            <th data-sort="signup">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dt-body"></tbody>
                </table>
            </div>

            <div id="dt-empty" class="empty-state" style="display: none;">
                <span class="empty-state__icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <h3 class="empty-state__title">No results</h3>
                <p class="empty-state__text">Try a different search term, or clear the filter to show everything again.</p>
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

            /*
            |--------------------------------------------------------------------------
            | Elements
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

            /*
            |--------------------------------------------------------------------------
            | State
            |--------------------------------------------------------------------------
            */

            let DATA = [];

            let state = {
                sort: 'id',
                dir: 'asc',
                page: 1,
                perPage: 10,
                query: ''
            };

            /*
            |--------------------------------------------------------------------------
            | Status Mapping
            |--------------------------------------------------------------------------
            */

            const STATUS = {

                active: {
                    label: 'Active',
                    cls: 'status--process'
                },

                inactive: {
                    label: 'Inactive',
                    cls: 'status--denied'
                },

                cancelled: {
                    label: 'Cancelled',
                    cls: 'status--denied'
                },

            };

            /*
            |--------------------------------------------------------------------------
            | Helpers
            |--------------------------------------------------------------------------
            */

            function format(date) {

                if (!date) return '';

                return new Date(date).toLocaleDateString(undefined, {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

            }

            function escapeHtml(str) {

                const div = document.createElement('div');

                div.textContent = str;

                return div.innerHTML;

            }

            /*
            |--------------------------------------------------------------------------
            | Fetch Data AJAX
            |--------------------------------------------------------------------------
            */

            async function fetchData() {

                try {

                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                Loading data...
                            </td>
                        </tr>
                    `;

                    const response = await fetch(
                        "{{ route('master.user.show') }}"
                    );

                    if (!response.ok) {

                        throw new Error('Failed to fetch data');

                    }

                    DATA = await response.json();

                    render();

                } catch (error) {

                    console.error(error);

                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-danger py-4">
                                Failed to load data
                            </td>
                        </tr>
                    `;

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

                    ? DATA.filter((row) =>
                        Object.values(row).some((v) =>
                            String(v).toLowerCase().includes(q)
                        )
                    )

                    : [...DATA];

                /*
                |--------------------------------------------------------------------------
                | Sorting
                |--------------------------------------------------------------------------
                */

                filtered.sort((a, b) => {

                    const va = a[state.sort];

                    const vb = b[state.sort];

                    let cmp;

                    if (
                        typeof va === 'number' &&
                        typeof vb === 'number'
                    ) {

                        cmp = va - vb;

                    } else {

                        cmp = String(va)
                            .localeCompare(String(vb));

                    }

                    return state.dir === 'asc'
                        ? cmp
                        : -cmp;

                });

                /*
                |--------------------------------------------------------------------------
                | Pagination
                |--------------------------------------------------------------------------
                */

                const total = filtered.length;

                const totalPages = Math.max(
                    1,
                    Math.ceil(total / state.perPage)
                );

                if (state.page > totalPages) {

                    state.page = totalPages;

                }

                const start = (state.page - 1) * state.perPage;

                const slice = filtered.slice(
                    start,
                    start + state.perPage
                );

                /*
                |--------------------------------------------------------------------------
                | Empty State
                |--------------------------------------------------------------------------
                */

                if (total === 0) {

                    tbody.innerHTML = '';

                    empty.style.display = 'flex';

                } else {

                    empty.style.display = 'none';

                    tbody.innerHTML = slice.map((row, index) => {
                        const s = STATUS[row.status] || {
                            label: row.status,
                            cls: ''
                        };

                        return `
                            <tr>
                                <td>
                                    ${start + index + 1}
                                </td>
                                <td>
                                    <strong
                                        style="
                                            color:var(--m-text);
                                            font-weight:500;
                                        ">
                                        ${escapeHtml(row.name)}
                                    </strong>
                                </td>

                                <td>
                                    <a href="#"
                                    style="
                                            color:var(--m-accent);
                                            text-decoration:none;
                                    ">
                                        ${escapeHtml(row.email)}
                                    </a>
                                </td>

                                <td>
                                    ${escapeHtml(row.role ?? '-')}
                                </td>

                                <td>
                                    ${escapeHtml(row.phone ?? '-')}
                                </td>

                                <td>
                                    <span class="${s.cls}">
                                        ${s.label}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td>

                                    <div class="table-data-feature">

                                        <!-- Edit -->
                                        <button
                                            class="item btn-edit"
                                            data-id="${row.uuid}"
                                            title="Edit">

                                            <i class="fa-solid fa-pen-to-square text-primary"></i>

                                        </button>

                                        <!-- Delete -->
                                        <button
                                            class="item btn-delete"
                                            data-id="${row.uuid}"
                                            title="Delete">

                                            <i class="fa-solid fa-trash text-danger"></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>
                        `;

                    }).join('');

                }

                /*
                |--------------------------------------------------------------------------
                | Info
                |--------------------------------------------------------------------------
                */

                info.textContent = total === 0

                    ? 'No results'

                    : `Showing ${start + 1}–${start + slice.length} of ${total}`;

                /*
                |--------------------------------------------------------------------------
                | Pagination Navigation
                |--------------------------------------------------------------------------
                */

                nav.innerHTML = '';

                const make = (
                    label,
                    page,
                    opts = {}
                ) => {

                    const btn = document.createElement('button');

                    btn.innerHTML = label;

                    btn.disabled = !!opts.disabled;

                    if (opts.active) {

                        btn.classList.add('is-active');

                    }

                    btn.addEventListener('click', () => {

                        state.page = page;

                        render();

                    });

                    nav.appendChild(btn);

                };

                make(
                    '<i class="fa-solid fa-chevron-left"></i>',
                    Math.max(1, state.page - 1),
                    {
                        disabled: state.page === 1
                    }
                );

                for (let p = 1; p <= totalPages; p++) {

                    make(String(p), p, {
                        active: p === state.page
                    });

                }

                make(
                    '<i class="fa-solid fa-chevron-right"></i>',
                    Math.min(totalPages, state.page + 1),
                    {
                        disabled: state.page === totalPages
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | Sort Indicators
                |--------------------------------------------------------------------------
                */

                headers.forEach((h) => {

                    h.classList.remove(
                        'dt-sort-asc',
                        'dt-sort-desc'
                    );

                    if (h.dataset.sort === state.sort) {

                        h.classList.add(
                            state.dir === 'asc'
                                ? 'dt-sort-asc'
                                : 'dt-sort-desc'
                        );

                    }

                });

            }

            /*
            |--------------------------------------------------------------------------
            | Events
            |--------------------------------------------------------------------------
            */

            headers.forEach((h) => {

                h.addEventListener('click', () => {

                    const col = h.dataset.sort;

                    if (state.sort === col) {

                        state.dir =
                            state.dir === 'asc'
                                ? 'desc'
                                : 'asc';

                    } else {

                        state.sort = col;

                        state.dir = 'asc';

                    }

                    render();

                });

            });

            search.addEventListener('input', () => {

                state.query = search.value;

                state.page = 1;

                render();

            });

            pageSize.addEventListener('change', () => {

                state.perPage = parseInt(
                    pageSize.value,
                    10
                );

                state.page = 1;

                render();

            });

            if (clearBtn) {

                clearBtn.addEventListener('click', () => {

                    search.value = '';

                    state.query = '';

                    render();

                });

            }

            /*
            |--------------------------------------------------------------------------
            | Init
            |--------------------------------------------------------------------------
            */

            fetchData();


            document.addEventListener('click', (e) => {

                /*
                |--------------------------------------------------------------------------
                | Edit
                |--------------------------------------------------------------------------
                */

                const editBtn = e.target.closest('.btn-edit');

                if (editBtn) {

                    const id = editBtn.dataset.id;

                    window.location.href =
                        `/master/user/${id}/edit`;

                }

                /*
                |--------------------------------------------------------------------------
                | Delete
                |--------------------------------------------------------------------------
                */

                const deleteBtn = e.target.closest('.btn-delete');

                if (deleteBtn) {

                    const id = deleteBtn.dataset.id;

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

                            /*
                            |--------------------------------------------------------------------------
                            | Route URL
                            |--------------------------------------------------------------------------
                            */

                            let deleteUrl = "{{ route('master.user.destroy', ':id') }}";

                            deleteUrl = deleteUrl.replace(':id', id);

                            /*
                            |--------------------------------------------------------------------------
                            | AJAX Delete
                            |--------------------------------------------------------------------------
                            */

                            fetch(deleteUrl, {

                                method: 'DELETE',

                                headers: {

                                    'Content-Type': 'application/json',

                                    'X-CSRF-TOKEN': document
                                        .querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content'),

                                    'Accept': 'application/json',

                                },

                            })

                            .then(async (response) => {

                                const data = await response.json();

                                if (!response.ok) {

                                    throw new Error(
                                        data.message || 'Failed to delete user'
                                    );

                                }

                                return data;

                            })

                            .then((data) => {

                                Swal.fire({

                                    icon: 'success',

                                    title: 'Success',

                                    text: data.message,

                                    timer: 1800,

                                    showConfirmButton: false,

                                });

                                /*
                                |--------------------------------------------------------------------------
                                | Reload Table
                                |--------------------------------------------------------------------------
                                */

                                fetchData();

                            })

                            .catch((error) => {

                                Swal.fire({

                                    icon: 'error',

                                    title: 'Error',

                                    text: error.message,

                                });

                            });

                        }

                    });

                }

            });
        })();

    </script>
@endsection