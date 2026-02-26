<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
    */
  public function run()
  {
    if (User::count() === 0) {
        User::factory()->count(5)->create();
        }

    if (Category::count() === 0) {
        Category::factory()->count(5)->create();
    }

    $items = [
      ['name' => '腕時計', 'price' => 15000, 'brand' => 'Rolax','description' => 'スタイリッシュなデザインのメンズ腕時計', 'image' => 'images/Armani+Mens+Clock.jpg' ,'condition' => 1],
      ['name' => 'HDD', 'price' => 5000, 'brand' => '西芝','description' => '高速で信頼性の高いハードディスク', 'image' => 'images/HDD+Hard+Disk.jpg' ,'condition' =>2],
      ['name' => '玉ねぎ３束', 'price' => 300, 'brand' => "",'description' => '新鮮な玉ねぎ３束のセット', 'image' => 'images/iLoveIMG+d.jpg' ,'condition' => 3],
      ['name' => '革靴', 'price' => 4000, 'brand' => "",'description' => 'クラシックなデザインの革靴', 'image' => 'images/Leather+Shoes+Product+Photo.jpg' ,'condition' => 4],
      ['name' => 'ノートPC', 'price' => 45000, 'brand' => "",'description' => '高性能なノートパソコン', 'image' => 'images/Living+Room+Laptop.jpg' ,'condition' => 1],
      ['name' => 'マイク', 'price' => 8000, 'brand' => "",'description' => '高音質のレコーディング用マイク', 'image' => 'images/Music+Mic+4632231.jpg' ,'condition' => 2],
      ['name' => 'ショルダーバッグ', 'price' => 3500, 'brand' => "",'description' => 'おしゃれなショルダーバッグ', 'image' => 'images/Purse+fashion+pocket.jpg' ,'condition' => 3],
      ['name' => 'タンブラー', 'price' => 500, 'brand' => "",'description' => '使いやすいタンブラー', 'image' => 'images/Tumbler+souvenir.jpg' ,'condition' => 4],
      ['name' => 'コーヒーミル', 'price' => 4000, 'brand' => "Starbacks",'description' => '手動のコーヒーミル', 'image' => 'images/Waitress+with+Coffee+Grinder.jpg' ,'condition' => 1],
      ['name' => 'メイクセット', 'price' => 2500, 'brand' => "",'description' => '便利なメイクアップセット', 'image' => 'images/外出メイクアップセット.jpg' ,'condition' => 2],
      ];

    foreach ($items as $item) {
        $user = User::inRandomOrder()->first();
        if ($user) {
            $item['user_id'] = $user->id;
            $createdItem = Item::create($item);

            $category = Category::inRandomOrder()->first();
            if ($category) {
                $createdItem->categories()->attach(
                  Category::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray()
                  );
              }
            }
          }
        }
      }