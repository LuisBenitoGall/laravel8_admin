<?php

namespace Database\Factories;

use App\Models\Province;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProvinceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Province::class;

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
            'country_id' => \App\Models\Country::factory(),
        ];
    }
}
