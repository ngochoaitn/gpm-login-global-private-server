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
        Schema::table('proxies', function (Blueprint $table) {
            // Add the new columns
            $table->string('raw_proxy', 255)->nullable()->after('id');
            $table->string('status')->default('active')->after('raw_proxy');
            $table->uuid('updated_by')->nullable()->after('created_by');
        });

        // Migrate existing data to new format
        DB::statement("UPDATE proxies SET raw_proxy = CONCAT(
            COALESCE(LOWER(type), 'http'), '://',
            CASE
                WHEN username IS NOT NULL AND password IS NOT NULL
                THEN CONCAT(username, ':', password, '@')
                ELSE ''
            END,
            host, ':', port
        ) WHERE raw_proxy IS NULL");

        // Update status based on is_active
        DB::statement("UPDATE proxies SET status = CASE WHEN is_active = 1 THEN 'active' ELSE 'inactive' END WHERE status = 'active'");

        Schema::table('proxies', function (Blueprint $table) {
            // Drop the old columns
            $table->dropColumn([
                'name',
                'host',
                'port',
                'type',
                'username',
                'password',
                'is_active',
                'description'
            ]);
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
            // Add back the old columns
            $table->string('name')->nullable()->after('id');
            $table->string('host')->after('name');
            $table->integer('port')->after('host');
            $table->string('type')->default('HTTP')->after('port');
            $table->string('username')->nullable()->after('type');
            $table->string('password')->nullable()->after('username');
            $table->boolean('is_active')->default(true)->after('password');
            $table->text('description')->nullable()->after('is_active');
        });

        // Migrate data back from raw_proxy to individual fields (basic parsing)
        $proxies = DB::table('proxies')->get();
        foreach ($proxies as $proxy) {
            if (!empty($proxy->raw_proxy)) {
                // Basic parsing to extract components
                $parsed = parse_url($proxy->raw_proxy);

                DB::table('proxies')->where('id', $proxy->id)->update([
                    'host' => $parsed['host'] ?? '127.0.0.1',
                    'port' => $parsed['port'] ?? 8080,
                    'type' => strtoupper($parsed['scheme'] ?? 'http'),
                    'username' => $parsed['user'] ?? null,
                    'password' => $parsed['pass'] ?? null,
                    'is_active' => $proxy->status === 'active',
                    'name' => 'Proxy ' . $proxy->id
                ]);
            }
        }

        Schema::table('proxies', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn(['raw_proxy', 'status', 'updated_by']);
        });
    }
};
