@extends('layouts/app')
@section('content')

    <div class="card">

            <img src="{{ asset('img/pest_report.jpg') }}" class="card-img-top" alt="Pest">

        <div class="card-body">

            <h2 class="card-title text-center">Make a report</h2>

            <div class="row justify-content-center">
                <div class="col-6">
                    <form>
                        <div class="form-group">
                            <label for="name">Pest:</label>
                            <input type="text" name="name" class="form-control" required="required"/>
                        </div>

                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea name="message" class="form-control" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Send</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
