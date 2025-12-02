<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\CreatesApplication;
use Illuminate\Support\Facades\Artisan;

class AuthControllerTest extends TestCase
{
    // use RefreshDatabase;
    // use CreatesApplication;

    /** @test */
    public function it_registers_a_user_successfully()
    {
        $user_name = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
        $payload = [
            'user_name' => $user_name,
            'display_name' => 'Test User',
            'password' => '123456',
        ];

        $response = $this->postJson('/api/users/register', $payload);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => $user_name,
            'display_name' => 'Test User',
            'is_active' => 0,
        ]);
    }

    /** @test */
    public function it_registers_a_duplicate_user()
    {
        $user_name = 'Administrator';
        $payload = [
            'user_name' => $user_name,
            'display_name' => 'Test User',
            'password' => '123456',
        ];

        $response = $this->postJson('/api/users/register', $payload);

        $response->assertStatus(200)
                 ->assertJson(['success' => false]);
    }

    /** @test */
    public function it_login_a_user_successfully()
    {
        $payload = [
            'user_name' => 'Administrator',
            'password' => 'Administrator'
        ];

        $response = $this->get('/api/users/login?user_name=' . $payload['user_name'] . '&password=' . $payload['password']);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}
