<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListUserTest extends TestCase
{
    use RefreshDatabase;

    /* @test */
    public function test_can_fetch_single_user()
    {
        $user = User::factory(User::class)->create();

        $response = $this->getJson('api/v1/user/'.$user->getRouteKey());

        $response->assertSee($user->name);
    }
}
