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
        // Modificar la relación category_id en la tabla groups
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });

        // Modificar la relación category_id y group_id en la tabla subgroups
        Schema::table('subgroups', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['group_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
            $table->foreign('group_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade');
        });

        // Modificar la relación product_id y subgroup_id en la tabla products_subgroups
        Schema::table('products_subgroups', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['subgroup_id']);
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
            $table->foreign('subgroup_id')
                  ->references('id')
                  ->on('subgroups')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios de las claves foráneas
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories');
        });

        Schema::table('subgroups', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['group_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories');
            $table->foreign('group_id')
                  ->references('id')
                  ->on('groups');
        });

        Schema::table('products_subgroups', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['subgroup_id']);
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products');
            $table->foreign('subgroup_id')
                  ->references('id')
                  ->on('subgroups');
        });
    }
};
