<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->enum('interval', ['monthly', 'yearly'])->default('monthly');
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->integer('product_limit')->default(-1);
            $table->integer('staff_limit')->default(5);
            $table->integer('store_limit')->default(1);
            $table->boolean('has_analytics')->default(true);
            $table->boolean('has_multi_payment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
