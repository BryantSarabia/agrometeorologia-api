@extends('demo.layouts.app')

@section('content')

    <div id="home-carousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner h-100">
            <div class="carousel-item active">
                <img class="d-block w-100" src="{{ asset("img/api.jpg") }}" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                    <h3>API Documentation</h3>
                    <a href="{{ route('api.specification') }}" class="btn home-button mx-2">Documentation</a>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ asset("img/pest.jpg") }}" alt="Second slide">
                <div class="carousel-caption d-none d-md-block">
                    <h3 class="p-2">Pest reports</h3>
                    <a href="{{ route('demo.report.create') }}" class="btn home-button mx-2">Make a report</a>
                    <a href="{{ route('demo.report.create') }}" class="btn home-button mx-2">See all reports</a>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ asset("img/location.jpg") }}" alt="Third slide">
                <div class="carousel-caption d-none d-md-block">
                    <h3>Locations</h3>
                    <a href="{{ route('demo.locations') }}" class="btn home-button mx-2">My locations</a>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#home-carousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#home-carousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
@endsection

@section('scripts')
    <script>
        $('#app').removeClass('container').addClass('container-fluid p-0')
    </script>
@endsection
