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
        Schema::dropIfExists('booking_extensions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('booking_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->float('extended_hours')->check('extended_hours > 0');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'EXPIRED', 'CANCELLED'])->default('PENDING');
            $table->timestamps();
        });
    }
};