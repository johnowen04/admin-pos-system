<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->string('slug', 50);
            $table->timestamps();
            $table->softDeletes(); // Adds a nullable 'deleted_at' column
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
