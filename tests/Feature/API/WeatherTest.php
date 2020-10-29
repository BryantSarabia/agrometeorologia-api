<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WeatherTest extends TestCase
{

    public function test_can_get_station_weather()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1/weather');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_get_station_weather_with_query_parameters(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1/weather?from=2020-10-10&to=2020-10-20');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_get_station_weather_with_from_bigger_than_to(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1/weather?from=2020-10-10&to=2020-10-09');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => "parameter from cannot be bigger than parameter to"
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_get_station_weather_with_invalid_id(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1sad1');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => "Invalid ID"
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_not_found_station_weather(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/11231231/weather');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => "Station not found or has not data"
            ])
            ->assertHeader('Content-Type', 'application/json');
    }
}
