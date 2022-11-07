<?php

namespace Database\Factories;

use App\Models\BudgetForms as BudgetForm;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFormFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BudgetForm::class;

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
            'conditions' => $this->faker->text,
            'labor_journeys' => $this->faker->text,
            'status' => $this->faker->randomNumber(0),
        ];
    }
}
