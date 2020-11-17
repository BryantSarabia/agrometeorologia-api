<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('includes.head')
    <!-- API Specification -->
    @yield('css')
</head>

<body>
@include('demo.includes.header')
<div id="app" class="container">

    @yield('content')

    <footer>
        @include('includes.footer')
    </footer>

    {{--SCRIPTS--}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset("js/jquery-ui.js") }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{  asset('js/demo.js') }}"></script>
    @yield('scripts')

</div>

</body>
</html>
