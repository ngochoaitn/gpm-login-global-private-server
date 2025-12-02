<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_shares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('proxy_id');
            $table->uuid('user_id');
            $table->enum('role', ['FULL', 'EDIT', 'VIEW'])->default('VIEW');
            $table->timestamps();

            // Ensure unique combination of proxy_id and user_id
            $table->unique(['proxy_id', 'user_id']);
            
            $table->foreign('proxy_id')->references('id')->on('proxies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxy_shares');
    }
};
