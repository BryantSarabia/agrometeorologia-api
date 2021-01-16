@extends('layouts/app')

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/swagger-ui.css') }}">
@endsection

@section('content')
    <div id="swagger-ui"></div>

@endsection

@section('scripts')
    @parent
    <script src="{{ asset('js/swagger-ui-bundle.js') }}"></script>
    <script src="{{ asset('js/swagger-ui-standalone-preset.js') }}"></script>
    <script>
        window.onload = function () {
            const ui = SwaggerUIBundle({
                @if(!isset($url))
                url: "{{ asset('OpenAPI-spec/struttura-api.json') }}",
                @else
                url: "{{ $url }}",
                @endif
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ]
            })

            window.ui = ui
        }
    </script>
@endsection
