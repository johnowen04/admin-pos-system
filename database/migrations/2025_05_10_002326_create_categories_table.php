<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name', 45); // Name column with max length 45
            $table->unsignedBigInteger('department_id'); // Foreign key column
            $table->boolean('is_shown')->default(true); // Boolean column with default value true
            $table->timestamps(); // Created at and updated at columns
            $table->softDeletes(); // Soft delete column

            // Index for departments_id
            $table->index('department_id', 'fk_categories_departments1_idx');

            // Foreign key constraint
            $table->foreign('department_id', 'fk_categories_departments1')
                ->references('id')
                ->on('departments')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};