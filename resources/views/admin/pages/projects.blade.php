@extends('admin.layouts.app')
@section('content')

    <div class="col-10 offset-2 p-4 main-content bg-transparent">
        <div class="col main-content-box shadow">
            <div class="row">
                <div class="col">
                    <h1 class="main-content-title text-center mt-3">All projects</h1>

                </div>
            </div>

            <hr>
            <div class="row main-content-table justify-content-center h-75">
                <div class="col-12">
                    @if($errors->any())
                        <div class="alert-danger">
                            <span>
                                {{$errors->first()}}
                            </span>
                        </div>
                    @endif
                    @if($projects->count() > 0)

                        @if(Session::has('message'))
                            <div class="alert-success">
                                <span>
                                    {{Session::get('message')}}
                                </span>
                            </div>
                        @endif
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">User</th>
                                <th scope="col">License</th>
                                <th scope="col">Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($projects as $project)
                                <tr>
                                    <th scope="row">{{$project->id}}</th>
                                    <td>{{$project->name}}</td>
                                    <td>{{$project->user->name}}</td>
                                    <th>{{strtoupper($project->license)}}</th>
                                    <td>
                                        <a href="#"
                                           class="btn btn-danger delete_project" data-id="{{$project->id}}">Delete</a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    @else
                        <div class="row alert alert-danger h-100">
                            <div class=" col-12 align-self-center p-0  text-center">
                                <h3 class="text-dark">No content</h3>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="row  justify-content-center align-items-end mb-1">
                    <div class="col">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
