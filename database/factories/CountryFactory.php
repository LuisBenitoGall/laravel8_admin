<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

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
            'code' => $this->faker->text(255),
            'alfa2' => $this->faker->word(2),
            'alfa3' => $this->faker->word(3),
            'flag' => $this->faker->text(255),
            'status' => $this->faker->randomNumber(0),
        ];
    }
}
