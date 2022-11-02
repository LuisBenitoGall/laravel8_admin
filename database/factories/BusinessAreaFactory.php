<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\BusinessAreas as BusinessArea;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessAreaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BusinessArea::class;

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
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
