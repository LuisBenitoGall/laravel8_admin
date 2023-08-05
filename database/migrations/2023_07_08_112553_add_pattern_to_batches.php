<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatternToBatches extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('batches', function (Blueprint $table) {
            $table->string('num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable()->after('num_cardinal');
            $table->foreign('pattern_id')->references('id')->on('batch_patterns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['num_cardinal', 'pattern_id']);
        });
    }
}
