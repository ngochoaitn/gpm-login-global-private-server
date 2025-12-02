<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Group;
use App\Models\User;

class SettingService
{
    public static $server_version = 13;

    public function initializeDefaultSettings()
    {
        $defaultSettings = [
            'storage_type' => 'local',
            's3_key' => '',
            's3_secret' => '',
            's3_bucket' => '',
            's3_region' => '',
            'cache_extension' => 'off'
        ];

        foreach ($defaultSettings as $key => $value) {
            $setting = Setting::where('name', $key)->first();
            if (!$setting) {
                $this->setSetting($key, $value);
            }
        }
    }

    public function getS3Settings()
    {
        try {
            $this->initializeDefaultSettings();

            $apiKey = $this->getSetting('s3_key')?->value ?? '';
            $apiSecret = $this->getSetting('s3_secret')?->value ?? '';
            $apiBucket = $this->getSetting('s3_bucket')?->value ?? '';
            $apiRegion = $this->getSetting('s3_region')?->value ?? '';

            $settings = [
                's3_api_key' => $apiKey,
                's3_api_secret' => $apiSecret,
                's3_api_bucket' => $apiBucket,
                's3_api_region' => $apiRegion
            ];

            return ['success' => true, 'message' => 'ok', 'data' => $settings];
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => 's3_settings_incomplete', 'data' => null];
        }
    }


    public function getS3Config()
    {
        $this->initializeDefaultSettings();

        return (object) [
            'S3_KEY' => $this->getSetting('s3_key')?->value ?? '',
            'S3_PASSWORD' => $this->getSetting('s3_secret')?->value ?? '',
            'S3_BUCKET' => $this->getSetting('s3_bucket')?->value ?? '',
            'S3_REGION' => $this->getSetting('s3_region')?->value ?? ''
        ];
    }


    public function setSetting(string $key, string $value)
    {
        $setting = Setting::where('name', $key)->first();

        if ($setting == null) {
            $setting = new Setting();
            $setting->name = $key;
        }

        $setting->value = $value;
        $setting->save();

        return $setting;
    }

        public function getSetting(string $key)
        {
            return Setting::where('name', $key)->first();
        }

        public function get(string $key, $default = null)
        {
            $setting = $this->getSetting($key);
            return $setting ? $setting->value : $default;
        }

        public function updateS3Settings(array $s3Data)
        {
            try {
                $this->setSetting('s3_key', $s3Data['S3_KEY'] ?? '');
                $this->setSetting('s3_secret', $s3Data['S3_PASSWORD'] ?? '');
                $this->setSetting('s3_bucket', $s3Data['S3_BUCKET'] ?? '');
                $this->setSetting('s3_region', $s3Data['S3_REGION'] ?? '');

                return ['success' => true, 'message' => 's3_settings_updated'];
            } catch (\Exception $ex) {
                return ['success' => false, 'message' => 's3_settings_error', 'data' => ['details' => $ex->getMessage()]];
            }
        }

        public function getStorageTypeSetting()
        {
            $this->initializeDefaultSettings();

            $setting = Setting::where('name', 'storage_type')->first();
            return ['success' => true, 'message' => 'ok', 'data' => $setting->value];
        }

        public function getPrivateServerVersion()
        {
            $version = self::$server_version;
            $response = [];

            $response['version'] = $version;
            return ['success' => true, 'message' => 'ok', 'data' => $response];
        }

        public function getAllSettings()
        {
            $this->initializeDefaultSettings();

            $version = self::$server_version;
            $response = [];

            $storage_type = Setting::where('name', 'storage_type')->first();
            $cache_extension = Setting::where('name', 'cache_extension')->first();

            $response['version'] = $version;
            $response['storage_type'] = $storage_type->value ?? 'local';
            $response['cache_extension'] = $cache_extension->value ?? 'off';

            return ['success' => true, 'message' => 'ok', 'data' => $response];
        }
    }
