<nav class="top-navbar" dir="rtl"> 
    <div class="navbar-inner">

        {{-- سمت راست: دکمه منوی همبرگری + برند فروشگاه --}}
        <div class="navbar-section">

            {{-- دکمه باز/بستن سایدبار (با Alpine که همراه Livewire بارگذاری می‌شود) --}}
            <button type="button" class="nav-icon-btn" @click="toggleSidebar()"
                :class="{ 'is-active': sidebarCollapsed }" title="باز و بستن منو" aria-label="باز و بستن منو">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img class="nav-logo" src="{{ storeLogo() }}" alt="Logo">
                <span class="brand-name">{{ setting('store_name', 'فروشگاه') }}</span>
            </a>
        </div>

        {{-- سمت چپ: منوی کاربر --}}
        @auth
            <div class="navbar-section">
                @php $avatarUrl = auth()->user()->avatar_url; @endphp

                <div class="user-menu" x-data="{ open: false }" @keydown.escape.window="open = false">

                    {{-- دکمه‌ی باز کردن منو با کلیک روی تصویر پروفایل --}}
                    <button type="button" class="user-menu-toggle" @click="open = !open"
                        :class="{ 'is-open': open }" aria-haspopup="true" :aria-expanded="open.toString()">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" class="user-avatar-img" alt="avatar">
                        @else
                            <span class="user-avatar-fallback"><i class="bi bi-person"></i></span>
                        @endif
                        <span class="user-menu-name d-none d-md-inline">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down user-menu-caret"></i>
                    </button>

                    {{-- منوی بازشونده --}}
                    <div class="user-dropdown" x-show="open" x-cloak x-transition
                        @click.outside="open = false">

                        {{-- سربرگ اطلاعات کاربر --}}
                        <div class="user-dropdown-head">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" class="dropdown-avatar" alt="avatar">
                            @else
                                <span class="dropdown-avatar dropdown-avatar-fallback">
                                    <i class="bi bi-person"></i>
                                </span>
                            @endif
                            <div class="dropdown-user-meta">
                                <div class="dropdown-user-name">{{ auth()->user()->name }}</div>
                                <div class="dropdown-user-role">
                                    {{ auth()->user()->role?->display_name ?? 'کاربر' }}
                                </div>
                            </div>
                        </div>

                        {{-- سوییچ تغییر زبان فارسی / انگلیسی --}}
                        <div class="lang-switch">
                            <span class="lang-switch-label">زبان برنامه</span>
                            <div class="lang-switch-btns" role="group" aria-label="تغییر زبان">
                                <a href="{{ route('locale.switch', 'fa') }}"
                                    class="lang-btn {{ app()->getLocale() === 'fa' ? 'active' : '' }}"
                                    title="فارسی" aria-label="فارسی">
                                    <img src="{{ asset('images/flags/ir.svg') }}" class="flag" alt="فارسی">
                                </a>
                                <a href="{{ route('locale.switch', 'en') }}"
                                    class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}"
                                    title="English" aria-label="English">
                                    <img src="{{ asset('images/flags/us.svg') }}" class="flag" alt="English">
                                </a>
                            </div>
                        </div>

                        {{-- سوییچ تغییر تم روشن / تیره --}}
                        <div class="theme-switch">
                            <span class="theme-switch-label">حالت نمایش</span>
                            <div class="theme-switch-btns" role="group" aria-label="تغییر تم">
                                <button type="button" class="theme-btn" @click="setTheme('light')"
                                    :class="{ 'active': theme === 'light' }" title="حالت روشن"
                                    aria-label="حالت روشن">
                                    <i class="bi bi-sun-fill"></i>
                                </button>
                                <button type="button" class="theme-btn" @click="setTheme('dark')"
                                    :class="{ 'active': theme === 'dark' }" title="حالت تیره"
                                    aria-label="حالت تیره">
                                    <i class="bi bi-moon-stars-fill"></i>
                                </button>
                            </div>
                        </div>

                        <div class="dropdown-divider-line"></div>

                        {{-- پروفایل --}}
                        <a class="user-dropdown-item" href="#">
                            <i class="bi bi-person"></i>
                            <span>پروفایل</span>
                        </a>

                        {{-- تنظیمات --}}
                        <a class="user-dropdown-item" href="{{ route('settings.index') }}">
                            <i class="bi bi-gear"></i>
                            <span>تنظیمات</span>
                        </a>

                        <div class="dropdown-divider-line"></div>

                        {{-- خروج از سیستم --}}
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="user-dropdown-item logout-item">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>خروج از سیستم</span>
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        @endauth

    </div>
</nav>
