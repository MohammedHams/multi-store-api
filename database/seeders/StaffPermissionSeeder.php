<?php

namespace Database\Seeders;

use App\Models\StaffPermission;
use Illuminate\Database\Seeder;

class StaffPermissionSeeder extends Seeder
{
    public function run()
    {
        $staffUsers = \App\Models\User::where('type', 'staff')->get();

        foreach ($staffUsers as $user) {
            StaffPermission::create([
                'user_id' => $user->id,
                'store_id' => $user->store_id,
                'manage_orders' => fake()->boolean(),
                'manage_products' => fake()->boolean(),
                'manage_settings' => fake()->boolean(),
            ]);
        }
    }
}
