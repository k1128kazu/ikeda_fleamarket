<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run()
    {
        // items テーブル
        DB::table('items')->insert([
            [
                'user_id' => 1,
                'name' => '腕時計',
                'price' => 47000,
                'brand' => 'ROLEX',
                'description' => '高級感のある腕時計です。',
                'image_path' => 'item/Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'color' => 'グレー',
                'status' => '販売中',
            ],
            [
                'user_id' => 2,
                'name' => 'HDD',
                'price' => 5000,
                'brand' => 'Western Digital',
                'description' => '大容量のハードディスクです。',
                'image_path' => 'item/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'color' => 'ブラック',
                'status' => '販売中',
            ],
            [
                'user_id' => 3,
                'name' => '玉ねぎ三束',
                'price' => 300,
                'brand' => '農家直送',
                'description' => '新鮮な玉ねぎの三束セットです。',
                'image_path' => 'item/iLoveIMG+d.jpg',
                'condition' => '新品',
                'color' => 'なし',
                'status' => '販売中',
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'price' => 12000,
                'brand' => 'REGAL',
                'description' => 'ビジネス用の革靴です。',
                'image_path' => 'item/Leather+Shoes+Product+Photo.jpg',
                'condition' => '良好',
                'color' => 'ブラウン',
                'status' => '販売中',
            ],
            [
                'user_id' => 2,
                'name' => 'ノートPC',
                'price' => 85000,
                'brand' => 'DELL',
                'description' => 'リビングでも使えるノートPC。',
                'image_path' => 'item/Living+Room+Laptop.jpg',
                'condition' => '良好',
                'color' => 'シルバー',
                'status' => '販売中',
            ],
            [
                'user_id' => 3,
                'name' => 'マイク',
                'price' => 3000,
                'brand' => 'SONY',
                'description' => '音質の良いマイクです。',
                'image_path' => 'item/Music+Mic+4632231.jpg',
                'condition' => '良好',
                'color' => 'ブラック',
                'status' => '販売中',
            ],
            [
                'user_id' => 1,
                'name' => '財布',
                'price' => 7000,
                'brand' => 'PORTER',
                'description' => 'コンパクトな財布です。',
                'image_path' => 'item/Purse+fashion+pocket.jpg',
                'condition' => '良好',
                'color' => 'ネイビー',
                'status' => '販売中',
            ],
            [
                'user_id' => 2,
                'name' => 'タンブラー',
                'price' => 2500,
                'brand' => 'STARBUCKS',
                'description' => 'おしゃれなタンブラーです。',
                'image_path' => 'item/Tumbler+souvenir.jpg',
                'condition' => '良好',
                'color' => 'ホワイト',
                'status' => '販売中',
            ],
            [
                'user_id' => 3,
                'name' => 'コーヒーグラインダー',
                'price' => 18000,
                'brand' => 'HARIO',
                'description' => '業務用コーヒーグラインダー。',
                'image_path' => 'item/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
                'color' => 'ブラック',
                'status' => '販売中',
            ],
            [
                'user_id' => 1,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => 'CANMAKE',
                'description' => '便利なメイクアップセット。',
                'image_path' => 'item/makeup_set.jpg',
                'condition' => '目立った傷や汚れなし',
                'color' => 'ピンク',
                'status' => '販売中',
            ],
        ]);

        // category_item（中間テーブル）
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        foreach (range(1, 10) as $itemId) {
            DB::table('category_item')->insert([
                'item_id' => $itemId,
                'category_id' => $categoryIds[array_rand($categoryIds)],
            ]);
        }
    }
}
