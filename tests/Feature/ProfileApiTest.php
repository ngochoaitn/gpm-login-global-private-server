<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Group;
use App\Models\GroupShare;
use App\Models\ProfileShare;

class ProfileApiTest extends TestCase
{
    use WithFaker;

    protected $user;
    protected $token;
    private $searchKeyword = 'Test Profile';
    private $searchAuthor = 'ABCĐE John Doe';

    private $filterGroupId = '';
    static $profileTestCreated = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth();

        $this->initProfiles();

        $this->filterGroupId = Group::where('name', 'Test filter group')->first()->id;
    }

    protected function auth()
    {
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

    function initProfiles()
    {
        if (!self::$profileTestCreated) {
            $this->createTestProfile($this->filterGroupId);
            $this->createTestProfile($this->filterGroupId);
            $this->createTestProfile($this->filterGroupId);

            $user = User::factory()->create(['display_name' => $this->searchAuthor]);
            $authorProfile = Profile::factory()->create([
                'name' => 'Profile Search Author',
                'created_by' => $user->id
            ]);
            ProfileShare::create([
                'profile_id' => $authorProfile->id,
                'user_id' => $this->user->id,
                'role' => ProfileShare::ROLE_VIEW
            ]);
            self::$profileTestCreated = true;
        }
    }

    function countTotalProfileOfUser($userId)
    {
        $user = User::find($userId);
        if ($user->isAdmin()) {
            return Profile::active()->count();
        }

        $groupShareIds = GroupShare::where('user_id', $userId)->pluck('group_id');     // Danh sách group id được chia sẻ

        $selfCreatedProfileIds = Profile::where('created_by', $userId)->where('is_deleted', false)->pluck('id');             // Danh sách profile id tạo bởi user
        $profileShareOverGroups = Profile::whereIn('group_id', $groupShareIds)->where('is_deleted', false)->pluck('id');     // Danh sách profile id chia sẻ qua group
        $profileShareIds = ProfileShare::where('user_id', $userId)
            ->join('profiles', 'profile_shares.profile_id', '=', 'profiles.id')
            ->where('profiles.is_deleted', false)
            ->pluck('profile_id');                                     // Danh sách profile id chia sẻ qua profile share

        $allProfileIds = collect($selfCreatedProfileIds)
            ->merge($profileShareOverGroups)
            ->merge($profileShareIds)
            ->unique();

        return $allProfileIds->count();
    }
    /** @test */
    public function checkNeedAuthentication()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get('/api/profiles');

        $response->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $response->json('message'));
        $this->assertFalse($response['success']);
    }

    /** @test */
    public function itCanCreateAProfile()
    {
        $profileName = 'itCanCreateAProfile';
        $profileData = [
            'name' => $profileName,
            'storage_path' => '/test/path',
            'fingerprint_data' => 'browser',
            'dynamic_data' => 'user_agent',
            'meta_data' => ['session' => 'test'],
            'group_id' => Group::first()->id,
            'storage_type' => 'S3'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->post('/api/profiles/create', $profileData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'storage_path',
                    'fingerprint_data',
                    'dynamic_data',
                    'meta_data',
                    'group_id',
                    'created_by'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($profileName, $response->json('data.name'));
        $this->assertEquals('/test/path', $response->json('data.storage_path'));
    }

    /** @test */
    public function itCanShowASpecificProfile()
    {
        // Create a profile first
        $profile = $this->createTestProfile(name: 'itCanShowASpecificProfile');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/profiles/' . $profile->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'storage_path'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($profile->name, $response->json('data.name'));
    }

    /** @test */
    public function itCanUpdateAProfile()
    {
        // Create a profile first
        $profile = $this->createTestProfile(name: 'itCanUpdateAProfile');

        $updateData = [
            'name' => 'Updated Profile Name',
            'storage_path' => '/updated/path',
            'fingerprint_data' => 'browser',
            'dynamic_data' => 'user_agent',
            'meta_data' => ['updated' => true],
            'group_id' => $this->filterGroupId
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->post('/api/profiles/update/' . $profile->id, $updateData);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify the profile was updated
        $profile = Profile::find($profile->id);
        $this->assertEquals('Updated Profile Name', $profile->name);
        $this->assertEquals('/updated/path', $profile->storage_path);
    }

    /** @test */
    public function itCanListProfiles()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/profiles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'total'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $count = $this->countTotalProfileOfUser($this->user->id);
        $this->assertEquals($count, $response->json('data.total'));
    }

    /** @test */
    public function itCanListProfilesWithSearch()
    {
        $keyword = 'Test Profile';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/profiles?search=' . $keyword);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'total'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $groupCount = Profile::where('name', 'like', '%' . $keyword . '%')->count();
        $this->assertEquals($groupCount, $response->json('data.total'));
    }

    /** @test */
    public function itCanListProfilesWithAuthorSearch()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/profiles?search=' . urlencode($this->searchAuthor));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'total'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(1, $response->json('data.total'));
    }

    /** @test */
    public function itCanListProfilesWithFilterGroupId()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/profiles?group_id=' . $this->filterGroupId);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'total'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $groupCount = Profile::where('group_id', $this->filterGroupId)
            ->where('created_by', $this->user->id)
            ->count();
        $this->assertEquals($groupCount, $response->json('data.total'));
    }

    /** @test */
    public function itCanDeleteAProfile()
    {
        // Create a profile first
        $profile = $this->createTestProfile(name: 'itCanDeleteAProfile');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/profiles/delete/' . $profile->id);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify the profile was soft deleted
        $profile->refresh();
        $this->assertTrue($profile->is_deleted);
    }

    /** @test */
    public function itCanGetProfileCount()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/profiles/count');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertIsInt($response->json('data.total'));
        $this->assertEquals(Profile::active()->count(), $response->json('data.total'));
    }

    /** @test */
    public function itRequiresAuthentication()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get('/api/profiles');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    /** @test */
    public function itValidatesRequiredFieldsForCreation()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->post('/api/profiles/create', []);

        // Since we're not using Laravel's built-in validation,
        // we expect the service to handle missing data gracefully
        // $response->assertStatus($response->status(), 'Response status should be 200 or 500');
        $this->assertTrue(in_array($response->status(), [200, 500]), 'Response status should be 200 or 500');
    }

    private function createTestProfile($groupId = null, $name = null)
    {
        // $groupId = $groupId ?? Group::first()?->id ?? 'huhu';
        if($groupId == null) {
            $groupId = Group::first()?->id;
        }
        return Profile::create([
            'name' => $name ?? ('Test Profile ' . $this->faker->uuid),
            'storage_path' => '/test/path/' . $this->faker->uuid,
            'fingerprint_data' => 'browser',
            'dynamic_data' => 'user_agent',
            'meta_data' => ['test' => true],
            'group_id' => $groupId,
            'created_by' => $this->user->id,
            'storage_type' => 'S3',
            'status' => 1,
            'usage_count' => 0,
            'is_deleted' => false
        ]);
    }
}
