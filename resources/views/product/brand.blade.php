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
                {{ $editing ? 'Update Brand' : 'Create Brand' }}
            </div>

            <form id="brand-form" action="{{ $editing ? route('master.brand.update', $brand) : route('master.brand.store') }}"
                method="POST">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="card-body">
                    <!-- Brand ID -->
                    <div class="mb-3">
                        <label for="id" class="form-control-label">
                            Brand ID
                        </label>
                        <input type="text"
                            id="id"
                            name="id"
                            class="form-control"
                            placeholder="Auto Generate"
                            readonly
                            value = "{{ $editing ? $brand->id : $generateId }}">
                    </div>

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-control-label">
                            Brand Name<span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter Brand Name"
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
                            Description
                        </label>

                        <textarea id="description"
                            name="description"
                            rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Enter Description">{{ $editing ? old('description', $brand->description) : old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- Status -->
                    <div class="mb-3">

                        <label class="form-control-label">
                            Status
                        </label>

                        <div class="form-check form-switch">

                            <input class="form-check-input"
                                type="checkbox"
                                id="status"
                                name="status"
                                {{ $editing ? old('status', ($brand->status ? 'checked' : '')) : 'checked' }}>

                            <label class="form-check-label"
                                for="status">

                                Active

                            </label>

                        </div>

                    </div>

                </div>

                <div class="card-footer d-flex justify-content-between">

                    <button type="reset"
                        class="btn btn-secondary">

                        <i class="fa-solid fa-rotate-left"></i>

                        Reset

                    </button>

                    <button type="submit" id="submit-btn" class="btn btn-primary">
                        <span class="btn-text">
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ $editing ? 'Update Brand' : 'Save Brand' }}
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

                <div>
                    Brand List
                </div>

            </div>

            <div class="card-body">

                <div class="dt-toolbar">
                    <div class="dt-search">
                        <i class="fa-solid fa-magnifying-glass"aria-hidden="true"></i>
                        <input type="search" id="dt-search-input" placeholder="Search any column…" aria-label="Search">
                    </div>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <label style="font-size:12.5px; color:var(--m-text-muted); display:inline-flex; align-items:center; gap:8px;">
                            Show
                            <select id="dt-page-size" class="form-select" style="width:80px; height:32px; padding:0 24px 0 10px; font-size:12.5px;">
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
                                <th data-sort="name">Brand Name</th>
                                <th data-sort="status">Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dt-body"></tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="dt-empty"
                    class="empty-state"
                    style="display:none;">
                    <span class="empty-state__icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <h3 class="empty-state__title">
                        No results
                    </h3>

                    <p class="empty-state__text">
                        Try a different search term,
                        or clear the filter to show
                        everything again.
                    </p>

                    <div class="empty-state__actions">
                        <button type="button"
                            class="m-btn m-btn--ghost"
                            id="dt-clear-search">
                            Clear search
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="dt-pagination">
                    <span class="dt-pagination__info"
                        id="dt-info"></span>
                    <div class="dt-pagination__nav"
                        id="dt-nav"></div>
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
        };

        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

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

                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            Loading data...
                        </td>
                    </tr>
                `;

                const response = await fetch(
                    "{{ route('master.brand.show') }}"
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
                        <td colspan="5"
                            class="text-center text-danger py-4">

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
                        String(v)
                            .toLowerCase()
                            .includes(q)
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
                        label: 'Unknown',
                        cls: ''
                    };

                    return `
                        <tr>

                            <!-- No -->
                            <td>
                                ${start + index + 1}
                            </td>

                            <!-- Brand Name -->
                            <td>
                                <strong
                                    style="
                                        color: var(--m-text);
                                        font-weight: 600;
                                    ">

                                    ${escapeHtml(row.name)}

                                </strong>
                            </td>

                            <!-- Status -->
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

        /*
        |--------------------------------------------------------------------------
        | Actions
        |--------------------------------------------------------------------------
        */

        document.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.btn-edit');
            if (editBtn) {
                const id = editBtn.dataset.id;
                let editUrl = "{{ route('master.brand.edit', ':id') }}";
                editUrl = editUrl.replace(':id', id);
                window.location.href = editUrl;
            }

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
                        let deleteUrl =
                            "{{ route('master.brand.destroy', ':id') }}";
                        deleteUrl =
                            deleteUrl.replace(':id', id);
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
                            if (!response.ok) {
                                throw new Error(
                                    data.message ||
                                    'Failed to delete category'
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

        const form = document.getElementById('brand-form');
        const submitBtn = document.getElementById('submit-btn');

        if (form && submitBtn) {

            form.addEventListener('submit', function () {

                /*
                |--------------------------------------------------------------------------
                | Disable Button
                |--------------------------------------------------------------------------
                */

                submitBtn.disabled = true;

                /*
                |--------------------------------------------------------------------------
                | Change Button Text
                |--------------------------------------------------------------------------
                */

                submitBtn
                    .querySelector('.btn-text')
                    .classList.add('d-none');

                submitBtn
                    .querySelector('.btn-loading')
                    .classList.remove('d-none');

                /*
                |--------------------------------------------------------------------------
                | Add Loading Effect
                |--------------------------------------------------------------------------
                */

                form.classList.add('form-loading');

            });

        }
    })();

</script>

@endsection