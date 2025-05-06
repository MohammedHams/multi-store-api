<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // فحص وجود متاجر قبل الإنشاء
        if (Store::count() === 0) {
            $this->command->error('No stores found! Please run StoreSeeder first.');
            return;
        }

        // سوبر أدمن
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'phone' => '+966500000000',
            'password' => bcrypt('secret'),
            'type' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // أصحاب المتاجر
        User::factory()->count(5)->create([
            'type' => 'store_owner',
            'store_id' => Store::inRandomOrder()->first()->id,
        ]);

        // الموظفين
        User::factory()->count(20)->create([
            'type' => 'staff',
            'store_id' => Store::inRandomOrder()->first()->id,
        ]);
    }
}
