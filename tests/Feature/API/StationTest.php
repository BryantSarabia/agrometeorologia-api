<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_get_all_stations()
    {
        $this->withoutMiddleware();
        $response = $this->get('/api/v1/stations');
        $response
            ->assertJsonStructure(['data'])
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_get_all_stations_filtered_by_province()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations?province=aq');
        $response
            ->assertJsonStructure(['data'])
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_get_all_stations_filtered_by_invalid_province()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations?province=1212231');
        $response
            ->assertJsonMissing(['data'], true)
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_get_data_from_undefined_source()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations?source=provaprova');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Undefined source'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_get_a_station()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_get_a_station_with_invalid_id()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1sa');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Invalid ID'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_not_found_station()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/12312312313');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'Station not found'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

}
