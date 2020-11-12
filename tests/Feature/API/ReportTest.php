<?php

namespace Tests\Feature\API;

use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_report_when_logged_in()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'name' => 'Hydrocotyle',
            'message' => 'Consiglio di eliminare il prima possibile questo infestante',
            'coordinates' => [
                'lat' => 41.12,
                'lon' => 42.1121
            ]
        ];
        $response = $this->actingAs($user)->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(201)
            ->assertExactJson([
                'id' => "1",
                'user_id' => (string) $user->id,
                'name' => 'Hydrocotyle',
                'message' => 'Consiglio di eliminare il prima possibile questo infestante',
                'coordinates' => [
                    'lat' => 41.12,
                    'lon' => 42.1121
                ],
                'created_at' => date('Y-m-d')
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_report_with_api_key()
    {
        $this->withoutMiddleware();
        $token = str::random(40);
        $user = User::factory()->hasProjects(1,['api_key' => $token])->create();
        $body = [
            'name' => 'Hydrocotyle',
            'message' => 'Consiglio di eliminare il prima possibile questo infestante',
            'coordinates' => [
                'lat' => 41.12,
                'lon' => 42.1121
            ]
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token )->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(201)
            ->assertExactJson([
                'id' => "1",
                'user_id' => (string) $user->id,
                'name' => 'Hydrocotyle',
                'message' => 'Consiglio di eliminare il prima possibile questo infestante',
                'coordinates' => [
                    'lat' => 41.12,
                    'lon' => 42.1121
                ],
                'created_at' => date('Y-m-d')
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_report_when_not_logged_in(){
        $this->withoutMiddleware();
        $body = [
            'name' => 'Hydrocotyle',
            'message' => 'Consiglio di eliminare il prima possibile questo infestante',
            'coordinates' => [
                'lat' => 41.12,
                'lon' => 42.1121
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'code' => 401,
                'title' => 'Unauthorized',
                'details' => 'You must be logged in or have an API Key'
            ])
            ->assertHeader('Content-Type','application/json');
    }

    public function test_cannot_report_with_latitude_bigger_than_90(){
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'user_id' => $user->id,
            'name' => 'Hydrocotyle',
            'message' => 'Consiglio di eliminare il prima possibile questo infestante',
            'coordinates' => [
                'lat' => 91.2145,
                'lon' => 42.1121
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => "Bad request",
                'details' => "Parameter lat must be a number between -90 and 90"
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_report_with_latitude_bigger_than_minus_90(){
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'user_id' => $user->id,
            'name' => 'Hydrocotyle',
            'message' => 'Consiglio di eliminare il prima possibile questo infestante',
            'coordinates' => [
                'lat' => -91.2145,
                'lon' => 42.1121
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => "Bad request",
                'details' => "Parameter lat must be a number between -90 and 90"
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_report_with_longitude_bigger_than_180(){
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'user_id' => $user->id,
            'name' => 'Hydrocotyle',
            'message' => 'Consiglio di eliminare il prima possibile questo infestante',
            'coordinates' => [
                'lat' => 43.2145,
                'lon' => 180.248
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => "Bad request",
                'details' => "Parameter lon must be a number between -180 and 180"
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_report_with_longitude_bigger_than_minus_180(){
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'user_id' => $user->id,
            'name' => 'Hydrocotyle',
            'message' => 'Consiglio di eliminare il prima possibile questo infestante',
            'coordinates' => [
                'lat' => 43.2145,
                'lon' => -180.248
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => "Bad request",
                'details' => "Parameter lon must be a number between -180 and 180"
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_report_with_missing_pest_name(){
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'user_id' => $user->id,
            'message' => 'Consiglio di eliminare il prima possibile questo infestante',
            'coordinates' => [
                'lat' => 41.12,
                'lon' => 42.1121
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Name must be a string'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_report_with_missing_message(){
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'user_id' => $user->id,
            'name' => 'Hydrocotyle',
            'coordinates' => [
                'lat' => 41.12,
                'lon' => 42.1121
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Message must be a string'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_report_with_missing_coordinates_longitude(){
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'user_id' => $user->id,
            'name' => 'Hydrocotyle',
            'message' => 'Messaggio di prova',
            'coordinates' => [
                'lat' => 41.12
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lon must be a float number'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_report_with_missing_coordinates_latitude(){
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $body = [
            'user_id' => $user->id,
            'name' => 'Hydrocotyle',
            'message' => 'Messaggio di prova',
            'coordinates' => [
                'lon' => 41.12
            ]
        ];
        $response = $this->postJson('api/v1/pests/reports',$body);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lat must be a float number'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_get_reports_with_missing_latitude(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/pests/reports');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lat must be a float number'
            ]);
    }
    public function test_cannot_get_reports_with_missing_longitude(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/pests/reports?lat=41.21');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter lon must be a float number'
            ]);
    }

    public function test_cannot_get_reports_with_missing_radius(){
        $this->withoutMiddleware();
        $response = $this->get('api/v1/pests/reports?lat=41.21&lon=41.121');
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Parameter radius must be number'
            ]);
    }

    public function test_can_get_reports(){
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $date = new DateTime();
        $report1 = Report::factory()->create([
            'user_id' => $user->id,
            'created_at' => $date->modify("-2 day")->format('Y-m-d H:i:s')
        ]);
        $report2 = Report::factory()->create([
            'user_id' => $user->id,
            'created_at' => $date->modify("-1 day")->format('Y-m-d H:i:s')
        ]);
        $report3 = Report::factory()->create([
            'user_id' => $user->id,
            'created_at' => $date->format('Y-m-d H:i:s')
        ]);
        $response = $this->get('api/v1/pests/reports?lat=42.0029881&lon=13.8472536&radius=5000000');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertHeader('Content-Type','application/json');
    }
}
