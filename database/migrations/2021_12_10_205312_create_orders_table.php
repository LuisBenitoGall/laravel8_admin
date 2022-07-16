<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('orders', function (Blueprint $table){
            $table->id();
            $table->boolean('is_budget')->default(false);
            $table->boolean('is_order')->default(false);
            $table->string('name')->nullable();
            $table->string('slug')->nullable();

            //About Budget:
            $table->bigInteger('recid')->nullable();    //Referencia del budget principal para las versiones.
            $table->string('budget_num')->nullable();
            $table->string('budget_num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('budget_pattern_id')->nullable();
            $table->foreign('budget_pattern_id')->references('id')->on('budget_patterns');
            $table->string('budget_old_num')->nullable();
            $table->integer('modality')->nullable();    // Indica tipo de presupuesto. Valores en el helper budgetModalities.
            //FK:
            $table->bigInteger('form_id')->unsigned()->nullable();
            $table->foreign('form_id')->references('id')->on('budget_forms');
            $table->date('accepted_date')->nullable();
            //FK:
            $table->unsignedBigInteger('accepted_by')->nullable();
            $table->foreign('accepted_by')->references('id')->on('users');
            $table->integer('budget_status')->default(0);      // Valores en el helper budgetStatus
            $table->text('description')->nullable();
            $table->text('comments')->nullable();
            $table->string('budget_file_name')->nullable();
            //FK:
            $table->unsignedBigInteger('budget_template_id')->nullable();
            $table->foreign('budget_template_id')->references('id')->on('budget_settings');
            $table->longText('budget_template')->nullable();

            //About Order:
            $table->bigInteger('order_parent')->nullable();
            $table->boolean('is_group')->default(0);
            $table->string('order_num')->nullable();
            $table->string('order_num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('order_pattern_id')->nullable();
            $table->foreign('order_pattern_id')->references('id')->on('order_patterns');
            $table->string('order_old_num')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->text('briefing')->nullable();
            $table->text('observations')->nullable();
            $table->text('observations_customer')->nullable();
            //FK:
            $table->unsignedBigInteger('order_template_id')->nullable();
            $table->foreign('order_template_id')->references('id')->on('order_settings');
            $table->longText('order_template')->nullable();
            $table->integer('order_status')->default(0);      // Valores en el helper orderStatus

            //About Purchase:
            $table->string('purchase_num')->nullable();
            $table->string('purchase_num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('purchase_pattern_id')->nullable();
            $table->foreign('purchase_pattern_id')->references('id')->on('purchase_patterns');
            $table->string('purchase_old_num')->nullable();
            $table->string('purchase_doc')->nullable();
            $table->string('pay_order')->nullable();
            $table->timestamp('pay_order_received')->nullable();

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
            //FK:
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->foreign('confirmed_by')->references('id')->on('users');
            $table->string('confirm_code')->nullable();
            $table->dateTime('confirm_code_date')->nullable();

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
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->float('discount', 5, 2)->nullable();
            $table->float('discount_amount', 11, 2)->nullable();
            $table->float('bag_amount',12,2)->default(0);   //Para bolsas de pedidos con importe predefinido.
            $table->boolean('invoiced')->default(false);

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
        Schema::dropIfExists('orders');
    }
}
