<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    return [
        'name' => $this->faker->word(),
        'price' => $this->faker->numberBetween(1000, 10000),
        'image' => 'default.jpg',
        'condition' => $this->faker->numberBetween(1, 4),
        'description' => $this->faker->sentence(),
        'user_id' => User::factory(), // Seederで上書きするなら不要
        ];
    }
}
