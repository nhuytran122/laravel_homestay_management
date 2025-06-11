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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('check_in');
            $table->dateTime('check_out');
            $table->integer('guest_count')->check('guest_count > 0');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED', 'COMPLETED'])->default('PENDING');
            $table->decimal('total_amount', 10, 2)->default(0)->check('total_amount >= 0');
            $table->decimal('paid_amount', 10, 2)->default(0)->check('paid_amount >= 0');
            $table->boolean('has_send_reminder')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};