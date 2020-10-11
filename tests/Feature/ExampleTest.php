<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample(){

        $password = Crypt::encryptString('hola');
        $password1 = Crypt::decryptString($password);
        dump($password);

    }
}
