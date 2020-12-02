<?php

namespace Tests\Feature\API;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Tests\TestCase;

class Authorization extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_fetch_data_with_api_key()
    {
        $this->withMiddleware(['api_key']);
        $user = User::factory()->create();
        $project = Project::factory()->forUser([
            'name' => $user->name
        ])->create();
        $response = $this->withHeader('Authorization', 'Bearer '. $project->api_key)->get('api/v1/stations');
        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type','application/json');
    }


    public function test_cannot_fetch_data_without_api_key(){
        $this->withMiddleware(['api_key']);

        $response = $this->get('api/v1/stations');
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'code' => 401,
                'title' => 'Unauthorized',
                'details' => 'Incorrect API Key, please try again'
            ])
            ->assertHeader('Content-Type','application/json');
    }
}
