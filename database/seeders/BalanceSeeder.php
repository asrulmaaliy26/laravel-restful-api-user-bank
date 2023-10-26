<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Balance;
use Illuminate\Foundation\Auth\User;

class BalanceSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('username', 'test')->first();
        Balance::create([
            'name' => 'Asrul',
            'pin' => '123456',
            'amount' => 100.00,
            'history' => '',
            'user_id' => $user->id,
        ]);
        // Tambahkan entri lain jika diperlukan
    }
}
