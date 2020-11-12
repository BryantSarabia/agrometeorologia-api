@extends('layouts/app',['api' => true])

@section('content')
    <div id="swagger-ui"></div>
    <script src="{{ asset('js/swagger-ui-bundle.js') }}"></script>
    <script src="{{ asset('js/swagger-ui-standalone-preset.js') }}"></script>
    <script>
        window.onload = function () {
            const ui = SwaggerUIBundle({
                url: "{{ asset('OpenAPI-spec/struttura-api.json') }}",
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
