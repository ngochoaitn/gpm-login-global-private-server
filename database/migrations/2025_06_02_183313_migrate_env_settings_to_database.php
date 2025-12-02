<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migrate existing .env settings to database
        $settingsToMigrate = [
            'storage_type' => $this->determineStorageType(),
            's3_key' => env('S3_KEY', ''),
            's3_secret' => env('S3_PASSWORD', ''),
            's3_bucket' => env('S3_BUCKET', ''),
            's3_region' => env('S3_REGION', ''),
            'cache_extension' => 'off'
        ];

        foreach ($settingsToMigrate as $name => $value) {
            Setting::updateOrCreate(
                ['name' => $name],
                ['value' => $value]
            );
        }
    }

    /**
     * Determine storage type based on .env values
     */
    private function determineStorageType()
    {
        $s3Key = env('S3_KEY');
        $s3Secret = env('S3_PASSWORD');
        $s3Bucket = env('S3_BUCKET');
        $s3Region = env('S3_REGION');

        // If all S3 settings are present, use S3, otherwise use local
        if (!empty($s3Key) && !empty($s3Secret) && !empty($s3Bucket) && !empty($s3Region)) {
            return 's3';
        }

        return 'local';
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove migrated settings
        $settingsToRemove = [
            'storage_type',
            's3_key',
            's3_secret',
            's3_bucket',
            's3_region'
        ];

        Setting::whereIn('name', $settingsToRemove)->delete();
    }
};
