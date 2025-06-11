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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('payment_type', ['CASH', 'TRANSFER']);
            $table->dateTime('payment_date');
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED', 'REFUNDED', 'PENDING_REFUND'])->default('PENDING');
            $table->string('vnp_transaction_no', 50)->nullable();
            $table->string('vnp_txt_ref', 50)->nullable();
            $table->decimal('total_amount', 10, 2)->default(0)->check('total_amount >= 0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};