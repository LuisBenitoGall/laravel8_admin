<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatternFieldsToCompanySettings extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('company_settings', function (Blueprint $table){
            $table->boolean('pattern_budgets')->default(false)->after('production_previsions');
            $table->boolean('pattern_sales')->default(false)->after('pattern_budgets');
            $table->boolean('pattern_purchases')->default(false)->after('pattern_sales');
            $table->boolean('pattern_deliveries')->default(false)->after('pattern_purchases');
            $table->boolean('pattern_projects')->default(false)->after('pattern_deliveries');
            $table->boolean('pattern_invoices')->default(false)->after('pattern_projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('company_settings', function (Blueprint $table){
             $table->dropColumn(['pattern_budgets', 'pattern_sales', 'pattern_purchases', 'pattern_deliveries', 'pattern_projects', 'pattern_invoices']);
        });
    }
}
