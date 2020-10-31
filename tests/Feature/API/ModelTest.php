<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_get_all_models()
    {
        $this->withoutMiddleware();
        $response = $this->get('api/v1/models');
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_run_model_with_undefined_station(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/11212123/models/olive_pheno');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'Station not found'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_run_model_with_invalid_station_id(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/asdad12a/models/olive_pheno');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Invalid ID'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_run_model_with_undefined_model(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1/models/axaxa');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'Model not found'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_can_get_run_model_data(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/stations/1/models/olive_pheno');
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertHeader('Content-Type', 'application/json');
    }
}
