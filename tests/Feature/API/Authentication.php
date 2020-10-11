<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Tests\TestCase;

class Authentication extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAuthentication()
    {
        $this->withoutExceptionHandling();
        $token = str::random(40);

        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->json('GET','api/v1/users/'.$user->id);


        $response
            ->assertExactJson([
            'message' => 'Unauthorized',
            ]);
    }
}
