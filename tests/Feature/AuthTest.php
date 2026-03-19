<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
     use RefreshDatabase;

   public function test_user_can_register(): void
   {
    $response = $this->postJson('/api/register',[
        'name' => 'Ali',
        'email' => 'ali22@example.com',
       'password' => 'password123',
            'password_confirmation' => 'password123',
    ]);
    $response->assertStatus(201)
             ->assertJsonStructure([
                'status',
                'message',
                'user',
                'token',
             ]);
   }

    public function test_user_can_login(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Ali',
            'email' => 'ali@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'ali@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'user',
                'token',
            ]);
    }

}
