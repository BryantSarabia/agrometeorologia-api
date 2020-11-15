@extends('layouts.app')

@section('content')

        <div class="row justify-content-center align-items-center h-75">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">Create your project</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('project.save') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Project name</label>

                                <div class="col-md-6">
                                    <input id="project_name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="project" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>




                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-success">
                                        Create
                                    </button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

@endsection
