<nav class="navbar bg-white z-3">
    <div class="container">
        <a class="navbar-brand" href="{{ route('landing') }}">
            <img src="{{ asset(config('meta.logo')) }}" alt="logo">
            <b class="nav-title text-primary text-sm">Mahakarya Agency</b>
        </a>
        <div>
            <div class="d-flex align-items-center">
                @if (Auth::check())
                    @if (request()->routeIs(['landing', 'jeje.index']))
                        <a class="btn btn-outline-primary" href="{{ Auth::user()->role->redirect }}">
                            Beranda
                        </a>
                    @else
                        <a class="btn btn-danger" href="#!" id="logout-btn">
                            Keluar
                        </a>
                    @endif
                @else
                    <a class="btn btn-primary" href="{{ route('login') }}">
                        Masuk
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>
