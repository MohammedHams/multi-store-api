<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->enum('type', ['super_admin', 'store_owner', 'staff'])->default('staff');
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // تفعيل Soft Delete
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
