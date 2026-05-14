<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
</head>
<body>

<div class="page">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    <div class="page-wrapper">

        {{-- Navbar --}}
        @include('partials.navbar')

        {{-- Content --}}
        <div class="page-body">
            <div class="px-5">
                @yield('content')
            </div>
        </div>

        {{-- Footer --}}
        @include('partials.footer')

    </div>
</div>
@yield('scripts')
</body>
</html>