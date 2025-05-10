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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('invoice_number')->unique()->nullable(); // Unique invoice number
            $table->decimal('grand_total', 15, 2); // Grand total amount
            $table->string('description', 255); // Unique invoice number
            $table->unsignedBigInteger('outlets_id'); // Foreign key to outlets table
            $table->string('nip'); // Employee Identification Number
            $table->timestamps(); // Created and updated timestamps
            $table->softDeletes(); // Soft delete column

            // Foreign key constraint
            $table->foreign('outlets_id')->references('id')->on('outlets')->onDelete('cascade');
            $table->foreign('nip')->references('nip')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};