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
            $table->string('description', 255)->nullable(); // Description of the invoice
            $table->unsignedBigInteger('outlet_id'); // Foreign key to outlets table
            $table->unsignedBigInteger('employee_id')->nullable(); // Foreign key to employees table
            $table->unsignedBigInteger('created_by'); // Foreign key to users table
            $table->timestamps(); // Created and updated timestamps
            $table->softDeletes(); // Soft delete column

            // Foreign key constraint
            $table->foreign('outlet_id')->references('id')->on('outlets');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('created_by')->references('id')->on('users');
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