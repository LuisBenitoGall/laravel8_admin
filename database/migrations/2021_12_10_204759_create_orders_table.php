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
            $table->bigInteger('parent')->nullable();
            $table->boolean('is_group')->default(0);

            //About Order:
            $table->string('order_num')->nullable();
            $table->string('num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable();
            $table->foreign('pattern_id')->references('id')->on('order_patterns');
            $table->string('order_old_num')->nullable();

            //About Purchase:
            $table->string('purchase_num')->nullable();
            $table->string('purchase_num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('purchase_pattern_id')->nullable();
            $table->foreign('purchase_pattern_id')->references('id')->on('purchase_patterns');
            $table->string('purchase_old_num')->nullable();
            
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('briefing')->nullable();
            $table->text('observations')->nullable();
            $table->date('customer_confirmation')->nullable();
            //FK:
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->foreign('confirmed_by')->references('id')->on('users');
            $table->date('estimated_delivery_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('confirm_code')->nullable();
            $table->dateTime('confirm_code_date')->nullable();
            $table->boolean('invoiced')->default(0);
            $table->string('pay_order')->nullable();
            $table->timestamp('pay_order_received')->nullable();
            $table->float('bag_amount',12,2)->default(0);   //Para bolsas de pedidos con importe predefinido.
            //FK:
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('order_settings');
            $table->longText('template')->nullable();
            $table->integer('status')->default(0);      // Valores en el helper orderStatus
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
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
