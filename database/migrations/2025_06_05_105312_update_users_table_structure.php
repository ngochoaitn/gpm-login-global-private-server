<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new email column
            $table->string('email')->after('id');

            // Add new system_role column
            $table->enum('system_role', ['ADMIN', 'MOD', 'USER'])->default('USER')->after('email');

            // Add new is_active column
            $table->boolean('is_active')->default(true)->after('system_role');

            // Add timestamps if they don't exist
            if (!Schema::hasColumn('users', 'created_at')) {
                $table->timestamps();
            }
        });

        // Copy data from old columns to new columns
        DB::statement('UPDATE users SET email = user_name');
        DB::statement('UPDATE users SET system_role = CASE WHEN role = 1 THEN "ADMIN" WHEN role = 2 THEN "MOD" ELSE "USER" END');
        DB::statement('UPDATE users SET is_active = CASE WHEN active = 1 THEN true ELSE false END');

        Schema::table('users', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['user_name', 'role', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back old columns
            $table->string('user_name')->after('id');
            $table->integer('role')->default(0)->after('user_name');
            $table->integer('active')->default(1)->after('role');
        });

        // Copy data back
        DB::statement('UPDATE users SET user_name = email');
        DB::statement('UPDATE users SET role = CASE WHEN system_role = "ADMIN" THEN 1 WHEN system_role = "MOD" THEN 2 ELSE 0 END');
        DB::statement('UPDATE users SET active = CASE WHEN is_active = true THEN 1 ELSE 0 END');

        Schema::table('users', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['email', 'system_role', 'is_active']);
            $table->dropTimestamps();
        });
    }
};
