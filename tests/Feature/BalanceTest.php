<?php


use App\Models\Balance;
use Database\Seeders\BalanceSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BalanceTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateBalance()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/balances',
            [
                'name' => 'Eko',
                'pin' => '123456',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson(
                [
                    'data' => [
                        'name' => 'Eko',
                        'pin' => '123456',
                    ]
                ]
            );
    }

    public function testCreateBalanceFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/balances',
            [
                'name' => '',
                'pin' => '123456',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'name' => [
                            'The name field is required.'
                        ]
                    ]
                ]
            );
    }

    public function testCreateBalanceUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/balances',
            [
                'name' => 'Eko',
                'pin' => '123456',
            ],
            // [
            //     'Authorization' => 'salah'
            // ]
        )->assertStatus(401)
            ->assertJson(
                [
                    'errors' => [
                        'message' => [
                            'unauthorized'
                        ]
                    ]
                ]
            );
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, BalanceSeeder::class]);
        $contact = Balance::query()->limit(1)->first('id');

        $this->get(
            '/api/balances/' . $contact->id,
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson(
                [
                    'data' => [
                        'name' => 'Asrul',
                        'pin' => '123456',
                        'amount' => 100,
                        'history' => ''
                    ]
                ]
            );
    }
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, BalanceSeeder::class]);
        $balance = Balance::query()->limit(1)->first();

        $this->get(
            '/api/balances/' . ($balance->id + 1),
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson(
                [
                    'errors' => [
                        'message' => [
                            'not found'
                        ]
                    ]
                ]
            );
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, BalanceSeeder::class]);
        $contact = Balance::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . $contact->id,
            [
                'Authorization' => 'test2'
            ]
        )->assertStatus(404)
            ->assertJson(
                [
                    'errors' => [
                        'message' => [
                            'not found'
                        ]
                    ]
                ]
            );
    }


    public function testUpdateBalance()
    {
        $this->seed([UserSeeder::class, BalanceSeeder::class]);
        $balance = Balance::query()->limit(1)->first();

        $newAmount = 150.0;

        $response = $this
            ->put(
                "/api/balances/$balance->id",
                [
                    'name' => 'Asrul',
                    'pin' => '123456',
                    'amount' => $newAmount,
                ],
                [
                    'Authorization' => 'test'
                ]
            );

        $response->assertStatus(200)
            ->assertJson(
                [
                    'data' => [
                        'name' => 'Asrul',
                        'pin' => '123456',
                        'amount' => $newAmount,
                        'history' => $balance->history,
                    ]
                ]
            );
    }
    public function testUpdateBalanceFailure()
    {
        $this->seed([UserSeeder::class, BalanceSeeder::class]);
        $balance = Balance::query()->limit(1)->first();

        $newAmount = 150.0; // Amount is negative, which is not allowed.

        $response = $this
            ->put(
                "/api/balances/$balance->id",
                [
                    'name' => 'Asrul',
                    'pin' => '',
                    'amount' => $newAmount,
                ],
                [
                    'Authorization' => 'test'
                ]
            );

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'pin' => [
                        'The pin field is required.'
                    ]
                ]
            ]);
    }

    public function testAddBalance()
    {
        $this->seed([UserSeeder::class, BalanceSeeder::class]);
        $balance = Balance::query()->limit(1)->first();

        $newAmount = 50;

        $response = $this->put(
            "/api/balances/add/$balance->id",
            [
                'pin' => '123456',
                'amount' => $newAmount,
            ],
            [
                'Authorization' => 'test'
            ]
        );

        $amount = $balance->amount + $newAmount;

        $response->assertStatus(200)
            ->assertJson(
                [
                    'data' => [
                        'name' => 'Asrul',
                        'pin' => '123456',
                        'amount' => $amount,
                        'history' => $balance->history . '+' . $newAmount . ',',
                    ],
                ]
            );
    }
    public function testAddBalanceWithNonExistentBalance()
{
    $this->seed([UserSeeder::class]);
    $nonExistentBalanceId = 9999; // Anggap id saldo ini tidak ada

    $newAmount = 50;

    $response = $this->put(
        "/api/balances/add/$nonExistentBalanceId",
        [
            'pin' => '123456',
            'amount' => $newAmount,
        ],
        [
            'Authorization' => 'test'
        ]
    );

    $response->assertStatus(404) // Harus gagal dengan status code 404 Not Found
        ->assertJson([
            'errors' => [
                'message' => ['Saldo tidak ditemukan'],
            ]
        ]);
}

    public function testSubtractBalanceSuccess()
    {
        $this->seed([UserSeeder::class, BalanceSeeder::class]);
        $balance = Balance::query()->limit(1)->first();
        $initialAmount = $balance->amount;

        $subtractedAmount = 30;

        $response = $this->put(
            "/api/balances/subtract/$balance->id",
            [
                'pin' => '123456',
                'amount' => $subtractedAmount,
            ],
            [
                'Authorization' => 'test'
            ]
        );

        $newAmount = $initialAmount - $subtractedAmount;

        $response->assertStatus(200)
            ->assertJson(
                [
                    'data' => [
                        'name' => 'Asrul',
                        'pin' => '123456',
                        'amount' => $newAmount,
                        'history' => $balance->history . '-' . $subtractedAmount . ',',
                    ],
                ]
            );
    }

    public function testSubtractInsufficientBalance()
    {
        $this->seed([UserSeeder::class, BalanceSeeder::class]);
        $balance = Balance::query()->limit(1)->first();
        $initialAmount = $balance->amount;

        $subtractedAmount = $initialAmount + 10; // Mencoba mengurangkan lebih dari saldo saat ini

        $response = $this->put(
            "/api/balances/subtract/$balance->id",
            [
                'pin' => '123456',
                'amount' => $subtractedAmount,
            ],
            [
                'Authorization' => 'test'
            ]
        );

        $response->assertStatus(400) // Mengubah kode status yang diharapkan menjadi 400
            ->assertJson(
                [
                    'errors' => [
                        "message" => [
                            "Saldo tidak mencukupi"
                        ]
                    ]
                ]
            );

        // Pastikan saldo tidak berubah
        $balance->refresh();
        $this->assertEquals($initialAmount, $balance->amount);
    }
}
