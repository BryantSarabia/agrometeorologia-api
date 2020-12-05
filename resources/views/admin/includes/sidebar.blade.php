<div class="left-side-menu col-2 p-0  h-100 shadow">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.home') }}"><h3>Dashboard</h3></a>
        </div>

        <ul class="list-unstyled components">
            <p>Agrometeorologia</p>

            <li {{Route::currentRouteName() == 'admin.home' ? 'class=active' : ''}}>
                <a href="#analytics" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Analytics</a>
                <ul class="collapse list-unstyled" id="analytics">
                    <li>
                        <a href="{{ route('admin.home') }}">All Analytics</a>
                    </li>
                    <li>
                        <a href="#">Home 2</a>
                    </li>
                    <li>
                        <a href="#">Home 3</a>
                    </li>
                </ul>
            </li>

            <li {{Route::currentRouteName() == 'admin.configuration.create' || Route::currentRouteName() == 'admin.configuration.all'  ? 'class=active' : ''}}>
                <a href="#meta-api" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Meta API Configurations</a>
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
