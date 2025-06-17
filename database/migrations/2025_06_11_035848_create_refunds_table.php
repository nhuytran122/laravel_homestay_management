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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->enum('refund_type', ['FULL', 'PARTIAL_70', 'PARTIAL_30']);
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->enum('status', ['REQUESTED', 'APPROVED', 'REJECTED', 'COMPLETED'])->default('REQUESTED');
            $table->string('vnp_transaction_no', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};