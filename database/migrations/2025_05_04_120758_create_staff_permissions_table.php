<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->boolean('manage_orders')->default(false);
            $table->boolean('manage_products')->default(false);
            $table->boolean('manage_settings')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_permissions');
    }
};
