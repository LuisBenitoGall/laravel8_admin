<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('invoices', function (Blueprint $table){
            $table->id();
            $table->bigInteger('refund')->nullable();
            $table->string('invoice_num')->nullable();
            $table->string('num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable();
            $table->foreign('pattern_id')->references('id')->on('invoice_patterns');
            $table->string('invoice_old_num')->nullable();
            //FK:
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->float('amount',12,2)->default(0);
            $table->float('with_iva',5,2)->nullable();
            $table->float('amount_with_iva',12,2)->default(0);
            $table->date('fiscal_date')->nullable();
            $table->date('expiration_date')->nullable();
            //FK:
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->longText('provider_observations')->nullable();
            $table->longText('customer_observations')->nullable();
            //FK:
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('invoice_settings');
            $table->longText('template')->nullable();
            $table->integer('printed')->default(0);
            $table->integer('status')->default(0);
            $table->boolean('blocked')->default(false);
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
        Schema::dropIfExists('invoices');
    }
}
