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
        Schema::create('purchase_invoice_product', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('purchase_invoice_id'); // Foreign key to purchase_invoices
            $table->unsignedBigInteger('product_id'); // Foreign key to products
            $table->integer('quantity'); // Quantity of the product
            $table->decimal('base_price', 15, 2); // Unit price of the product
            $table->decimal('unit_price', 15, 2); // Unit price of the product
            $table->decimal('total_price', 15, 2); // Total price (quantity * unit_price)
            $table->timestamps(); // Created and updated timestamps

            // Foreign key constraints
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_product');
    }
};