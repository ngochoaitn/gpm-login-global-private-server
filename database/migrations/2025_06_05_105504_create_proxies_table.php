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
        Schema::create('proxies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('host');
            $table->integer('port');
            $table->enum('type', ['HTTP', 'HTTPS', 'SOCKS4', 'SOCKS5'])->default('HTTP');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            // Ensure unique combination of host and port
            $table->unique(['host', 'port']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxies');
    }
};
