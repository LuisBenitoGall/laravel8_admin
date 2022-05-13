<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySettingsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('company_settings', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            //FK:
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->boolean('customers_management')->default(0);
            $table->boolean('providers_management')->default(0);
            $table->boolean('validate_nif')->default(1);
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('base_color_budgets')->nullable();
            $table->string('base_color_orders')->nullable();
            $table->string('base_color_invoices')->nullable();
            $table->float('iva', 4, 2)->nullable();
            $table->text('ip')->nullable();
            $table->longText('emails')->nullable();
            $table->longText('public_info')->nullable();
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
        Schema::dropIfExists('company_settings');
    }
}
