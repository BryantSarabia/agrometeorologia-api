@extends('admin.layouts.app')
@section('content')

    <div class="col-10 p-4 main-content bg-transparent">
        <div class="col main-content-box shadow">
            <div class="row">
                <div class="col">
                    <h1 class="main-content-title text-center mt-3">All configurations</h1>

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
                    @if($configurations->count() > 0)

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
                                <th scope="col">Group</th>
                                <th scope="col">Service</th>
                                <th scope="col">Configuration file</th>
                                <th scope="col">Status</th>
                                <th scope="col">Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($configurations as $configuration)
                                <tr>
                                    <th scope="row">{{$configuration->id}}</th>
                                    <td>{{json_decode($configuration->configuration)->group}}</td>
                                    <td>{{json_decode($configuration->configuration)->service}}</td>
                                    <td>
                                        <a href="{{ route('admin.configuration.show', ['id' => $configuration->id]) }}"
                                           class="btn main-form-button" target="_blank">Show</a>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input change_status"
                                                   data-id="{{$configuration->id}}"
                                                   id="{{$configuration->id}}" {{ $configuration->enabled ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                   for="{{$configuration->id}}">{{ $configuration->enabled ? 'Disable' : 'Enable' }}</label>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#"
                                           class="btn btn-danger delete_configuration" data-id="{{$configuration->id}}">Delete</a>
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
                        {{ $configurations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
