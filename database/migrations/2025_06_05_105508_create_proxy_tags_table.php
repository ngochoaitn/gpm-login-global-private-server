<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        Schema::create('proxy_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('proxy_id');
            $table->uuid('tag_id');
            $table->timestamps();

            // Ensure unique combination of proxy_id and tag_id
            $table->unique(['proxy_id', 'tag_id']);
            
            $table->foreign('proxy_id')->references('id')->on('proxies')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxy_tags');
    }
};
