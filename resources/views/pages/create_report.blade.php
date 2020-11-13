@extends('layouts/app')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset("css/map.css") }}"/>
@endsection

@section('content')

    <div class="card">

        <img src="{{ asset('img/pest_report.jpg') }}" class="card-img-top" alt="Pest">

        <div class="card-body">

            <h2 class="card-title text-center">Make a report</h2>

            <div class="row justify-content-center">
                <div class="col-6">
                    <form id="pest-report">
                        <div class="form-group">
                            <label for="name">Pest:</label>
                            <input type="text" name="name" class="form-control" required="required"/>
                        </div>

                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea name="message" class="form-control" required="required"></textarea>
                        </div>

                        {{--MAPPA--}}
                        @include('includes/map')

                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-success">Send</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBwMt8hY7PpmKikcYIxRiH7ApE1roDA6aA&callback=initMap&libraries=&v=weekly"
        defer
    ></script>
    <script src="{{ asset("js/map.js") }}"></script>
@endsection
