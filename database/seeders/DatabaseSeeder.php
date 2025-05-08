<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Store;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $store = Store::factory()->create(); // Create one store to associate users with

        User::factory()->create([
            'name' => 'Store Owner',
            'email' => 'storeowner@example.com',
            'type' => 'store_owner',
            'store_id' => $store->id,
        ]);

        User::factory()->create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'type' => 'staff',
            'store_id' => $store->id,
        ]);

        $this->call([
            UserSeeder::class,
            StaffPermissionSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
