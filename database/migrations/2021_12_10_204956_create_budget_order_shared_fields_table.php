<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetOrderSharedFieldsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('budget_order_shared_fields', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->foreign('budget_id')->references('id')->on('budgets');
            //FK:
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');

            //About Customer:
            //FK:
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('customer_manager_id')->nullable();
            $table->foreign('customer_manager_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('customer_workplace_id')->nullable();
            $table->foreign('customer_workplace_id')->references('id')->on('workplaces');
            //FK:
            $table->unsignedBigInteger('customer_cost_center_id')->nullable();
            $table->foreign('customer_cost_center_id')->references('id')->on('cost_centers');
            //FK:
            $table->unsignedBigInteger('customer_business_area_id')->nullable();
            $table->foreign('customer_business_area_id')->references('id')->on('business_areas');
            $table->string('customer_order')->nullable();   // Order Customer
            $table->string('customer_order_doc')->nullable();   // Order Customer doc
            $table->text('customer_team')->nullable();
            $table->text('customer_products')->nullable();

            //About Provider:
            //FK:
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('provider_manager_id')->nullable();
            $table->foreign('provider_manager_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('provider_workplace_id')->nullable();
            $table->foreign('provider_workplace_id')->references('id')->on('workplaces');
            //FK:
            $table->unsignedBigInteger('provider_cost_center_id')->nullable();
            $table->foreign('provider_cost_center_id')->references('id')->on('cost_centers');
            //FK:
            $table->unsignedBigInteger('provider_business_area_id')->nullable();
            $table->foreign('provider_business_area_id')->references('id')->on('business_areas');
            $table->text('provider_team')->nullable();

            //About Final Customer:
            //FK:
            $table->unsignedBigInteger('final_customer_id')->nullable();
            $table->foreign('final_customer_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('final_customer_manager_id')->nullable();
            $table->foreign('final_customer_manager_id')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('third_part_budget')->nullable();
            $table->foreign('third_part_budget')->references('id')->on('budgets');

            $table->float('free_amount', 9, 2)->nullable();
            $table->float('discount', 5, 2)->nullable();
            $table->float('discount_amount', 11, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('budget_order_shared_fields');
    }
}
