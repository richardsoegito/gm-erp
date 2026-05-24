@extends('layouts.app')

@section('title', $editing ? 'Ubah Pengguna' :  'Tambah Pengguna Baru')

@section('content')

<div class="row">
    <div class="card">
        <div class="card-header">
            {{ $editing ? 'Ubah Pengguna' : 'Tambah Pengguna Baru' }}
        </div>
        <form id="user-form" action="{{ $editing ? route('master.user.update', $user) : route('master.user.store') }}"
            method="POST"
            enctype="multipart/form-data">
            @csrf
            @if ($editing)
                @method('put')
            @endif

            <div class="card-body">

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="status" class="form-control-label">
                            Status
                        </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="status" {{ $editing ? ($user->status ? 'checked' : '') : 'checked' }}>
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>
                    </div>
                    <!-- Name -->
                    <div class="col-md-6 mb-3">

                        <label for="name" class="form-control-label">
                            Nama Lengkap
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            id="name"
                            name="name"
                            placeholder="Masukkan Nama Lengkap"
                            value="{{ $editing ? old('name', $user->name) : old('name') }}"
                            class="form-control @error('name') is-invalid @enderror">

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- Username -->
                    <div class="col-md-6 mb-3">

                        <label for="username" class="form-control-label">
                            Username
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan Username"
                            value="{{ $editing ? old('username', $user->username) : old('username') }}"
                            class="form-control @error('username') is-invalid @enderror">

                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">

                        <label for="email" class="form-control-label">
                            Email
                            <span class="text-danger">*</span>
                        </label>

                        <input type="email"
                            id="email"
                            name="email"
                            placeholder="Masukkan Email"
                            value="{{ $editing ? old('email', $user->email) : old('email') }}"
                            class="form-control @error('email') is-invalid @enderror">

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-3">

                        <label for="phone" class="form-control-label">
                            Nomor Telepon
                        </label>

                        <input type="text"
                            id="phone"
                            name="phone"
                            placeholder="Masukkan Nomor Telepon"
                            value="{{ $editing ? old('phone', $user->phone) : old('phone') }}"
                            class="form-control @error('phone') is-invalid @enderror">

                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- Password -->
                    <div class="col-md-6 mb-3 {{ $editing ? 'd-none' : '' }}">

                        <label for="password" class="form-control-label">
                            Kata Sandi
                            <span class="text-danger">*</span>
                        </label>

                        <input type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan Kata Sandi"
                            class="form-control @error('password') is-invalid @enderror">
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-6 mb-3 {{ $editing ? 'd-none' : '' }}">

                        <label for="password_confirmation"
                            class="form-control-label">

                            Konfirmasi Kata Sandi
                            <span class="text-danger">*</span>

                        </label>

                        <input type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Konfirmasi Kata Sandi"
                            class="form-control @error('password_confirmation') is-invalid @enderror">

                        @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- Role -->
                    <div class="col-md-6 mb-3">

                        <label for="role_id">
                            Role
                            <span class="text-danger">*</span>
                        </label>

                        <select name="role_id"
                            id="role_id"
                            class="@error('role_id') is-invalid @enderror">

                            @foreach ($roles as $role)

                                <option value="{{ $role->id }}"
                                    {{
                                        old(
                                            'role_id',
                                            $editing
                                                ? $user->roles->first()?->id
                                                : ''
                                        ) == $role->id
                                            ? 'selected'
                                            : ''
                                    }}>
                                    
                                    {{ $role->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('role_id')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- Profile Picture -->
                    <div class="col-md-6 mb-3">

                        <label for="profile_picture" class="form-control-label">
                            Foto Profil
                        </label>

                        <!-- Preview -->
                        <div class="mb-3">

                            <img id="preview-image"
                                src="{{ $editing ? ($user->avatar ? asset('storage/profile-picture/' . $user->avatar) : 'https://placehold.co/120x120?text=Tanpa+Foto') : 'https://placehold.co/120x120?text=Tanpa+Foto' }}"
                                alt="Preview"
                                class="img-thumbnail"
                                style="width:120px; height:120px; object-fit:cover;">

                        </div>

                        <!-- Input -->
                        <input type="file"
                            id="profile_picture"
                            name="profile_picture"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="form-control @error('profile_picture') is-invalid @enderror">

                        @error('profile_picture')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <!-- Help -->
                        <small class="text-muted d-block mt-2">
                            Format file yang diizinkan:
                            JPG, JPEG, PNG, WEBP
                        </small>

                        <small class="text-muted d-block">
                            Ukuran maksimal unggahan: 5 MB
                        </small>

                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="card-footer">
                <a href="javascript:history.back();" class="btn btn-secondary">

                    <i class="fa-solid fa-chevron-left"></i>

                    Kembali

                </a>
                <button type="submit"
                    id="submit-btn"
                    class="btn btn-primary">

                <span class="btn-text">

                    <i class="fa-regular fa-dot-circle"></i>

                    {{ $editing ? 'Perbarui Pengguna' : 'Simpan Pengguna' }}

                </span>

                <span class="btn-loading d-none">

                    <i class="fa-solid fa-spinner fa-spin"></i>

                    Memproses...

                </span>

            </button>

            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <!-- jQuery -->
    <script>
        ready(() => {

            /*
            |--------------------------------------------------------------------------
            | Profile Picture Preview
            |--------------------------------------------------------------------------
            */

            on('#profile_picture', 'change', function () {

                const file = this.files[0];

                if (!file) return;

                // Allowed MIME Types
                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                // Max Upload Size (5MB)
                const maxSize = 5 * 1024 * 1024;

                /*
                |--------------------------------------------------------------------------
                | Validate File Type
                |--------------------------------------------------------------------------
                */

                if (!allowedTypes.includes(file.type)) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Hanya file JPG, JPEG, PNG, dan WEBP yang diizinkan.'
                    });

                    this.value = '';

                    $('#preview-image').src =
                        'https://placehold.co/120x120?text=Tanpa+Foto';

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Validate File Size
                |--------------------------------------------------------------------------
                */

                if (file.size > maxSize) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Ukuran maksimal unggahan adalah 5 MB.'
                    });

                    this.value = '';

                    $('#preview-image').src =
                        'https://placehold.co/120x120?text=Tanpa+Foto';

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Image Preview
                |--------------------------------------------------------------------------
                */

                const reader = new FileReader();

                reader.onload = (e) => {

                    $('#preview-image').src = e.target.result;

                };

                reader.readAsDataURL(file);

            });

            /*
            |--------------------------------------------------------------------------
            | Tom Select
            |--------------------------------------------------------------------------
            */

            const roleSelect = $('#role_id');

            if (roleSelect) {
                new TomSelect(roleSelect, {
                    create: false,
                    placeholder: 'Pilih Role',
                    allowEmptyOption: true,
                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    }
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Form Loading Submit
            |--------------------------------------------------------------------------
            */

            const form = $('#user-form');

            const submitBtn = $('#submit-btn');

            if (form && submitBtn) {

                on(form, 'submit', function () {

                    /*
                    |--------------------------------------------------------------------------
                    | Disable Button
                    |--------------------------------------------------------------------------
                    */

                    submitBtn.disabled = true;

                    /*
                    |--------------------------------------------------------------------------
                    | Change Text
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
                    | Add Loading Class
                    |--------------------------------------------------------------------------
                    */

                    form.classList.add('form-loading');

                });

            }
        });
    </script>
@endsection