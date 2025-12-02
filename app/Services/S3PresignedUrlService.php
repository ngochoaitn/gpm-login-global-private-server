<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

class S3PresignedUrlService
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function getS3PresignedUrl(string $type, string $sessionId): array
    {
        try {
            // Validate type parameter
            if (!in_array($type, ['get', 'post'])) {
                return [
                    'success' => false,
                    'message' => __('api.s3_presigned_url_invalid_type'),
                    'data' => null
                ];
            }

            // Get cache keys
            $cacheKey = "s3_{$type}_{$sessionId}";
            $expiredKey = "s3_{$type}_{$sessionId}_expired";

            // Check if cached URL exists and not expired
            $cachedUrl = Cache::get($cacheKey);
            $expiredTime = Cache::get($expiredKey);

            if ($cachedUrl && $expiredTime && Carbon::now()->isBefore($expiredTime)) {
                return [
                    'success' => true,
                    'message' => 'ok',
                    'data' => [
                        'url' => $cachedUrl,
                        'expires_at' => $expiredTime,
                        'cached' => true
                    ]
                ];
            }

            // Generate new presigned URL
            $result = $this->generatePresignedUrl($type);

            if (!$result['success']) {
                return $result;
            }

            // Get cache duration from environment (default 120 minutes)
            $cacheMinutes = env('S3_PRESIGNED_URL_CACHE_MINUTES', 120);
            $expiresAt = Carbon::now()->addMinutes($cacheMinutes);

            // Store in cache
            Cache::put($cacheKey, $result['data']['url'], $cacheMinutes);
            Cache::put($expiredKey, $expiresAt, $cacheMinutes);

            return [
                'success' => true,
                'message' => __('api.ok'),
                'data' => [
                    'url' => $result['data']['url'],
                    'expires_at' => $expiresAt,
                    'cached' => false
                ]
            ];

        } catch (Exception $ex) {
            return [
                'success' => false,
                'message' => __('api.s3_presigned_url_generation_error') . ': ' . $ex->getMessage(),
                'data' => null
            ];
        }
    }

    private function generatePresignedUrl(string $type): array
    {
        try {
            // Initialize settings if needed
            $this->settingService->initializeDefaultSettings();

            // Get S3 settings from database
            $s3Settings = $this->settingService->getS3Settings();

            if (!$s3Settings['success']) {
                return [
                    'success' => false,
                    'message' => __('api.s3_presigned_url_config_not_found'),
                    'data' => null
                ];
            }

            $s3Data = $s3Settings['data'];

            // Validate S3 configuration
            if (
                empty($s3Data['s3_api_key']) || empty($s3Data['s3_api_secret']) ||
                empty($s3Data['s3_api_bucket']) || empty($s3Data['s3_api_region'])
            ) {
                return [
                    'success' => false,
                    'message' => __('api.s3_presigned_url_config_incomplete'),
                    'data' => null
                ];
            }

            // Create S3 client
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => $s3Data['s3_api_region'],
                'credentials' => [
                    'key' => $s3Data['s3_api_key'],
                    'secret' => $s3Data['s3_api_secret'],
                ],
            ]);

            // Get presigned URL duration from environment (default 120 minutes)
            $durationMinutes = env('S3_PRESIGNED_URL_DURATION_MINUTES', 120);
            $expires = "+{$durationMinutes} minutes";

            if ($type === 'get') {
                // Generate presigned URL for GET operations (download)
                $command = $s3Client->getCommand('GetObject', [
                    'Bucket' => $s3Data['s3_api_bucket'],
                    'Key' => 'profiles/{filename}', // Placeholder for actual file key
                ]);

                $presignedUrl = (string) $s3Client->createPresignedRequest($command, $expires)->getUri();
            } else {
                // Generate presigned URL for POST operations (upload)
                $postObject = new \Aws\S3\PostObjectV4(
                    $s3Client,
                    $s3Data['s3_api_bucket'],
                    [
                        'key' => 'profiles/${filename}', // Use placeholder for dynamic filename
                        'acl' => 'private',
                        'success_action_status' => '201'
                    ],
                    [
                        ['bucket' => $s3Data['s3_api_bucket']],
                        ['starts-with', '$key', 'profiles/'],
                        ['content-length-range', 1, 104857600], // 100MB max
                        ['starts-with', '$Content-Type', ''],
                    ],
                    $expires
                );

                $formAttributes = $postObject->getFormAttributes();
                $formInputs = $postObject->getFormInputs();

                $presignedUrl = [
                    'url' => $formAttributes['action'],
                    'fields' => $formInputs
                ];
            }

            return [
                'success' => true,
                'message' => __('api.ok'),
                'data' => [
                    'url' => $presignedUrl,
                    'type' => $type,
                    'bucket' => $s3Data['s3_api_bucket'],
                    'region' => $s3Data['s3_api_region'],
                    'expires_in_minutes' => $durationMinutes
                ]
            ];

        } catch (Exception $ex) {
            return [
                'success' => false,
                'message' => __('api.s3_presigned_url_generation_error') . ': ' . $ex->getMessage(),
                'data' => null
            ];
        }
    }


    public function clearCache(string $type, string $sessionId): array
    {
        try {
            $cacheKey = "s3_{$type}_{$sessionId}";
            $expiredKey = "s3_{$type}_{$sessionId}_expired";

            Cache::forget($cacheKey);
            Cache::forget($expiredKey);

            return [
                'success' => true,
                'message' => __('api.ok'),
                'data' => null
            ];
        } catch (Exception $ex) {
            return [
                'success' => false,
                'message' => __('api.s3_presigned_url_cache_clear_error') . ': ' . $ex->getMessage(),
                'data' => null
            ];
        }
    }
}