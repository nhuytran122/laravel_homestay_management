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
        Schema::create('booking_pricing_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->integer('base_duration')->check('base_duration > 0');
            $table->decimal('base_price')->check('base_price > 0');
            $table->decimal('extra_hour_price')->check('extra_hour_price > 0');
            $table->decimal('overnight_price')->check('overnight_price > 0');
            $table->decimal('daily_price')->check('daily_price > 0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_pricing_snapshots');
    }
};