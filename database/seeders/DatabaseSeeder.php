<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            StoreSeeder::class, // يتم تشغيله أولاً
            UserSeeder::class,
            StaffPermissionSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
