<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::insert([
            [
                'name' => '山田 太郎',
                'email' => 'taro@example.com',
                'password' => Hash::make('password'),
                'postcode' => '100-0001',
                'address' => '東京都千代田区1-1',
                'building' => 'テストビル101',
                'image' => 'user/m01.png',
            ],
            [
                'name' => '佐藤 次郎',
                'email' => 'jiro@example.com',
                'password' => Hash::make('password'),
                'postcode' => '150-0001',
                'address' => '東京都渋谷区1-1',
                'building' => '渋谷マンション202',
                'image' => 'user/m02.png',
            ],
            [
                'name' => '鈴木 花子',
                'email' => 'hanako@example.com',
                'password' => Hash::make('password'),
                'postcode' => '530-0001',
                'address' => '大阪市北区1-1',
                'building' => '梅田タワー303',
                'image' => 'user/f01.png',
            ],
        ]);
    }
}
