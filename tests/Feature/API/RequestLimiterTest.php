<?php

namespace Tests\Feature\API;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestLimiterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cannot_fetch_data_if_exceed_monthly_limit(){
        $this->withMiddleware(['limit']);
        $date = date('Y-m-d', strtotime(date('Y-m-01') . ' + 1 month'));
        $message = 'You have exceeded your monthly limit, please retry after: ' . $date;
        $project = Project::factory()->hasRequests(1,['number' => (Project::MAX_REQUESTS_BASIC + 1 )])->create(['license' => 'basic']);
        $response = $this->withHeader('Authorization', 'Bearer '. $project->api_key)->get('api/v1/stations');
        $response
            ->assertStatus(429)
            ->assertExactJson([
                'code' => 429,
                'title' => 'Too many requests',
                'details' => $message
            ])
            ->assertHeader('Content-Type','application/json');

        $project = Project::factory()->hasRequests(1,['number' => (Project::MAX_REQUESTS_PRO + 1)])->create(['license' => 'pro']);
        $response = $this->withHeader('Authorization', 'Bearer '. $project->api_key)->get('api/v1/stations');
        $response
            ->assertStatus(429)
            ->assertExactJson([
                'code' => 429,
                'title' => 'Too many requests',
                'details' => $message
            ])
            ->assertHeader('Content-Type','application/json');
    }


}
