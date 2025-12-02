<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Group;
use App\Models\GroupShare;
use App\Models\ProfileShare;

class ProfileApiTest_Admin extends ProfileApiTest
{
    protected function auth()
    {
        // Create a test user
        $email = 'Administrator';
        $this->user = User::where('email', $email)->first();

        // Create a token for authentication
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }
}
