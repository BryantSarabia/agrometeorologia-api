@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">My projects</div>

                    <div class="card-body">
                        @if (session('created'))
                            <div class="alert alert-success" role="alert">
                                {{ session('created') }}
                            </div>
                        @endif


                        @forelse($projects as $project)
                            <div class="project">
                                <div class="row">
                                    <div class="col">
                                        <h4>{{$project->name}}</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6 key_field">
                                                <input type="text" class="form-control"
                                                       placeholder="xxxx-xxxx-xxxx-xxxx" disabled>
                                            </div>

                                            <div class="col-2">
                                                <button class="btn btn-success {{$project->api_key ? 'show_key' : 'generate'}}" name="generate" type="button" data-id="{{$project->id}}">
                                                    {{$project->api_key ? 'Show' : 'Generate'}}
                                                </button>
                                            </div>

                                            @if($project->api_key)
                                            <div class="col-2">
                                                <button class="btn btn-warning refresh" name="generate" type="button" data-id="{{$project->id}}">
                                                    Refresh
                                                </button>
                                            </div>
                                            @endif
                                            <div class="col-1">
                                                <a class="btn btn-danger project_delete" name="delete" href="{{route('project.delete',['project' => $project->id])}}" data-id="{{$project->id}}">Delete
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @empty
                                <p>Click <a href="{{route('project.create')}}">here</a> to create a new project!</p>
                        @endforelse

                            <div class="d-flex justify-content-center">
                                {!! $projects->links() !!}
                            </div>
                    </div>
                </div>
            </div>
        </div>

    {{--MODALS--}}
        @include('modals.delete_project')
        @include('modals.confirm_password')
    </div>
@endsection
