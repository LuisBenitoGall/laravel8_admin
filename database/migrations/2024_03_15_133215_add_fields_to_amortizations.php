<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAmortizations extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('amortizations', function (Blueprint $table) {
            $table->date('create_date')->nullable()->after('amortization_pendent');
            $table->date('modify_date')->nullable()->after('create_date');
            $table->date('deletion_date')->nullable()->after('modify_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('amortizations', function (Blueprint $table) {
            $table->dropColumn(['create_date', 'modify_date', 'deletion_date']);
        });
    }
}
