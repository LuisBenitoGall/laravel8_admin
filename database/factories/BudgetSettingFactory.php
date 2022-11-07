<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\BudgetSettings as BudgetSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BudgetSetting::class;

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
            'payment_method' => $this->faker->text,
            'observations' => $this->faker->text,
            'footer_legend' => $this->faker->text,
            'featured' => $this->faker->randomNumber(0),
            'status' => $this->faker->numberBetween(0, 8388607),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
