<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\BudgetPatterns as BudgetPattern;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetPatternFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BudgetPattern::class;

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
            'pattern' => $this->faker->text(255),
            'yearly_reset' => $this->faker->boolean,
            'status' => $this->faker->randomNumber(0),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
