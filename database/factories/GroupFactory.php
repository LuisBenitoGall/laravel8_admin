<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Group;
use Illuminate\Support\Str;

class GroupFactory extends Factory{
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(){
        $name = $this->faker->name();

        return [
            'company_id' => 1,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->text(),
            'status' => 1
        ];
    }
}
