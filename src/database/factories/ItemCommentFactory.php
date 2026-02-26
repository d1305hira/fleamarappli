<?php

namespace Database\Factories;

use App\Models\ItemComment;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemCommentFactory extends Factory
{
    protected $model = ItemComment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'comment' => $this->faker->sentence(6),
        ];
    }
}
