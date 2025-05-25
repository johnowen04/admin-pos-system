<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('type')->default('pos');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->string('phone', 15);
            $table->string('whatsapp', 15);
            $table->string('email', 255);
            $table->text('address');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
