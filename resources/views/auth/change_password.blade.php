@extends('layouts.app')

@section('title', 'Change Password')

@section('content')

<div class="row">

    <div class="col-lg-6">

        <div class="card">

            <!-- Header -->
            <div class="card-header">

                <strong>
                    Change Password
                </strong>

            </div>

            <!-- Form -->
            <form action="{{ route('change_password.update') }}"
                method="POST">

                @csrf
                @method('PUT')

                <div class="card-body">
                    
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show animate__animated animate__bounceInLeft" role="alert">
                            <i class="fa-solid fa-circle-check" style="margin-top: 5px; margin-right:5px;"></i>
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Current Password -->
                    <div class="mb-3">

                        <label for="current_password"
                            class="form-control-label">

                            Current Password
                            <span class="text-danger">*</span>

                        </label>

                        <div class="position-relative">

                            <input type="password"
                                id="current_password"
                                name="current_password"
                                placeholder="Enter Current Password"
                                class="form-control pe-5 @error('current_password') is-invalid @enderror">

                            <button type="button"
                                class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent shadow-none toggle-password">

                                <i class="fa-solid fa-eye"></i>

                            </button>

                        </div>

                        @error('current_password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- New Password -->
                    <div class="mb-3">

                        <label for="password"
                            class="form-control-label">

                            New Password
                            <span class="text-danger">*</span>

                        </label>

                        <div class="position-relative">

                            <input type="password"
                                id="password"
                                name="password"
                                placeholder="Enter New Password"
                                class="form-control pe-5 @error('password') is-invalid @enderror">

                            <button type="button"
                                class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent shadow-none toggle-password">

                                <i class="fa-solid fa-eye"></i>

                            </button>

                        </div>

                        @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">

                        <label for="password_confirmation"
                            class="form-control-label">

                            Confirm Password
                            <span class="text-danger">*</span>

                        </label>

                        <div class="position-relative">

                            <input type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Confirm Password"
                                class="form-control pe-5">

                            <button type="button"
                                class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent shadow-none toggle-password">

                                <i class="fa-solid fa-eye"></i>

                            </button>

                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="card-footer d-flex justify-content-between">

                    <a href="javascript:history.back()"
                        class="btn btn-secondary">

                        <i class="fa-solid fa-chevron-left"></i>

                        Back

                    </a>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="fa-solid fa-key"></i>

                        Update Password

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@section('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | Toggle Password
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.toggle-password')
        .forEach((button) => {

            button.addEventListener('click', function () {

                const input = this.parentElement
                    .querySelector('input');

                const icon = this.querySelector('i');

                if (input.type === 'password') {

                    input.type = 'text';

                    icon.classList.remove('fa-eye');

                    icon.classList.add('fa-eye-slash');

                } else {

                    input.type = 'password';

                    icon.classList.remove('fa-eye-slash');

                    icon.classList.add('fa-eye');

                }

            });

        });

</script>

@endsection