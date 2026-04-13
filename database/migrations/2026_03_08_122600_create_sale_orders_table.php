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
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['pending','accepted', 'processing','shipped','received', 'cancelled'])->default('pending');
            $table->enum('payment_status' , ['unpaid' , 'partial' ,'paid']);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_orders');
    }
};
