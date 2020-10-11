<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Request::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'project_id' => Project::factory(),
            'endpoint' => $this->faker->randomElement(['/api/v1/models','/api/v1/meteo','/api/v1/crops']),
            'number' => $this->faker->numberBetween(0,100),
            'date'  => carbon::now(),
        ];
    }
}
