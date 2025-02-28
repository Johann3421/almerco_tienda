<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('filter_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filter_id')->constrained('filters');
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('filter_items_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filter_item_id')->constrained('filter_items');
            $table->foreignId('product_id')->constrained('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_items_products');
        Schema::dropIfExists('filter_items');
        Schema::dropIfExists('filters');
    }
};
