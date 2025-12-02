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
        Schema::table('profiles', function (Blueprint $table) {
            // Add storage_type field
            $table->enum('storage_type', ['S3', 'GOOGLE_DRIVE', 'LOCAL'])->default('S3')->after('name');

            // Add new storage_path column
            $table->string('storage_path')->nullable()->after('storage_type');

            // Add new meta_data column
            $table->longText('meta_data')->nullable()->after('storage_path');

            // Add usage tracking fields
            $table->uuid('using_by')->nullable();
            $table->foreign('using_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('last_used_at')->nullable();
            $table->integer('usage_count')->default(0);

            // Add soft delete fields
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });

        // Copy data from old columns to new columns
        DB::statement('UPDATE profiles SET storage_path = s3_path WHERE s3_path IS NOT NULL');
        DB::statement('UPDATE profiles SET meta_data = cookie_data WHERE cookie_data IS NOT NULL');

        Schema::table('profiles', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['s3_path', 'cookie_data']);
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
            // Add back old columns
            $table->string('s3_path')->nullable();
            $table->longText('cookie_data')->nullable();
        });

        // Copy data back
        DB::statement('UPDATE profiles SET s3_path = storage_path WHERE storage_path IS NOT NULL');
        DB::statement('UPDATE profiles SET cookie_data = meta_data WHERE meta_data IS NOT NULL');

        Schema::table('profiles', function (Blueprint $table) {
            // Remove added fields
            $table->dropColumn(['storage_type', 'storage_path', 'meta_data', 'using_by', 'last_used_at', 'usage_count', 'is_deleted', 'deleted_at', 'deleted_by']);
        });
    }
};
