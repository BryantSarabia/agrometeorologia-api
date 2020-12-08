@extends('admin.layouts.app')
@section('content')

    <div class="col-10 offset-2 p-4 main-content bg-transparent">
        <div class="col main-content-box shadow">
            <div class="row">
                <div class="col">
                    <h1 class="main-content-title text-center mt-3">Add a new configuration</h1>
                </div>
            </div>

            <hr>
            <div class="row justify-content-center h-75 ">
                <div class="col-8 main-content-upload">
                    <form method="POST" enctype="multipart/form-data" class="main-content-form" action="{{ route('admin.configuration.save') }}">
                        @csrf
                        <div class="row mb-1">
                            <div class="col-12 text-center text-dark font-weight-bold">
                                <h3>Upload configuration file</h3>
                            </div>
                            @if($errors->any())
                                <div class="col-12 text-center font-weight-bold mb-0">
                                    <div class="alert alert-danger small">
                                            {{ $errors->first() }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-12 upload-box h-50">
                            <div class="row align-items-center h-100">
                                <div class="col text-center files">
                                    <input type="file" name="configuration_file" id="configuration_file" class="@error('configuration_file') is-invalid @enderror" required>
                                </div>
                            </div>


                        </div>
                        <div class="row h-25">
                            <div class="col-12 align-self-end text-center">
                                <button type="submit" class="btn main-form-button">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
