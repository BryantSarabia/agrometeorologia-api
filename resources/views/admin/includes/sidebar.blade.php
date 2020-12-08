<div class="left-side-menu col-2 p-0  h-100 shadow">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.home') }}"><h3>Dashboard</h3></a>
        </div>

        <ul class="list-unstyled components">
            <p>Agrometeorologia</p>

            <li {{Route::currentRouteName() == 'admin.home' ? 'class=active' : ''}}>
                <a href="{{ route('admin.home') }}">Analytics</a>
            </li>

            <li {{Route::currentRouteName() == 'admin.user.all' ? 'class=active' : ''}}>
                <a href="{{ route('admin.user.all') }}">Users</a>
            </li>

            <li {{Route::currentRouteName() == 'admin.project.all' ? 'class=active' : ''}}>
                <a href="{{ route('admin.project.all') }}">Projects</a>
            </li>

            <li {{Route::currentRouteName() == 'admin.configuration.create' || Route::currentRouteName() == 'admin.configuration.all'  ? 'class=active' : ''}}>
                <a href="#meta-api" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Meta API
                    Configurations</a>
                <ul class="collapse list-unstyled" id="meta-api">
                    <li>
                        <a href="{{ route('admin.configuration.create') }}">New configuration</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.configuration.all') }}">All configurations</a>
                    </li>

                </ul>
            </li>

        </ul>
    </nav>
</div>
