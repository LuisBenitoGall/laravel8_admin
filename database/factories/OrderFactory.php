<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'is_budget' => $this->faker->boolean,
            'is_order' => $this->faker->boolean,
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'recid' => $this->faker->randomNumber,
            'from_customer' => $this->faker->boolean,
            'budget_num' => $this->faker->text(255),
            'budget_num_cardinal' => $this->faker->text(255),
            'budget_old_num' => $this->faker->text(255),
            'modality' => $this->faker->randomNumber(0),
            'accepted_date' => $this->faker->date,
            'budget_status' => $this->faker->randomNumber(0),
            'description' => $this->faker->sentence(15),
            'comments' => $this->faker->text,
            'budget_file_name' => $this->faker->text(255),
            'budget_template' => $this->faker->text,
            'order_parent' => $this->faker->randomNumber,
            'is_group' => $this->faker->boolean,
            'order_num' => $this->faker->text(255),
            'order_num_cardinal' => $this->faker->text(255),
            'order_old_num' => $this->faker->text(255),
            'estimated_delivery_date' => $this->faker->date,
            'delivery_date' => $this->faker->date,
            'briefing' => $this->faker->text,
            'observations' => $this->faker->text,
            'observations_customer' => $this->faker->text,
            'order_template' => $this->faker->text,
            'order_status' => $this->faker->randomNumber(0),
            'pucharse_num' => $this->faker->text(255),
            'pucharse_num_cardinal' => $this->faker->text(255),
            'pucharse_old_num' => $this->faker->text(255),
            'pucharse_doc' => $this->faker->text(255),
            'pay_order' => $this->faker->text(255),
            'pay_order_received' => $this->faker->dateTime,
            'customer_order' => $this->faker->text(255),
            'customer_order_doc' => $this->faker->text(255),
            'customer_team' => $this->faker->text,
            'customer_products' => $this->faker->text,
            'customer_items' => $this->faker->text,
            'customer_confirmation' => $this->faker->dateTime,
            'confirm_code' => $this->faker->text(255),
            'confirm_code_date' => $this->faker->dateTime,
            'provider_team' => $this->faker->text,
            'discount' => $this->faker->randomFloat(2, 0, 9999),
            'discount_amount' => $this->faker->randomNumber(1),
            'bag_amount' => $this->faker->randomNumber(1),
            'invoiced' => $this->faker->boolean,
            'prepare_delivery' => $this->faker->randomNumber(0),
            'old_budget_id' => $this->faker->randomNumber,
            'old_order_id' => $this->faker->randomNumber,
            'budget_pattern_id' => \App\Models\BudgetPattern::factory(),
            'form_id' => \App\Models\BudgetForm::factory(),
            'accepted_by' => \App\Models\User::factory(),
            'budget_template_id' => \App\Models\BudgetSetting::factory(),
            'order_pattern_id' => \App\Models\OrderPattern::factory(),
            'order_template_id' => \App\Models\OrderSetting::factory(),
            'purchase_pattern_id' => \App\Models\PurchasePattern::factory(),
            'customer_id' => \App\Models\Company::factory(),
            'customer_manager_id' => \App\Models\User::factory(),
            'customer_workplace_id' => \App\Models\Workplace::factory(),
            'customer_cost_center_id' => \App\Models\CostCenter::factory(),
            'customer_business_area_id' => \App\Models\BusinessArea::factory(),
            'confirmed_by' => \App\Models\User::factory(),
            'provider_id' => \App\Models\Company::factory(),
            'provider_manager_id' => \App\Models\User::factory(),
            'provider_workplace_id' => \App\Models\Workplace::factory(),
            'provider_cost_center_id' => \App\Models\CostCenter::factory(),
            'provider_business_area_id' => \App\Models\BusinessArea::factory(),
            'final_customer_id' => \App\Models\Company::factory(),
            'final_customer_manager_id' => \App\Models\User::factory(),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
