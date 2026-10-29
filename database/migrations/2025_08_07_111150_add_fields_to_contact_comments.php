<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToContactComments extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('contact_comments', function (Blueprint $table) {
            $table->string('category')->nullable()->after('comment');
            $table->longText('tags')->nullable()->after('category');
            $table->decimal('estimated_value', 10, 2)->nullable()->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('contact_comments', function (Blueprint $table) {
            $table->dropColumn(['category', 'tags', 'estimated_value']);
        });
    }
}
