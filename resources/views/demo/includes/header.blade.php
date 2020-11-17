<nav class="navbar navbar-expand-md  navbar-dark bg-success shadow-sm">
    <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name', 'Laravel') }}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'demo.home' ? 'active' : '' }}" href="{{ route('demo.home') }}">Home</a>
            </li>

            @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reports
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('demo.report.create') }}">Make Report</a>
                        <a class="dropdown-item" href="{{ route('demo.report.index') }}">All reports</a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'demo.locations' ? 'active' : '' }}" href="{{ route('demo.locations') }}">My Locations</a>
                </li>
            @endauth

        </ul>

        <ul class="navbar-nav ml-auto">
            @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit()
                                                                  localStorage.removeItem('token');">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('demo.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>

            @endauth
            @guest
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'login' ? 'active' : '' }}" href="{{ route('demo.login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'register' ? 'active' : '' }}" href="{{ route('demo.register') }}">Register</a>
                </li>
            @endguest
        </ul>
    </div>
</nav>
