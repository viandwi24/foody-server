<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MenuTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_all_menu()
    {
        $response = $this
            ->getJson('/api/menu');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'description',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }

    public function test_create_menu()
    {
        $response = $this
            ->postJson(
                '/api/menu',
                [
                    'name' => 'test',
                    'price' => 1000,
                    'description' => 'test',
                    'image' => 'https://via.placeholder.com/150',
                ]
            );
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'price',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    // public function test_get_menu_by_id()
    // {
    //     $response = $this
    //         ->getJson('/api/menu/1');
    //     $response->assertStatus(200)
    //         ->assertJsonStructure([
    //             'success',
    //             'data' => [
    //                 'id',
    //                 'name',
    //                 'price',
    //                 'description',
    //                 'created_at',
    //                 'updated_at',
    //             ]
    //         ]);
    // }
}
