@extends('layouts.app')

@section('title', isset($role) ? 'Edit Role' : 'Tambah Role Baru')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header fw-bold fs-5">
                {{ isset($role) ? 'Edit Role: ' . $role->name : 'Tambah Role Baru' }}
            </div>
            
            <form action="{{ isset($role) ? route('settings.group_user.update', $role->id) : route('settings.group_user.store') }}" method="POST">
                @csrf
                @if(isset($role))
                    @method('PUT')
                @endif

                <div class="card-body">
                    <div class="mb-4">
                        <label for="role_name" class="form-label fw-semibold">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="role_name" name="name" 
                               value="{{ old('name', $role->name ?? '') }}" 
                               placeholder="Contoh: Admin, Manager" required>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                        <h5 class="mb-0 fw-semibold">Hak Akses (Permissions)</h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="checkAll">
                            <label class="form-check-label fw-bold text-primary" for="checkAll" style="cursor: pointer;">Pilih Semua</label>
                        </div>
                    </div>

                    <div class="table-responsive border rounded">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="25%">Modul</th>
                                    <th width="15%" class="text-center">Read</th>
                                    <th width="15%" class="text-center">Create</th>
                                    <th width="15%" class="text-center">Update</th>
                                    <th width="15%" class="text-center">Delete</th>
                                    <th width="15%" class="text-center">Semua Baris</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groups as $group)
                                    <tr class="table-light">
                                        <td colspan="6" class="text-primary" style="font-size: 14px;">
                                            <i class="fa fa-folder-open me-2"></i> {{ $group->name }}
                                        </td>
                                    </tr>

                                    @php
                                        // MENGELOMPOKKAN PERMISSION BERDASARKAN MODUL
                                        // Mengambil kata setelah tanda strip '-' (cth: 'create-user' -> jadi 'user')
                                        $modules = $group->permissions->groupBy(function($perm) {
                                            $parts = explode('-', $perm->name, 2);
                                            return count($parts) > 1 ? $parts[1] : $perm->name;
                                        });
                                    @endphp

                                    @foreach($modules as $moduleName => $modulePermissions)
                                        @php 
                                            // Membuat ID unik untuk JS (berjaga-jaga jika ada spasi)
                                            $moduleSlug = Str::slug($moduleName); 
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-medium text-dark" style="font-size: 12px;">
                                                <i class="fa-solid fa-angle-right me-2 text-muted" style="font-size: 12px;"></i>
                                                {{ ucwords(str_replace('-', ' ', $moduleName)) }}
                                            </td>
                                            
                                            @foreach(['read', 'create', 'update', 'delete'] as $action)
                                                <td class="text-center">
                                                    @php
                                                        // Cari permission dengan prefix action di modul ini (cth: 'read-user')
                                                        $perm = $modulePermissions->first(function($p) use ($action) {
                                                            return str_starts_with(strtolower($p->name), $action . '-');
                                                        });
                                                    @endphp

                                                    @if($perm)
                                                        <div class="form-check form-switch d-flex justify-content-center m-0">
                                                            <input class="form-check-input perm-checkbox row-check-{{ $moduleSlug }}" 
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $perm->name }}" 
                                                                   id="perm_{{ $perm->id }}"
                                                                   style="cursor: pointer;"
                                                                   {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                                                        </div>
                                                    @else
                                                        <span class="text-muted" style="opacity: 0.5;">-</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            <td class="text-center" style="background-color: #f8f9fa;">
                                                <div class="form-check form-switch d-flex justify-content-center m-0">
                                                    <input class="form-check-input check-row" 
                                                           type="checkbox" 
                                                           data-module-target="row-check-{{ $moduleSlug }}"
                                                           style="cursor: pointer;"
                                                           title="Pilih Semua Akses {{ ucwords($moduleName) }}">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Belum ada data Permission Group yang didaftarkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('settings.group_user.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> {{ $editing ? 'Perbarui Role' : 'Simpan Role' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. Fitur "Pilih Semua" Master (Global)
        const checkAllToggle = document.getElementById('checkAll');
        const allCheckboxes = document.querySelectorAll('.perm-checkbox');
        const rowCheckboxes = document.querySelectorAll('.check-row');

        if (checkAllToggle) {
            checkAllToggle.addEventListener('change', function () {
                let isChecked = this.checked;
                
                // Centang matriks CRUD
                allCheckboxes.forEach(cb => { cb.checked = isChecked; });
                // Centang toggle dikanan
                rowCheckboxes.forEach(cb => { cb.checked = isChecked; });
            });
        }

        // 2. Fitur "Pilih Semua" Per Modul (Baris Horizontal)
        rowCheckboxes.forEach(rowToggle => {
            rowToggle.addEventListener('change', function () {
                // Ambil target class dari data-module-target
                let targetClass = this.getAttribute('data-module-target');
                let isChecked = this.checked;

                // Cari semua toggle CRUD di baris modul tersebut
                let moduleCheckboxes = document.querySelectorAll('.' + targetClass);
                
                moduleCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
            });
        });

    });
</script>
@endsection