<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('products', function (Blueprint $table){
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('ref')->nullable();
            //FK:
            $table->unsignedBigInteger('product_pattern_id')->nullable();
            $table->foreign('product_pattern_id')->references('id')->on('product_patterns');
            $table->string('manual_ref')->nullable();
            $table->text('translations')->nullable();
            $table->text('tags')->nullable();
            $table->text('description')->nullable();
            $table->longText('long_description')->nullable();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('type',1)->nullable();       //p = producto: s = servicio.
            $table->float('price', 10, 2)->default(0);
            $table->float('calculated_by_percent', 5, 2)->nullable();
            $table->float('iva', 4, 2)->nullable();
            $table->boolean('batch')->default(0);
            $table->boolean('stock_management')->default(0);
            $table->boolean('on_sale')->default(1);
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
        Schema::dropIfExists('products');
    }
}
