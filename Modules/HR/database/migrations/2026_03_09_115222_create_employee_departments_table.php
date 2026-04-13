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
        Schema::create('employee_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('restrict');
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->unique(['employee_id', 'department_id']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('type_of_working', ['full_time', 'part_time', 'contract'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_departments');
    }
};
