<nav class="navbar navbar-expand-lg top-navbar">
    <div class="container-fluid">
        <!-- دکمه منو همبرگری -->
        <button class="btn me-3 nev-menu-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>

        <a class="navbar-brand d-flex" href="{{ route('dashboard') }}">
            <!-- لوگو فروشگاه -->
            <img class="nav-logo" src="{{ storeLogo() }}" alt="Logo">
            <!-- نام فروشگاه -->
            <span class="ms-2 fw-bold">
                {{ setting('store_name', '') }}
            </span>
        </a>
        
            @auth

                <div class="dropdown user-menu">

                    <button class="btn user-menu-toggle" type="button" data-bs-toggle="dropdown" 
                        aria-expanded="false">
                        <i class="bi bi-person-circle user-avatar"></i>
                    </button>



                    <ul class="dropdown-menu user-dropdown">

                        <!-- پروفایل -->
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person"></i>
                                <span>پروفایل</span>
                            </a>
                        </li>


                        <!-- تنظیمات -->
                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="bi bi-gear"></i>
                                <span>تنظیمات</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="bi bi-gear"></i>
                                <span>گزینه چهارم</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="bi bi-gear"></i>
                                <span>گزینه پنجم</span>
                            </a>
                        </li>


                        <li>
                            <hr class="dropdown-divider">
                        </li>


                        <!-- خروج -->
                        <li>

                            <form action="{{ route('logout') }}" method="POST" class="m-0">

                                @csrf

                                <button type="submit" class="dropdown-item logout-item">

                                    <i class="bi bi-box-arrow-right"></i>

                                    <span>خروج از سیستم</span>

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            @endauth




        </div>
    </div>
</nav>
