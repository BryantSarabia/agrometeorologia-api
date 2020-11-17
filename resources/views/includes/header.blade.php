<nav class="navbar navbar-expand-md  navbar-dark bg-success shadow-sm">
    <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name', 'Laravel') }}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{Route::currentRouteName() == 'api.specification' ? 'active' : ''}}" href="{{ route('api.specification') }}">API Specification</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{Route::currentRouteName() == 'pricing' ? 'active' : ''}}" href="#">Pricing</a>
            </li>



            @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Projects
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="{{ route('project.create') }}">New Project</a>
                        <a class="dropdown-item" href="{{ route('project.index') }}">My projects</a>
                    </div>
                </li>

            @endauth

            <li class="nav-item">
                <a class="nav-link" href="{{ route('demo.home') }}">Demo</a>
            </li>

        </ul>

        <ul class="navbar-nav ml-auto">
            @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>

            @endauth
            @guest
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'login' ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'register' ? 'active' : '' }}" href="{{ route('register') }}">Register</a>
                </li>
            @endguest
        </ul>
    </div>
</nav>
