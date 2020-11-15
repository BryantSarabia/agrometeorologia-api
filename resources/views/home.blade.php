@extends('layouts/app')

{{--@section('css')--}}
{{--    <style>--}}
{{--        body, html{--}}
{{--            height: 80%;--}}
{{--        }--}}
{{--    </style>--}}
{{--@endsection--}}

@section('content')

    <div id="home-carousel" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#home-carousel" data-slide-to="0" class="active"></li>
            <li data-target="#home-carousel" data-slide-to="1"></li>
            <li data-target="#home-carousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner h-100">
            <div class="carousel-item active">
                <img class="d-block w-100" src="{{ asset("img/pest_report.jpg") }}" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>First slide label</h5>
                    <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ asset("img/pest_report.jpg") }}" alt="Second slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Second slide label</h5>
                    <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ asset("img/pest_report.jpg") }}" alt="Third slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Third slide label</h5>
                    <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
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
