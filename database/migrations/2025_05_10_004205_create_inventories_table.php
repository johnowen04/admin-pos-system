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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id'); 
            $table->unsignedBigInteger('product_id');
            $table->smallInteger('quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['outlet_id', 'product_id'], 'unique_outlet_product');

            $table->index('outlet_id', 'fk_outlets_has_products_outlets1_idx');
            $table->index('product_id', 'fk_outlets_has_products_products1_idx');

            $table->foreign('outlet_id', 'fk_outlets_has_products_outlets1')
                ->references('id')
                ->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('product_id', 'fk_outlets_has_products_products1')
                ->references('id')
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
        Schema::dropIfExists('inventories');
    }
};
