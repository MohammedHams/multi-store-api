<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        // إنشاء متجر إذا لم يكن موجودًا
        $store = Store::inRandomOrder()->first() ?? Store::factory()->create();

        return [
            'store_id' => $store->id,
            'name' => fake()->words(3, true), // مثال: "Fresh Organic Apples"
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 1, 1000), // سعر بين 1 و 1000
        ];
    }
}
