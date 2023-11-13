<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('seats', function (Blueprint $table){
            $table->id();
            //FK:
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('year')->nullable();
            $table->integer('seat_num')->nullable();
            $table->integer('seat_type')->nullable();
            $table->date('seat_date')->nullable();
            $table->string('operation_type')->nullable();
            $table->string('diary')->nullable();
            $table->string('concept')->nullable();
            $table->text('description')->nullable();
            $table->string('doc_num')->nullable();
            $table->integer('sort_num')->nullable();
            //FK: toma el company_id pues puede ser cliente o proveedor con quien company_id hace la gestión.
            $table->unsignedBigInteger('company_target')->nullable();
            $table->foreign('company_target')->references('id')->on('companies');
            $table->string('mode')->nullable();     //Si el registro es Automático (A) o Manual (M)
            //FK:
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->float('base1',12,4)->default(0);
            $table->float('base2',12,4)->default(0);
            $table->float('base3',12,4)->default(0);
            $table->float('iva_percent1',12,4)->default(0);
            $table->float('iva_percent2',12,4)->default(0);
            $table->float('iva_percent3',12,4)->default(0);
            $table->float('iva_amount1',12,4)->default(0);
            $table->float('iva_amount2',12,4)->default(0);
            $table->float('iva_amount3',12,4)->default(0);
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->string('provider_doc')->nullable();
            $table->integer('pay_sign')->nullable();
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
        Schema::dropIfExists('seats');
    }
}
