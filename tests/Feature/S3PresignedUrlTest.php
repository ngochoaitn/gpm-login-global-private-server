<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use App\Services\S3PresignedUrlService;
use App\Services\SettingService;
use App\Models\Setting;
use App\Models\User;

class S3PresignedUrlTest extends TestCase
{
    protected $s3PresignedUrlService;
    protected $settingService;
    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->settingService = new SettingService();
        $this->s3PresignedUrlService = new S3PresignedUrlService($this->settingService);
        
        // Set up mock S3 settings
        $this->setupMockS3Settings();

        // Create a test user
        $email = 'test@example.com';
        $existingUser = User::where('email', $email)->first();
        if (!$existingUser) {
            $this->user = User::factory()->create([
                'email' => $email,
                'password' => bcrypt('password'), 
                'system_role' => 'USER',
                'is_active' => true
            ]);
        } else {
            $this->user = $existingUser;
        }

        // Create a token for authentication
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    private function setupMockS3Settings()
    {
        Setting::firstOrCreate(
            ['name' => 's3_key'],
            ['value' => 'test_key']
        );
        Setting::firstOrCreate(
            ['name' => 's3_secret'], 
            ['value' => 'test_secret']
        );
        Setting::firstOrCreate(
            ['name' => 's3_bucket'],
            ['value' => 'test_bucket']
        );
        Setting::firstOrCreate(
            ['name' => 's3_region'],
            ['value' => 'us-east-1']
        );
    }

    public function test_api_endpoint_requires_auth()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get('/api/settings/get-s3-api');
        
        $this->assertEquals(401, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Unauthenticated', $data['message']);
    }

    public function test_api_endpoint_get_s3_settings()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/settings/get-s3-api');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertTrue($data['success']);
        $this->assertStringContainsString('ok', $data['message']);
    }

    public function test_cache_key_generation()
    {
        $sessionId = 'test_session_123';
        $type = 'get';
        
        // Clear any existing cache
        Cache::forget("s3_{$type}_{$sessionId}");
        Cache::forget("s3_{$type}_{$sessionId}_expired");
        
        // Verify cache is empty
        $this->assertNull(Cache::get("s3_{$type}_{$sessionId}"));
        $this->assertNull(Cache::get("s3_{$type}_{$sessionId}_expired"));
    }

    public function test_environment_variables_are_used()
    {
        // Test that environment variables are properly read
        $defaultCacheMinutes = env('S3_PRESIGNED_URL_CACHE_MINUTES', 120);
        $defaultDurationMinutes = env('S3_PRESIGNED_URL_DURATION_MINUTES', 120);
        
        $this->assertEquals(120, $defaultCacheMinutes);
        $this->assertEquals(120, $defaultDurationMinutes);
    }

    public function test_s3_settings_validation()
    {
        // Clear existing settings
        Setting::where('name', 's3_key')->delete();
        
        $result = $this->s3PresignedUrlService->getS3PresignedUrl('get', 'test123');
        
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('S3 configuration', $result['message']);
    }

    protected function tearDown(): void
    {
        // Clear cache after each test
        Cache::flush();
        parent::tearDown();
    }
}
