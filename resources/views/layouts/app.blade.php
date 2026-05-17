<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ERP Gunung Mas - Enterprise Resources Planning System, Online Store Gunung Mas, Gunung Mas Online Store">
    <title>@yield('title') | ERP Gunung Mas</title>
    <meta property="og:type" content="ERP Gunung Mas - Enterprise Resources Planning System">
    <meta property="og:title" content="ERP Gunung Mas - Enterprise Resources Planning System">
    <meta property="og:description" content="Modern Bootstrap 5 admin dashboard with Chart.js widgets, responsive tables, and clean typography.">

    <meta name="theme-color" content="#4272d7">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    {{-- Animate CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <title>@yield('title')</title>

    @yield('styles')
</head>
<body class="theme-2026">

<div class="page">

    
    <div class="page-wrapper">
        
        {{-- Navbar --}}
        @include('partials.navbar')
        {{-- Sidebar --}}
        @include('partials.sidebar')
        {{-- Content --}}
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <button class="sidebar-toggle js-sidebar-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="main-sidebar">
                                <i class="fa-solid fa-bars" aria-hidden="true"></i>
                            </button>
                            <form class="form-header" role="search" onsubmit="return false">
                                <i class="fa-solid fa-magnifying-glass form-header__icon" aria-hidden="true"></i>
                                <input class="au-input au-input--xl" type="search" name="search" placeholder="Search anything…" aria-label="Search">
                                <kbd class="form-header__hint" aria-hidden="true">⌘K</kbd>
                            </form>
                            <div class="header-button">
                                <div class="account-wrap">
                                    <div class="account-item clearfix js-item-menu" role="button" tabindex="0" aria-haspopup="true" aria-label="Account menu">
                                        <div class="image">
                                            <img src="{{ auth()->user()->avatar ? asset('storage/profile-picture/' . auth()->user()->avatar) : asset('assets/images/default-avatar.webp') }}" alt="{{ auth()->user()->name }}" />
                                        </div>
                                        <div class="content">
                                            <a class="js-acc-btn" href="#">{{ auth()->user()->name }}</a>
                                        </div>
                                        <div class="account-dropdown js-dropdown">
                                            <div class="info clearfix">
                                                <div class="content">
                                                    <h5 class="name">
                                                        <a href="#">{{ auth()->user()->name }}</a>
                                                    </h5>
                                                    <span class="email">{{ auth()->user()->email }}</span>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__body">
                                                <div class="account-dropdown__item">
                                                    <a href="{{ route('master.user.edit', auth()->user()->uuid) }}">
                                                        <i class="fa-solid fa-user"></i>Account</a>
                                                </div>
                                                <div class="account-dropdown__item">
                                                    <a href="{{ route('change_password.index') }}">
                                                        <i class="fa-solid fa-arrows-rotate"></i>Change Password</a>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__footer">
                                                <a href="#"
                                                id="btn-logout">

                                                    <i class="fa-solid fa-power-off"></i>
                                                    Logout

                                                </a>
                                                <!-- Hidden Logout Form -->
                                                <form id="logout-form"
                                                    action="{{ route('logout') }}"
                                                    method="POST"
                                                    class="d-none">

                                                    @csrf

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- HEADER DESKTOP-->
            <main id="main-content" class="main-content">
                <div class="section__content mt-3">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
                
                {{-- Footer --}}
                @include('partials.footer')
            </main>
        </div>

    </div>
</div>

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

        on('#btn-logout', 'click', (e) => {

            e.preventDefault();

            Swal.fire({

                title: 'Logout Confirmation',

                text: 'Are you sure you want to logout?',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Yes, Logout',

                confirmButtonColor: "#DC3545",

                cancelButtonText: 'Cancel',

                reverseButtons: true,

            }).then((result) => {

                if (result.isConfirmed) {

                    $('#logout-form').submit();

                }

            });

        });

    });
</script>
@yield('scripts')
</body>
</html>