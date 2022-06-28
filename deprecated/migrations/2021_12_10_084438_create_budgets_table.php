<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('budgets', function (Blueprint $table){
            $table->id();
            $table->bigInteger('recid')->nullable();
            $table->bigInteger('father')->nullable();
            $table->string('budget_num')->nullable();
            $table->string('num_cardinal')->nullable();
            //FK:
            $table->unsignedBigInteger('pattern_id')->nullable();
            $table->foreign('pattern_id')->references('id')->on('budget_patterns');
            $table->string('file_name')->nullable();        // Nombre que toma el documento en .pdf
            $table->string('budget_old_num')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->integer('modality')->nullable();    // Indica tipo de presupuesto. Valores en el helper budgetModalities.
            $table->text('description')->nullable();
            $table->text('comments')->nullable();
            //FK:
            $table->bigInteger('form_id')->unsigned()->nullable();
            $table->foreign('form_id')->references('id')->on('budget_forms');
            $table->date('accepted_date')->nullable();
            //FK:
            $table->unsignedBigInteger('accepted_by')->nullable();
            $table->foreign('accepted_by')->references('id')->on('users');
            $table->date('delivery_date')->nullable();
            
            //FK:
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('budget_settings');
            $table->longText('template')->nullable();
            $table->integer('status')->default(0);      // Valores en el helper budgetStatus
            //FK:
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            //FK:
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('budgets');
    }
}
