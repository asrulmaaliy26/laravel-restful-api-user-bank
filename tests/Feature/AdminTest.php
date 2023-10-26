<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test2')->first();
        // $this->get('/api/users/current', [
        //     'Authorization' => 'test'
        // ]);
        $this->patch(
            '/api/users/current',
            [
                // 'name'=>'test',
                'password' => 'baru'
            ],
            [
                'Authorization' => 'test2'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test2',
                    'name' => 'test2'
                ]
            ]);

        $newUser = User::where('username', 'test2')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test2')->first();

        $this->patch(
            '/api/users/current',
            [
                'name' => 'Eko'
            ],
            [
                'Authorization' => 'test2'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test2',
                    'name' => 'Eko'
                ]
            ]);

        $newUser = User::where('username', 'test2')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            '/api/users/current',
            [
                'name' => 'AsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsuAsu'
            ],
            [
                'Authorization' => 'test2'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }
}
