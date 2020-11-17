@extends('demo.layouts.app')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset("css/map.css") }}"/>
    <style>

        #map {
            height: 40vw;
        }

        aside {
            height: 40vw;
        }
    </style>
@endsection

@section('content')


    <div class="row mt-2 mb-1">
        <div class="col-3">
            <aside class="border border-success rounded shadow p-2">

                <div class="row">
                    <div class="col text-center">
                        <h1>Get Reports</h1>
                        <small class="text-danger">Click on the map to set the desired location</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <form id="get-reports">
                            <div class="form-group row justify-content-between">
                                <label class="col-1 col-form-label">From</label>
                                <div class="col-10">
                                    <input type="text" name="from" class="form-control form-control-sm datepicker"
                                           autocomplete="off"
                                           value="{{ date('Y-m-d', strtotime( date('Y-m-d') . " - 1 month")) }}">
                                    <small class="form-text text-muted">Default: last 30 days</small>
                                </div>
                            </div>

                            <div class="form-group row justify-content-between mb-4">
                                <label class="col-1 col-form-label">To</label>
                                <div class="col-10">
                                    <input type="text" name="to" class="form-control form-control-sm datepicker"
                                           autocomplete="off" value="{{ date('Y-m-d') }}">
                                    <small class="form-text text-muted">Default: Current day</small>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group row mt-4">
                                <div class="col">
                                    <span><strong>Marker</strong>: Click on the map to set the marker and get the reports of that zone</span>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <label class="col-4 col-form-label pr-0">Marker radius</label>
                                <div class="col-8 pl-0">
                                    <input name="radius" type="range" id="radius" class="form-control" min="1" max="100"
                                           value="20">
                                    <small class="form-text text-center">20 km</small>
                                </div>
                            </div>


                            <div class="row justify-content-center mb-2">

                                <div class="col-6 text-center">
                                    <a id="hide-locations" class="btn btn-primary" role="button" href="#" data-toggle="tooltip" data-placement="top" title="Click here to hide your saved locations">Hide locations</a>
                                </div>

                            </div>

                            <div class="col text-center mt-4">
                                <input type="submit" value="Get reports" class="btn btn-success">
                            </div>
                        </form>
                    </div>

                </div>


            </aside>
        </div>
        <div class="col-9">
            {{--MAPPA--}}
            @include('demo.includes.map')
        </div>
    </div>


@endsection

@section('scripts')
    @parent
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&callback=initMap&libraries=&v=weekly"
        defer
    ></script>
    <script src="{{ asset("js/map.js") }}"></script>
    <script>
        $('#app').removeClass('container').addClass('container-fluid');
    </script>
@endsection
