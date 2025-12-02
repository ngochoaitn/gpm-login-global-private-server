<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Add new columns
            $table->text('fingerprint_data')->nullable()->after('storage_path');
            $table->text('dynamic_data')->nullable()->after('fingerprint_data');
        });

        // Copy data from json_data to fingerprint_data (assuming json_data contains fingerprint info)
        DB::statement('UPDATE profiles SET fingerprint_data = null');

        Schema::table('profiles', function (Blueprint $table) {
            // Drop old json_data column
            $table->dropColumn('json_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Add back json_data column
            $table->longText('json_data')->nullable();
        });

        // Copy data back from fingerprint_data to json_data
        DB::statement('UPDATE profiles SET json_data = fingerprint_data WHERE fingerprint_data IS NOT NULL');

        Schema::table('profiles', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['fingerprint_data', 'dynamic_data']);
        });
    }
};
