<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('category');
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock')->default(0);
            $table->text('description');
            $table->string('image_url')->default('');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('customer_name');
            $table->string('phone');
            $table->json('items');
            $table->unsignedInteger('total');
            $table->text('note')->nullable();
            $table->string('payment_method')->default('whatsapp');
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('source');
            $table->string('customer_name');
            $table->json('items');
            $table->unsignedInteger('total');
            $table->timestamps();
        });

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->string('identifier')->primary();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
    }
};
