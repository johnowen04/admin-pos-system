<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        foreach (['purchase_invoices', 'sales_invoices'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->boolean('is_voided')->default(false);
                $table->string('void_reason')->nullable();
                $table->unsignedBigInteger('voided_by')->nullable();
                $table->timestamp('voided_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        foreach (['purchase_invoices', 'sales_invoices'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['is_voided', 'void_reason', 'voided_by', 'voided_at']);
            });
        }
    }
};
