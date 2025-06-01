<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        {{-- Logo --}}
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="32" height="32" style="object-fit: cover;">
            {{ config('app.name', 'Ifinance') }}
        </a>
        {{-- Toggle Button --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible Content --}}
        <div class="collapse navbar-collapse" id="mainNavbar">
            {{-- Right Side --}}
            <ul class="navbar-nav ms-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('images/default-avatar.png') }}"
                            alt="Avatar" class="rounded-circle" width="32" height="32">
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">{{ __('Log Out') }}</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth

                @guest
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="nav-link">{{ __('Login') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('register') }}" class="nav-link">{{ __('Register') }}</a>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>