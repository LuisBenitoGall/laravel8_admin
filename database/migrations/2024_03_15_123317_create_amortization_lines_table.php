<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmortizationLinesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('amortization_lines', function (Blueprint $table) {
            $table->id();
            //FK:
            $table->unsignedBigInteger('amortization_id')->nullable();
            $table->foreign('amortization_id')->references('id')->on('amortizations');
            $table->float('amount',12,4)->default(0);
            $table->float('percent',10,0)->default(0);
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
        Schema::dropIfExists('amortization_lines');
    }
}
