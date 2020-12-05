<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('admin.includes.head')
<!-- API Specification -->
    @yield('css')
</head>

<body>
@include('admin.includes.header')
<div id="app" class="container-fluid h-100">

    <div class="row h-100">

        @include('admin.includes.sidebar')
        @yield('content')

        <footer>
            @include('admin.includes.footer')
        </footer>
    </div>

    {{--SCRIPTS--}}
    @include('admin.includes.scripts')
    @yield('scripts')

</div>

</body>
</html>
