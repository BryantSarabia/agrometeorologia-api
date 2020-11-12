<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{  asset('js/custom.js') }}"></script>

@if($api ?? '')
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
@endif
