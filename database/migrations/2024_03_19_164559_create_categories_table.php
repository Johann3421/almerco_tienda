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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('token', 40)->unique()->nullable()->default(null);
            $table->char('featured', 1)->default('N');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->char('featured', 1)->default('N');
            $table->boolean('active')->default(true);
            $table->string('token', 40)->unique()->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('subgroups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('group_id')->constrained('groups');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->char('featured', 1)->default('N');
            $table->boolean('active')->default(true);
            $table->string('token', 40)->unique()->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('products_subgroups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('subgroup_id')->constrained('subgroups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_subcategories');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
    }
};
