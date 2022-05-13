<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrdersTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('work_orders', function (Blueprint $table){
            $table->id();
            $table->string('work_order_num')->nullable();
            $table->string('num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable();
            $table->foreign('pattern_id')->references('id')->on('work_order_patterns');
            $table->string('work_order_old_num')->nullable();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->date('delivery_date')->nullable();
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->boolean('simulation')->default(false);      //Indica si la orden es simulada o no. Sólo las NO simuladas entran en el proceso de producción y logística.
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('work_orders');
    }
}
