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
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('id');
            $table->string('part_number')->nullable()->after('sku');
            $table->string('description')->nullable()->after('part_number');
            $table->boolean('on_promotion')->default(false)->after('on_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('part_number');
            $table->dropColumn('description');
            $table->dropColumn('on_promotion');
        });
    }
};
