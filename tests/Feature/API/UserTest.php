<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_register()
    {
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response->assertStatus(201)
                ->assertJsonStructure(['token'])
                ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_register_with_invalid_email()
    {
        $this->withoutMiddleware();

        $body = [
            'email' => 'provaprova.it',
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Email is not valid'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_register_without_email()
    {
        $this->withoutMiddleware();

        $body = [
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Missing email'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_register_with_missing_name()
    {
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Missing name'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_register_with_missing_password()
    {
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password_confirmation' => 'password'
        ];
        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Missing password'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_register_with_missing_password_confirmation()
    {
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password',
        ];
        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Missing password confirmation'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_register_with_password_length_less_than_8()
    {
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'pass',
            'password_confirmation' => 'pass'
        ];
        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Password must contain at least 8 characters'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_register_if_passwords_do_not_match()
    {
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password123',
            'password_confirmation' => 'password'
        ];
        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'The password confirmation does not match'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_register_if_email_is_already_taken()
    {
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);

        $response = $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Email is already taken'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_login(){
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);

        $response = $this->postJson('api/v1/login',[
            'email' => 'prova@prova.it',
            'password' => 'password'
        ],['Content-Type' => 'application/json']);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['token'])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_login_with_invalid_email(){
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);

        $response = $this->postJson('api/v1/login',[
            'email' => 'provaprova.it',
            'password' => 'password'
        ],['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Email is not valid'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }
    public function test_cannot_login_with_missing_email(){
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);

        $response = $this->postJson('api/v1/login',[
            'password' => 'password'
        ],['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Missing parameters'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_login_with_missing_password(){
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);

        $response = $this->postJson('api/v1/login',[
            'email' => 'prova@prova.it',
        ],['Content-Type' => 'application/json']);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'code' => 400,
                'title' => 'Bad request',
                'details' => 'Missing parameters'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_login_with_non_existent_user(){
        $this->withoutMiddleware();


        $response = $this->postJson('api/v1/login',[
            'email' => 'prova111@prova.it',
            'password' => 'password'
        ],['Content-Type' => 'application/json']);
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'code' => 404,
                'title' => 'Not found',
                'details' => 'This user does not exist'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_cannot_login_with_invalid_password(){
        $this->withoutMiddleware();

        $body = [
            'email' => 'prova@prova.it',
            'name' => 'prova',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $this->postJson('/api/v1/register',$body,['Content-Type' => 'application/json']);

        $response = $this->postJson('api/v1/login',[
            'email' => 'prova@prova.it',
            'password' => 'peapsoas'
        ],['Content-Type' => 'application/json']);
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'code' => 401,
                'title' => 'Unauthorized',
                'details' => 'The password is invalid'
            ])
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_can_logout(){
        $this->withoutMiddleware();
        $this->withoutExceptionHandling();
        $token = str::random(40);
        $user = User::factory()->create();
        $user->generateToken();
        $user->save();
        $response = $this->postJson('/api/v1/logout', [],
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $user->token
            ]);
        $response
            ->assertStatus(204);
    }
}
