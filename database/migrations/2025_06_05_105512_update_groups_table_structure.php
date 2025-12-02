<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            // Add new sort_order column (renamed from order to avoid MySQL reserved keyword issues)
            $table->integer('sort_order')->default(0)->after('name');

            // Add updated_by field
            $table->uuid('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Add timestamps if they don't exist
            if (!Schema::hasColumn('groups', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Copy data from old column to new column
        DB::statement('UPDATE `groups` SET sort_order = sort WHERE sort IS NOT NULL');

        Schema::table('groups', function (Blueprint $table) {
            // Drop old column
            $table->dropColumn('sort');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            // Add back old column
            $table->integer('sort')->default(0)->after('name');
        });

        // Copy data back
        DB::statement('UPDATE `groups` SET sort = `order` WHERE `order` IS NOT NULL');

        Schema::table('groups', function (Blueprint $table) {
            // Remove new columns
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['order', 'updated_by', 'updated_at']);
        });
    }
};
