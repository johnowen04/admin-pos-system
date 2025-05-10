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
        Schema::create('outlet_product', function (Blueprint $table) {
            $table->unsignedBigInteger('outlets_id'); // Foreign key for outlets
            $table->string('sku', 50); // Foreign key for products (primary key in products table)
            $table->smallInteger('quantity')->default(0); // Quantity column with default value 0

            // Composite primary key
            $table->primary(['outlets_id', 'sku']);

            // Indexes
            $table->index('sku', 'fk_outlets_has_products_products1_idx');
            $table->index('outlets_id', 'fk_outlets_has_products_outlets1_idx');

            // Foreign key constraints
            $table->foreign('outlets_id', 'fk_outlets_has_products_outlets1')
                ->references('id')
                ->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('sku', 'fk_outlets_has_products_products1')
                ->references('sku')
                ->on('products')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_product');
    }
};