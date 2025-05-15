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
            $table->unsignedBigInteger('employee_id'); // Foreign key for employees
            
            // Composite primary key
            $table->primary(['outlets_id', 'employee_id']);

            // Indexes
            $table->index('employee_id', 'fk_outlets_has_employees_employees1_idx');
            $table->index('outlets_id', 'fk_outlets_has_employees_outlets1_idx');

            // Foreign key constraints
            $table->foreign('outlets_id', 'fk_outlets_has_employees_outlets1')
                ->references('id')
                ->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

                $table->foreign('employee_id', 'fk_outlets_has_employees_employees1')
                ->references('id')
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