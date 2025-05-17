<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('product_id'); // Foreign key to products
            $table->unsignedBigInteger('outlet_id'); // Foreign key to outlets
            $table->unsignedBigInteger('employee_id')->nullable(); // Nullable foreign key to employees
            $table->string('movement_type'); // Enum: initial, purchase, sale, adjustment
            $table->decimal('quantity', 10, 2); // Positive (in) or negative (out)
            $table->text('reason')->nullable(); // Optional reason for the movement
            $table->timestamps(); // Created at and updated at timestamps
            $table->softDeletes(); // Soft delete column

            // Add foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
}