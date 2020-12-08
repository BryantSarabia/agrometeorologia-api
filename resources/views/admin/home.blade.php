@extends('admin.layouts.app')
@section('content')

    <div class="analytics col-10 offset-2">
        <div class="row justify-content-around h-25 mt-5">
            <a href="{{ route('admin.user.all') }}" class="entity">
                <div class="total-box row col-12 p-5 h-75">
                    <div class="total-icon col-12 text-center">
                        <i class="fas fa-users fa-3x"></i>
                    </div>

                    <div class="total-number col-12 text-center">
                        {{$users}}
                    </div>
                    <div class="total-header col-12 text-center">
                        Users
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.project.all') }}" class="entity">
                <div class="total-box row col-12 p-5 h-75">
                    <div class="total-icon col-12 text-center">
                        <i class="fas fa-project-diagram fa-3x"></i>
                    </div>

                    <div class="total-number col-12 text-center">
                        {{$projects}}
                    </div>

                    <div class="total-header col-12 text-center">
                        Projects
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.configuration.all') }}" class="entity">
                <div class="total-box row col-12 p-5 h-75">
                    <div class="total-icon col-12 text-center">
                        <i class="fas fa-wrench fa-3x"></i>
                    </div>

                    <div class="total-number col-12 text-center">
                        {{$configurations}}
                    </div>

                    <div class="total-header col-12 text-center">
                        Configurations
                    </div>
                </div>
            </a>

        </div>

        <div class="row">
            <div class="col-12 mt-3" style="position: relative;">
                <canvas id="apiUsage" width="200" height="300"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-3" style="position: relative;">
                <canvas id="endpointUsage" width="200" height="300"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-6" style="position: relative;">
                <canvas id="projectUsage" width="200" height="300"></canvas>
            </div>

            <div class="col-6" style="position: relative;">
                <canvas id="userUsage" width="200" height="300"></canvas>
            </div>
        </div>

    </div>
@endsection

@section('scripts')

    <script src="{{ asset("js/analytics.js") }}"></script>

@endsection
