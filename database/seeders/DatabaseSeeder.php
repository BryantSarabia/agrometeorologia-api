<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
            User::factory()
            ->count(2)
            ->has(Project::factory()->count(2)->has(Request::factory()->count(2)))
            ->create();
    }
}
