<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    @include('includes.head')
    </head>

    <body>
        <div id="app" class="container">

            @include('includes.header')

            <main class="py-4">
                @yield('content')
            </main>


            <footer>
                @include('includes.footer')
            </footer>

            {{--SCRIPTS--}}
            @include('includes.scripts')

        </div>

    </body>
</html>
