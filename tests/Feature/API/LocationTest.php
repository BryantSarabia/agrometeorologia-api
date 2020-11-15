<?php

namespace Tests\Feature\API;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_save_location()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat'=> 41.12,
                'lon' => 41.12121,
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(201)
            ->assertExactJson([
                'id' => (string) 1,
                'user_id' => (string) $user->id,
                'coordinates' => [
                    'lat' => 41.12,
                    'lon' => 41.12121
                ],
                'radius' => 40
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_missing_latitude()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lon' => 41.12121,
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lat must be a float number'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_latitude_bigger_than_90()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => 91,
                'lon' => 41.12121,
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lat must be a number between -90 and 90'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_latitude_less_than_minus_90()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => -91,
                'lon' => 41.12121,
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lat must be a number between -90 and 90'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_missing_longitude()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => 41.12,
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lon must be a float number'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_longitude_bigger_than_180()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => 41.12,
                'lon' => 181.12,
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lon must be a number between -180 and 180'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_longitude_less_than_minus_180()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => 41.12,
                'lon' => -181.12,
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lon must be a number between -180 and 180'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_invalid_longitude()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => 41.12,
                'lon' => "invalid lon"
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lon must be a float number'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_invalid_latitude()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => "invalid lat",
                'lon' => 41.12121
            ],
            'radius' => 40
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lat must be a float number'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_missing_radius()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => 41.12,
                'lon' => 41.12121
            ],
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter radius must be number'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_cannot_save_location_with_invalid_radius()
    {
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'coordinates' => [
                'lat' => 41.12,
                'lon' => 41.12121
            ],
            'radius' => "invalid radius"
        ];

        $response = $this->postJson(
            'api/v1/me/locations',
            $body,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter radius must be number'
            ])
            ->assertHeader('Content-Type','application/json');

    }

    public function test_can_delete_a_location(){
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();

        $location = Location::create([
            'user_id' => $user->id,
            'lat' => 41.12,
            'lon' => 41.12121,
            'radius' => 20
        ]);

        $response = $this->deleteJson(
            'api/v1/me/locations/' . $location->id,
            [],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->dump()
            ->assertStatus(204);
    }

    public function test_user_cannot_delete_a_location_if_does_not_belong_to_him(){
        $this->withoutMiddleware();

        $token = str::random(30);
        $user1 = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $token = str::random(30);
        $user2 = User::factory()->hasProjects(1,['api_key' => $token])->create();

        $location = Location::create([
            'user_id' => $user1->id,
            'lat' => 41.12,
            'lon' => 41.12121,
            'radius' => 20
        ]);

        $response = $this->deleteJson(
            'api/v1/me/locations/' . $location->id,
            [],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'code' => 401,
                'title' => 'Unauthorized',
                'details' => 'This location does not belongs to you'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_user_can_delete_all_locations(){
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();

        Location::create([
            'user_id' => $user->id,
            'lat' => 41.12,
            'lon' => 41.12121,
            'radius' => 20
        ]);

        Location::create([
            'user_id' => $user->id,
            'lat' => 41.12,
            'lon' => 41.12121,
            'radius' => 20
        ]);

        $response = $this->deleteJson(
            'api/v1/me/locations',
            [],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(204);
    }

    public function test_user_cannot_delete_all_locations_if_he_does_not_have_locations(){
        $this->withoutMiddleware();
        $token = str::random(30);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();


        $response = $this->deleteJson(
            'api/v1/me/locations',
            [],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'You have no locations to delete'
            ])
            ->assertHeader('Content-Type','application/json');
    }
}
