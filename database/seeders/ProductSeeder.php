<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $stores = \App\Models\Store::all();

        foreach ($stores as $store) {
            Product::factory()->count(15)->create([
                'store_id' => $store->id,
            ]);
        }
    }
}
