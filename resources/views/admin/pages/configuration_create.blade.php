@extends('admin.layouts.app')
@section('content')

    <div class="col-10 p-4 main-content">
        <div class="col main-content-box shadow">
            <div class="row">
                <div class="col">
                    <h1 class="main-content-title text-center mt-3">Add a new configuration</h1>
                </div>
            </div>

            <hr>
            <div class="row justify-content-center h-75">
                <div class="col-8">
                        <form method="POST" enctype="multipart/form-data" class="main-content-form">
                            <div class="form-group">
                                <label>Configuration file:</label>
                                <input type="file" name="configuration_file">
                            </div>
                            <div class="form-group">
                                <label>Enabled</label>
                                <input type="checkbox" name="enabled">
                            </div>
                            <div class="col-12  text-center">
                                <button type="submit" class="btn main-form-button">Send</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>

@endsection
