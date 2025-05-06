<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $stores = \App\Models\Store::all();

        foreach ($stores as $store) {
            Order::factory()->count(20)->create([
                'store_id' => $store->id,
                'user_id' => \App\Models\User::inRandomOrder()->first()->id,
            ]);
        }
    }
}
