<?php

namespace Database\Factories;

use App\Models\CostCenter;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CostCenterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CostCenter::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'status' => $this->faker->randomNumber(0),
        ];
    }
}
