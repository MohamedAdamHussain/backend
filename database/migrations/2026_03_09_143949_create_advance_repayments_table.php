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
        Schema::create('advance_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advance_id')->constrained('advances')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'completed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_repayments');
    }
};
