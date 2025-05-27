<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->nullable()->default(0);
            $table->decimal('buy_price', 10, 2)->default(0);
            $table->decimal('sell_price', 10, 2)->default(0);
            $table->smallInteger('min_qty')->default(1);
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('category_id');
            $table->boolean('is_shown')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('unit_id', 'fk_products_units_idx');
            $table->index('category_id', 'fk_products_categories1_idx');

            $table->foreign('unit_id', 'fk_products_units')
                ->references('id')
                ->on('units')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('category_id', 'fk_products_categories1')
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