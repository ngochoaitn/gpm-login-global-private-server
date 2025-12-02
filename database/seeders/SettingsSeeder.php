<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultSettings = [
            'storage_type' => 'local',
            's3_key' => '',
            's3_secret' => '',
            's3_bucket' => '',
            's3_region' => '',
            'cache_extension' => 'off'
        ];

        foreach ($defaultSettings as $name => $value) {
            Setting::updateOrCreate(
                ['name' => $name],
                ['value' => $value]
            );
        }
    }
}
