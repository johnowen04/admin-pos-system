<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique();
            $table->string('name', 255);
            $table->string('phone', 15)->nullable();
            $table->string('email', 255)->nullable();
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index for position_id
            $table->index('position_id', 'fk_employees_positions1_idx');
            $table->index('user_id', 'fk_employees_users1_idx');

            // Foreign key constraint
            $table->foreign('position_id', 'fk_employees_positions1_idx')
                ->references('id')
                ->on('positions')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('user_id', 'fk_employees_users1_idx')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
