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
        Schema::create('outlet_category', function (Blueprint $table) {
            $table->unsignedBigInteger('outlets_id'); // Foreign key for outlets
            $table->unsignedBigInteger('categories_id'); // Foreign key for categories

            // Composite primary key
            $table->primary(['outlets_id', 'categories_id']);

            // Indexes
            $table->index('categories_id', 'fk_outlets_has_categories_categories1_idx');
            $table->index('outlets_id', 'fk_outlets_has_categories_outlets1_idx');

            // Foreign key constraints
            $table->foreign('outlets_id', 'fk_outlets_has_categories_outlets1')
                ->references('id')
                ->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('categories_id', 'fk_outlets_has_categories_categories1')
                ->references('id')
                ->on('categories')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_category');
    }
};