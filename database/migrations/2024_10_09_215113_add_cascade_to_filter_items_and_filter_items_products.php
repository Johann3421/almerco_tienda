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
        // Modificar la relación filter_id en filter_items para agregar cascadeOnDelete
        Schema::table('filter_items', function (Blueprint $table) {
            $table->dropForeign(['filter_id']); // Elimina la clave foránea actual
            $table->foreign('filter_id')->references('id')->on('filters')->cascadeOnDelete(); // Añade cascadeOnDelete
        });

        // Modificar las relaciones en filter_items_products
        Schema::table('filter_items_products', function (Blueprint $table) {
            $table->dropForeign(['filter_item_id']); // Elimina la clave foránea actual
            $table->foreign('filter_item_id')->references('id')->on('filter_items')->cascadeOnDelete(); // Añade cascadeOnDelete

            $table->dropForeign(['product_id']); // Elimina la clave foránea actual
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete(); // Añade cascadeOnDelete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios eliminando las claves con cascadeOnDelete y restaurando las originales
        Schema::table('filter_items', function (Blueprint $table) {
            $table->dropForeign(['filter_id']);
            $table->foreign('filter_id')->references('id')->on('filters'); // Sin cascadeOnDelete
        });

        Schema::table('filter_items_products', function (Blueprint $table) {
            $table->dropForeign(['filter_item_id']);
            $table->foreign('filter_item_id')->references('id')->on('filter_items'); // Sin cascadeOnDelete

            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products'); // Sin cascadeOnDelete
        });
    }
};
