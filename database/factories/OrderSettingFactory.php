<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\OrderSettings as OrderSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderSetting::class;

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
            'status' => $this->faker->randomNumber(0),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
