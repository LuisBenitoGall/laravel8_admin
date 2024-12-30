<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCorrectiveActionToIncidenceMessages extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('incidence_messages', function (Blueprint $table) {
            $table->boolean('corrective_action')->default(false)->after('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('incidence_messages', function (Blueprint $table) {
            $table->dropColumn(['corrective_action']);
        });
    }
}
