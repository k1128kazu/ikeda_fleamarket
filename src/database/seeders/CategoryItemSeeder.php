<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryItemSeeder extends Seeder
{
    public function run()
    {
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        if (empty($categoryIds)) return;

        $itemIds = DB::table('items')->pluck('id')->toArray();
        if (empty($itemIds)) return;

        foreach ($itemIds as $itemId) {
            // 既にカテゴリが付いてる商品は触らない
            $exists = DB::table('category_item')
                ->where('item_id', $itemId)
                ->exists();

            if ($exists) continue;

            DB::table('category_item')->insert([
                'item_id' => $itemId,
                'category_id' => $categoryIds[array_rand($categoryIds)],
            ]);
        }
    }
}
