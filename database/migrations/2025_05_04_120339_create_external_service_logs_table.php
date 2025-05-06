<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('external_service_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('service_type', ['whatsapp', 'shipping']);
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->text('response')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_service_logs');
    }
};
