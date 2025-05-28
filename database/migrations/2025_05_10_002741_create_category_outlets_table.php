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
        Schema::create('category_outlets', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('outlet_id');

            $table->primary(['outlet_id', 'category_id']);

            $table->index('category_id', 'fk_outlets_has_categories_categories1_idx');
            $table->index('outlet_id', 'fk_outlets_has_categories_outlets1_idx');

            $table->foreign('category_id', 'fk_outlets_has_categories_categories1')
                ->references('id')
                ->on('categories')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('outlet_id', 'fk_outlets_has_categories_outlets1')
                ->references('id')
                ->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_outlets');
    }
};