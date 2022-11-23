<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MenuTest extends TestCase
{
    // use RefreshDatabase;

    public function create_menu()
    {
        $name = 'test' . rand(1, 100);
        $price = rand(1000, 10000);
        $response = $this
            ->postJson(
                '/api/menu',
                [
                    'name' => $name,
                    'price' => $price,
                    'description' => 'test',
                    'image' => 'https://via.placeholder.com/150',
                ]
            )
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'price',
                    'image',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);
        return [$response, $name, $price];
    }

    public function get_menu($id)
    {
        return $this
            ->getJson('/api/menu/' . $id)
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'price',
                    'image',
                    'description',
                    'created_at',
                    'updated_at',
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
                    'image',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function test_get_menu_by_id()
    {
        // create menu
        $created_menu = $this->create_menu();
        $response_create_menu = $created_menu[0];
        $created_menu_name = $created_menu[1];
        $created_menu_price = $created_menu[2];
        $response_create_menu
            ->assertJson([
                'data' => [
                    'name' => $created_menu_name,
                    'price' => $created_menu_price,
                ]
            ]);



        // get menu by id
        $response_get_menu_by_id = $this
            ->get_menu($response_create_menu->json('data.id'))
            ->assertJson([
                'data' => [
                    'name' => $created_menu_name,
                    'price' => $created_menu_price,
                ]
            ]);
    }

    public function test_edit_menu()
    {
        // create menu
        $created_menu = $this->create_menu();
        $response_create_menu = $created_menu[0];
        $created_menu_name = $created_menu[1];
        $created_menu_price = $created_menu[2];
        $response_create_menu
            ->assertJson([
                'data' => [
                    'name' => $created_menu_name,
                    'price' => $created_menu_price,
                ]
            ]);


        // get menu by id
        $response_get_menu_by_id = $this
            ->get_menu($response_create_menu->json('data.id'))
            ->assertJson([
                'data' => [
                    'name' => $created_menu_name,
                    'price' => $created_menu_price,
                ]
            ]);

        //  edit
        $new = [
            'name' => 'test' . rand(1, 100),
            'price' => rand(1000, 10000),
            'description' => 'description test' . rand(1, 100),
            'image' => 'https://via.placeholder.com/' . rand(1, 100),
        ];
        $response_edit = $this
            ->putJson(
                '/api/menu/' . $response_create_menu->json('data.id'),
                $new
            )
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'price',
                    'image',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => $new['name'],
                    'price' => $new['price'],
                    'description' => $new['description'],
                    'image' => $new['image'],
                ]
            ]);


        // get menu by id
        $response_get_menu_by_id = $this
            ->get_menu($response_create_menu->json('data.id'))
            ->assertJson([
                'data' => [
                    'name' => $new['name'],
                    'price' => $new['price'],
                ]
            ]);
    }

    public function test_delete_menu()
    {
        // create menu
        $created_menu = $this->create_menu();
        $response_create_menu = $created_menu[0];
        $created_menu_name = $created_menu[1];
        $created_menu_price = $created_menu[2];
        $response_create_menu
            ->assertJson([
                'data' => [
                    'name' => $created_menu_name,
                    'price' => $created_menu_price,
                ]
            ]);


        // get menu by id
        $response_get_menu_by_id = $this
            ->get_menu($response_create_menu->json('data.id'))
            ->assertJson([
                'data' => [
                    'name' => $created_menu_name,
                    'price' => $created_menu_price,
                ]
            ]);


        // delete
        $response_delete = $this
            ->deleteJson('/api/menu/' . $response_create_menu->json('data.id'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'price',
                    'image',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => $created_menu_name,
                    'price' => $created_menu_price,
                ]
            ]);
    }

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
                        'image',
                        'description',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }
}
