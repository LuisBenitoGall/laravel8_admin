<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(){
        $name = $this->faker->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'company_id' => 1,
            'user_id' => 1,
            'type' => 'p',
            'price' => $this->faker->numberBetween(1, 100),
            'on_sale' => 1,
            'status' => 1
        ];
    }
}
