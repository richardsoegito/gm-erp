<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sign in to the Gunung Mas ERP System.">
    <title>Sign in | Gunung Mas ERP</title>
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#4272d7">

    <!-- Fontfaces CSS-->
    <link href="{{ asset('assets/css/font-face.css') }}" rel="stylesheet" media="all">
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="{{ asset('assets/vendor/fontawesome-7.2.0/css/all.min.css') }}" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="{{ asset('assets/vendor/bootstrap-5.3.8.min.css') }}" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}" rel="stylesheet" media="all">
    
    <!-- Main CSS-->
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('assets/css/theme-2026.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('assets/css/css-external.css') }}" rel="stylesheet" media="all">
</head>

<body class="theme-2026 auth-page">
    <main id="auth-form" class="login-wrap">
        <div class="login-content">
            <a href="index.html" class="auth-brand" aria-label="CoolAdmin home">
                <span class="logo-mark" aria-hidden="true">GM</span>
                <span class="logo-text">Gunung Mas</span>
            </a>
            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-subtitle">Sign in to continue to your dashboard.</p>

            <form class="login-form"
                action="{{ route('login.authenticate') }}"
                method="POST">

                @csrf

                <!-- Username -->
                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input id="username"
                        class="au-input @error('username') is-invalid @enderror"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="johndoe"
                        autocomplete="username"
                        required>

                    @error('username')

                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

                <!-- Password -->
                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="position-relative">

                        <input id="password"
                            class="au-input pe-5 @error('password') is-invalid @enderror"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required>

                        <!-- Toggle -->
                        <button type="button"
                                id="toggle-password"
                                class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent shadow-none">

                            <i class="fa-solid fa-eye"></i>

                        </button>

                    </div>

                    @error('password')

                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

                <!-- Submit -->
                <button class="btn btn-primary text-center w-100"
                        type="submit">

                    Sign in

                </button>

            </form>
        </div>
    </main>

    <!-- Jquery JS-->
    <script src="{{ asset('assets/js/vanilla-utils.js') }}"></script>
    <!-- Bootstrap JS-->
    <script src="{{ asset('assets/vendor/bootstrap-5.3.8.bundle.min.js') }}"></script>
    <!-- Vendor JS       -->
    <script src="{{ asset('assets/vendor/chartjs/chart.umd.js-4.5.1.min.js') }}"></script>

    <!-- Main JS-->
    <script src="{{ asset('assets/js/bootstrap5-init.js') }}"></script>
    <script src="{{ asset('assets/js/main-vanilla.js') }}"></script>
    <script src="{{ asset('assets/js/modern-plugins.js') }}"></script>

    <script>
        ready(() => {

            const passwordInput = $('#password');

            const togglePassword = $('#toggle-password');

            const icon = togglePassword.querySelector('i');

            on(togglePassword, 'click', () => {

                const isPassword =
                    passwordInput.type === 'password';

                /*
                |--------------------------------------------------------------------------
                | Toggle Input Type
                |--------------------------------------------------------------------------
                */

                passwordInput.type =
                    isPassword ? 'text' : 'password';

                /*
                |--------------------------------------------------------------------------
                | Toggle Icon
                |--------------------------------------------------------------------------
                */

                icon.classList.toggle('fa-eye');

                icon.classList.toggle('fa-eye-slash');

            });

        });
    </script>
</body>
</html>
