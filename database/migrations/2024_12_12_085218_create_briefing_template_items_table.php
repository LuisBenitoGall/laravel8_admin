<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBriefingTemplateItemsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('briefing_template_items', function (Blueprint $table){
            $table->id();
            $table->bigInteger('recid')->nullable();
            //FK:
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('briefing_templates');
            $table->text('name')->nullable();
            $table->text('translations')->nullable();
            $table->text('observations')->nullable();
            $table->float('expected_qty',11,2)->nullable();     //Previsión del tiempo a emplear
            $table->integer('time_unit')->nullable();           //Unidad de tiempo en que medir la tarea
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('briefing_template_items');
    }
}
