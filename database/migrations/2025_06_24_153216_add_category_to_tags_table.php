<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->string('category', 255)->nullable()->after('color');
            $table->dropUnique(['name']);
            $table->unique(['name', 'category'], 'tags_name_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique('tags_name_category_unique');
            $table->unique(['name']);
            $table->dropColumn('category');
        });
    }
};
