<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('base_units', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name', 45); // Name column with max length 45
            $table->timestamps(); // Created at and updated at columns
            $table->softDeletes(); // Soft delete column
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_units');
    }
};