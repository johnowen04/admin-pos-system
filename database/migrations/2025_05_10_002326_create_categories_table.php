<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->unsignedBigInteger('department_id')->nullable(); 
            $table->boolean('is_shown')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('department_id', 'fk_categories_departments1_idx');

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