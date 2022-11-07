<?php

namespace Database\Factories;

use App\Models\Workplace;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkplaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Workplace::class;

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
            'featured' => $this->faker->randomNumber(0),
            'logo' => $this->faker->text(255),
            'address' => $this->faker->text(255),
            'cp' => $this->faker->text(255),
            'nif' => $this->faker->text(255),
            'website' => $this->faker->text(255),
            'description' => $this->faker->sentence(15),
            'status' => $this->faker->randomNumber(0),
            'company_id' => \App\Models\Company::factory(),
            'town_id' => \App\Models\Town::factory(),
        ];
    }
}
