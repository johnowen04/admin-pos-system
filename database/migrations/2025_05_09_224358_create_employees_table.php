<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('nip', 20)->primary(); // Primary key
            $table->string('name', 255);
            $table->string('phone', 15)->nullable();
            $table->string('email', 255)->nullable();
            $table->unsignedBigInteger('roles_id'); // Foreign key
            $table->timestamps();
            $table->softDeletes(); // Soft delete column

            // Index for roles_id
            $table->index('roles_id', 'fk_employees_roles1_idx');

            // Foreign key constraint
            $table->foreign('roles_id', 'fk_employees_roles1')
                ->references('id')
                ->on('roles')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};