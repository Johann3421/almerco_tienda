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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_year', 4);
            $table->unsignedInteger('order_counter')->default(0);
            $table->uuid('order_code')->unique();
            $table->string('customer_name', 100);
            $table->string('customer_email', 100);
            $table->string('customer_phone', 20);
            $table->string('customer_address', 255);
            $table->string('customer_document_number', 20);
            $table->char('customer_document_type', 1);
            $table->enum('order_status', ['Pendiente de Pago', 'Pendiente de Entrega', 'Completado', 'Cancelado', 'Reembolsado'])->default('Pendiente de Pago');
            $table->string('order_code_otp', 6)->nullable();
            $table->decimal('order_total', 10, 2);
            $table->text('observation')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('amount');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
