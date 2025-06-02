<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            {{ config('app.name', 'Ifinance') }} &nbsp; &nbsp; &nbsp; |
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            {{-- Left side --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="text-primary nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        {{ __('Dashboard') }}
                    </a>
                <li class="nav-item">
                    <a class="text-primary nav-link" href="{{ route('transactions.index') }}">📒 Nhập Thu Chi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary" href="{{ route('congno.index') }}">
                        📌 Quản lý Công Nợ 
                    </a>
                </li>

            </ul>


            {{-- Right side --}}
            <ul class="navbar-nav ms-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <div style="width: 32px; height: 32px; overflow: hidden; border-radius: 50%;">
                            <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('images/avatar.png') }}"
                                alt="Avatar"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span class="text-dark">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                {{ __('Hồ sơ') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('wallets.index') }}">
                                {{ __('Tài chính') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('transactions.history') }}">
                                {{ __('Lịch sử giao dịch') }}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">{{ __('Đăng xuất') }}</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth

                @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Đăng nhập') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Đăng ký') }}</a>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>