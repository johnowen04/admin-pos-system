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
        Schema::create('employee_outlet', function (Blueprint $table) {
            $table->unsignedBigInteger('outlets_id'); // Foreign key for outlets
            $table->string('nip', 20); // Foreign key for employees (primary key in employees table)

            // Composite primary key
            $table->primary(['outlets_id', 'nip']);

            // Indexes
            $table->index('nip', 'fk_outlets_has_employees_employees1_idx');
            $table->index('outlets_id', 'fk_outlets_has_employees_outlets1_idx');

            // Foreign key constraints
            $table->foreign('outlets_id', 'fk_outlets_has_employees_outlets1')
                ->references('id')
                ->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('nip', 'fk_outlets_has_employees_employees1')
                ->references('nip')
                ->on('employees')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_outlet');
    }
};