{{-- <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
            <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu"
            aria-expanded="false"
            aria-label="Toggle navigation"
            >
            <span class="navbar-toggler-icon"></span>
            </button>
        <div class="navbar-brand navbar-brand-autodark">
            <h2>GM ERP</h2>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="./">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-1"
                    >
                        <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg></span>
                    <span class="nav-link-title"> Home </span>
                </a>
                </li>
                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle"
                        href="#navbar-base"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="false"
                        role="button"
                        aria-expanded="false"
                    >
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-1">

                                <path d="M12 3c-4.97 0 -9 1.79 -9 4s4.03 4 9 4s9 -1.79 9 -4s-4.03 -4 -9 -4" />
                                <path d="M3 7v10c0 2.21 4.03 4 9 4s9 -1.79 9 -4v-10" />
                                <path d="M3 12c0 2.21 4.03 4 9 4s9 -1.79 9 -4" />
                            </svg>
                        </span>
                        <span class="nav-link-title"> Master </span>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item">Product</a>
                                <a class="dropdown-item">Product Category</a>
                                <a class="dropdown-item">Product Brand</a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</aside> --}}
<aside class="menu-sidebar" id="main-sidebar">
    <div class="logo">
        <a href="index.html" class="logo-link" aria-label="CoolAdmin home">
            <span class="logo-mark" aria-hidden="true">GM</span>
            <span class="logo-text">GUNUNG MAS</span>
        </a>
        <button class="sidebar-close js-sidebar-toggle" type="button" aria-label="Close navigation">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <li class="{{ request()->is('/') ? 'active' : '' }}">
                    <a href="{{ url('/') }}"><i class="fa-solid fa-tachometer-alt"></i>Dashboard</a>
                </li>
                <li class="has-sub {{ request()->is('master/*') ? 'active' : '' }}">
                    <a class="js-arrow {{ request()->is('master/*') ? 'open' : '' }}" href="#">
                        <i class="fas fa-database"></i>
                        Master
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list"
                        style="{{ request()->is('master/*') ? 'display:block;' : 'display:none;' }}">

                        <li class="{{ request()->is('master/user*') ? 'active' : '' }}">
                            <a href="{{ route('master.user.index') }}">User</a>
                        </li>
                        <li><a href="data-table.html">Product</a></li>
                        <li class="{{ request()->is('master/categories*') ? 'active' : '' }}">
                            <a href="{{ route('master.category.index') }}">Categories</a>
                        </li>
                        <li class="{{ request()->is('master/brands*') ? 'active' : '' }}">
                            <a href="{{ route('master.brand.index') }}">Brands</a>
                        </li>
                        <li class="{{ request()->is('master/units*') ? 'active' : '' }}">
                            <a href="{{ route('master.unit.index') }}">Units</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>