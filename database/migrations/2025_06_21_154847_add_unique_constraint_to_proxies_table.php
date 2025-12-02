<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proxies', function (Blueprint $table) {
            // Add unique constraint for raw_proxy and created_by combination
            $table->unique(['raw_proxy', 'created_by'], 'unique_raw_proxy_per_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proxies', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('unique_raw_proxy_per_user');
        });
    }
};