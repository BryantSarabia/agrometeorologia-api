<?php

namespace Tests\Feature\API\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;


    public function test_can_fetch_single_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson(route('api.v1.users.show', $user));

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string)$user->getRouteKey(),
                'attributes' => [
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'links' => [
                    'self' => route('api.v1.users.show', $user)
                ]
            ]
        ]);
    }

    public function test_can_fetch_list_user()
    {
        $users = User::factory()->times(3)->create();

        $response = $this->getJson(route('api.v1.users.index'));

        $response->assertExactJson([
            'data' => [
                [
                    'type' => 'users',
                    'id' => (string)$users[0]->getRouteKey(),
                    'attributes' => [
                        'name' => $users[0]->name,
                        'email' => $users[0]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.users.show', $users[0])
                    ]
                ],
                [
                    'type' => 'users',
                    'id' => (string)$users[1]->getRouteKey(),
                    'attributes' => [
                        'name' => $users[1]->name,
                        'email' => $users[1]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.users.show', $users[1])
                    ]
                ],
                [
                    'type' => 'users',
                    'id' => (string)$users[2]->getRouteKey(),
                    'attributes' => [
                        'name' => $users[2]->name,
                        'email' => $users[2]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.users.show', $users[2])
                    ]
                ],
            ],
            'links' => [
                'self' => route('api.v1.users.index')
            ],
            meta => [
                'users_count' => 3
            ]
        ]);
    }
}
