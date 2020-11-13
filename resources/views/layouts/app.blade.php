<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('includes.head')
<!-- API Specification -->
    @yield('css')
</head>

<body>
@include('includes.header')
<div id="app" class="container">

    <main class="py-4">
        @yield('content')
    </main>


    <footer>
        @include('includes.footer')
    </footer>

    {{--SCRIPTS--}}
    @include('includes.scripts')
    @yield('scripts')

</div>

</body>
</html>
