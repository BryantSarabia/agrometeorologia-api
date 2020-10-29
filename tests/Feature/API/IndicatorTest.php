<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndicatorTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_get_all_indicators()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/indicators');

        $response
            ->assertJsonStructure([
                'data'
            ])
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_get_stations_indicator_values()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/indicators/1');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_get_stations_indicator_values_with_invalid_id(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/indicators/1a');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Invalid ID'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_find_stations_indicator(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/indicators/111111111');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'Indicator not found'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_can_get_single_indicator(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/indicators/1');
        $response
            ->assertJsonStructure([
                'data'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_find_single_indicator(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/indicators/112312321');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'Indicator not found'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_can_get_station_indicator(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1/indicators/1');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_get_station_indicator_if_station_has_not_data_about_the_indicator(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/7/indicators/1');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'This station has no data about this indicator'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_get_data_if_station_does_not_exist(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/71231212/indicators/1');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'Station not found'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_get_data_if_indicator_does_not_exist(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1/indicators/112311');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'Indicator not found'
            ])
            ->assertHeader('Content-Type','application/json');
    }
}
