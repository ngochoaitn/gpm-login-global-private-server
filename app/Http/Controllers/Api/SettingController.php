<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\SettingService;
use App\Services\S3PresignedUrlService;

class SettingController extends BaseController
{
    protected $settingService;
    protected $s3PresignedUrlService;

    public function __construct(SettingService $settingService, S3PresignedUrlService $s3PresignedUrlService)
    {
        $this->settingService = $settingService;
        $this->s3PresignedUrlService = $s3PresignedUrlService;
    }

    public function getS3Setting(Request $request)
    {
        $result = $this->settingService->getS3Settings();
        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    public function getS3Api(Request $request)
    {
        // Validate required parameters
        $type = $request->query('type');
        $sessionId = $request->query('session_id');

        if (!$type || !$sessionId) {
            return $this->getJsonResponse(false, __('api.s3_presigned_url_missing_params'), (object) []);
        }

        if (!in_array($type, ['get', 'post'])) {
            return $this->getJsonResponse(false, __('api.s3_presigned_url_invalid_type'), (object) []);
        }

        $result = $this->s3PresignedUrlService->getS3PresignedUrl($type, $sessionId);
        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    public function getStorageTypeSetting()
    {
        $result = $this->settingService->getStorageTypeSetting();
        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    // 23.7.2024 check version of private server
    public function getPrivateServerVersion()
    {
        $result = $this->settingService->getPrivateServerVersion();
        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    // 24.9.2024
    public function getAllSetting()
    {
        $result = $this->settingService->getAllSettings();
        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }
}
