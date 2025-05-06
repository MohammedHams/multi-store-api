<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $store = Store::inRandomOrder()->first() ?? Store::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'store_id' => $store->id,
            'user_id' => $user->id,
            'total' => fake()->randomFloat(2, 10, 1000), // قيمة عشوائية بين 10 و 1000
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}
