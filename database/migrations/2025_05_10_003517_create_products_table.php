<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('sku', 50)->primary(); // Primary key
            $table->string('name', 100); // Product name
            $table->text('description')->nullable(); // Product description
            $table->decimal('base_price', 10, 2)->nullable()->default(0); // Base price
            $table->decimal('buy_price', 10, 2)->default(0); // Buy price
            $table->decimal('sell_price', 10, 2)->default(0); // Sell price
            $table->smallInteger('min_qty')->default(1); // Minimum quantity with default value 1
            $table->unsignedBigInteger('units_id'); // Foreign key for units
            $table->unsignedBigInteger('categories_id'); // Foreign key for categories
            $table->boolean('is_shown')->default(true); // Boolean column with default value true
            $table->timestamps(); // Created at and updated at columns
            $table->softDeletes(); // Soft delete column

            // Indexes
            $table->index('units_id', 'fk_products_units_idx');
            $table->index('categories_id', 'fk_products_categories1_idx');

            // Foreign key constraints
            $table->foreign('units_id', 'fk_products_units')
                ->references('id')
                ->on('units')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('categories_id', 'fk_products_categories1')
                ->references('id')
                ->on('categories')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};