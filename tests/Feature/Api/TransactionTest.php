<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{

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

    public function create_txs($data)
    {
        return $this
            ->postJson('/api/transaction', $data)
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'code',
                    'user_name',
                    'total',
                    'status',
                    'created_at',
                    'updated_at',
                    'menus' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'image',
                            'description',
                            'created_at',
                            'updated_at',
                            'pivot' => [
                                'quantity',
                                'total',
                                'price',
                            ]
                        ]
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Transaction Created',
            ]);
    }

    public function update_tx($id, $status)
    {
        $r = $this
            ->putJson('/api/transaction/' . $id, ['status' => $status])
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'user_name',
                    'total',
                    'status',
                    'code',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Transaction Updated',
            ]);

        $this->assertEquals($status, $r['data']['status']);

        return $r;
    }

    public function get_tx($id)
    {
        return $this->getJson('/api/transaction/' . $id)
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'user_name',
                    'total',
                    'status',
                    'code',
                    'created_at',
                    'updated_at',
                    'menus' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'image',
                            'description',
                            'created_at',
                            'updated_at',
                            'pivot' => [
                                'quantity',
                                'total',
                                'price',
                            ]
                        ]
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Detail Transaction',
            ]);
    }

    public function test_create_transaction()
    {
        // prepare data
        $creates = [];
        for ($i = 0; $i < 3; $i++) {
            $creates[] = $this->create_menu();
        }
        $data = [
            'user_name' => 'example user',
            'menus' => [
                ['id' => $creates[0][0]['data']['id'], 'qty' => 1],
                ['id' => $creates[1][0]['data']['id'], 'qty' => 2],
                ['id' => $creates[2][0]['data']['id'], 'qty' => 3],
            ]
        ];

        $response = $this->create_txs($data);
    }

    public function test_get_transaction_by_id()
    {
        // prepare data
        $creates = [];
        for ($i = 0; $i < 3; $i++) {
            $creates[] = $this->create_menu();
        }
        $data = [
            'user_name' => 'example user',
            'menus' => [
                ['id' => $creates[0][0]['data']['id'], 'qty' => 1],
                ['id' => $creates[1][0]['data']['id'], 'qty' => 2],
                ['id' => $creates[2][0]['data']['id'], 'qty' => 3],
            ]
        ];

        // create
        $response_create = $this->create_txs($data);

        // get id
        $tx_id = $response_create->json('data.id');
        $response_get = $this->get_tx($tx_id);
        $transaction_menus = $response_get->json('data.menus');
        $this->assertEquals($transaction_menus[0]['id'], $creates[0][0]['data']['id']);
        $this->assertEquals($transaction_menus[1]['id'], $creates[1][0]['data']['id']);
        $this->assertEquals($transaction_menus[2]['id'], $creates[2][0]['data']['id']);
    }

    public function test_update_transaction_change_status()
    {
        // prepare data
        $creates = [];
        for ($i = 0; $i < 3; $i++) {
            $creates[] = $this->create_menu();
        }
        $data = [
            'user_name' => 'example user',
            'menus' => [
                ['id' => $creates[0][0]['data']['id'], 'qty' => 1],
                ['id' => $creates[1][0]['data']['id'], 'qty' => 2],
                ['id' => $creates[2][0]['data']['id'], 'qty' => 3],
            ]
        ];

        // create
        $response_create = $this->create_txs($data);

        // get id
        $tx_id = $response_create->json('data.id');
        $response_get = $this->get_tx($tx_id);

        // change to processing
        $response_update = $this->update_tx($tx_id, 'processing');
        $response_get = $this->get_tx($tx_id);
        $this->assertEquals('processing', $response_get->json('data.status'));

        // change to declined
        $response_update = $this->update_tx($tx_id, 'declined');
        $response_get = $this->get_tx($tx_id);
        $this->assertEquals('declined', $response_get->json('data.status'));

        // change to finished
        $response_update = $this->update_tx($tx_id, 'finished');
        $response_get = $this->get_tx($tx_id);
        $this->assertEquals('finished', $response_get->json('data.status'));
    }

    public function test_get_all_transaction()
    {
        $response = $this
            ->getJson('/api/transaction')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'user_name',
                        'total',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'List Data Transaction',
            ]);
    }
}
