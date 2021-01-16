@extends('layouts/app')

@section('content')
    <div class="col-12 p-4 mt-3 main-content bg-transparent">
        <div class="col main-content-box shadow">
            <div class="row">
                <div class="col">
                    <h1 class="main-content-title text-center mt-3">All configurations</h1>
                </div>
            </div>

            <div class="row main-content-table justify-content-center h-75">
                <div class="col-12">
                    @if($configurations->count() > 0)
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Group</th>
                                <th scope="col">Service</th>
                                <th scope="col">API Specification</th>
                                <th scope="col">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th scope="row">0</th>
                                <td>API</td>
                                <td>Agroambiente</td>
                                <td>
                                    <a href="{{ route('api.specification.show') }}"
                                       class="btn btn-success" target="_blank">Show</a>
                                </td>
                                <td>
                                    Enabled
                                </td>
                            </tr>
                            @foreach($configurations as $configuration)
                                <tr>
                                    <th scope="row">{{$configuration->id}}</th>
                                    <td>{{json_decode($configuration->configuration)->group}}</td>
                                    <td>{{json_decode($configuration->configuration)->service}}</td>
                                    <td>
                                        <a href="{{ route('api.specification.show', ['id' => $configuration->id]) }}"
                                           class="btn btn-success" target="_blank">Show</a>
                                    </td>
                                    <td>
                                        {{ $configuration->enabled ? 'Enabled' : 'Disabled' }}
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
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
