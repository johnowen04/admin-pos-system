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
        Schema::create('employee_outlets', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('outlet_id');

            $table->primary(['employee_id', 'outlet_id']);

            $table->index('employee_id', 'fk_outlets_has_employees_employees1_idx');
            $table->index('outlet_id', 'fk_outlets_has_employees_outlets1_idx');

            $table->foreign('employee_id', 'fk_outlets_has_employees_employees1')
                ->references('id')
                ->on('employees')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('outlet_id', 'fk_outlets_has_employees_outlets1')
                ->references('id')
                ->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_outlets');
    }
};
