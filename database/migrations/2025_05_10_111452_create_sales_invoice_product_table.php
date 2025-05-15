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
        Schema::create('sales_invoice_product', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('sales_invoice_id'); // Foreign key to sales_invoices
            $table->unsignedBigInteger('products_id'); // Foreign key to products
            $table->integer('quantity'); // Quantity of the product
            $table->decimal('unit_price', 15, 2); // Unit price of the product
            $table->decimal('total_price', 15, 2); // Total price (quantity * unit_price)
            $table->timestamps(); // Created and updated timestamps

            // Foreign key constraints
            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');
            $table->foreign('products_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_product');
    }
};