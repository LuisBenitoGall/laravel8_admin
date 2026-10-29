<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameWorkplaceIdColumnToCustomerProviders extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->renameColumn('workplace_id', 'customer_workplace_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('customer_providers', function (Blueprint $table){
            $table->renameColumn('customer_workplace_id', 'workplace_id');
        });
    }
}
