<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PaymentMethod;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 自分（ログイン用ユーザー）を作成
        User::create([
            'id' => 1,
            'name' => 'テスト1',
            'email' => 'test1@aa.com',
            'password' => bcrypt('11111111'),
        ]);

        User::create([
            'id' => 2,
            'name' => 'テスト2',
            'email' => 'test2@aa.com',
            'password' => bcrypt('22222222'),
        ]);
    }
}