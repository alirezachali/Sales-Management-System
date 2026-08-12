<nav class="navbar navbar-expand-lg top-navbar">
    <div class="container-fluid">
        <!-- List Button -->
        <button class="btn me-3" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
        <!-- Brand Logo & Name -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ storeLogo() }}" alt="Logo" height="45">
            <!-- Store Name -->
            <span class="ms-2 fw-bold">
                {{ setting('store_name', '') }}
            </span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <!-- Live Clock -->
            <span class="me-4" id="liveClock"></span>
            @auth
                <!-- User Name & Profile icon -->
                <span class="me-4">
                    <!-- Profile icon -->
                    <i class="bi bi-person-circle"></i>
                    <!-- User name -->
                    {{ auth()->user()->name }}
                </span>
                <!-- Logout Request Form -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <!-- Logout Button -->
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i>
                        خروج
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>
