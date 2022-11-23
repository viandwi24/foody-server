<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login()
    {
        $response = $this
            ->postJson(
                '/api/auth/login',
                [
                    'email' => 'owner@example.com',
                    'password' => 'password',
                ]
            );
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'token'
            ]);
    }
}
