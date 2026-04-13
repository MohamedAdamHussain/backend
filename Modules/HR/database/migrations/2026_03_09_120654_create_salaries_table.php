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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('restrict');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('transport_allowance', 15, 2)->default(0);
            $table->decimal('housing_allowance', 15, 2)->default(0);
            $table->decimal('absences_deduction', 15, 2)->default(0);
            $table->decimal('advances_deduction', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2);
            $table->date('month');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
