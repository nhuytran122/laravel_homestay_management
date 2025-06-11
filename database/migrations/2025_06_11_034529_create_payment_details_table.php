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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_service_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('extension_id');
            $table->foreign('extension_id')->references('id')->on('booking_extensions')->onDelete('cascade');
            $table->enum('payment_purpose', ['ROOM_BOOKING', 'PREPAID_SERVICE', 'ADDITIONAL_SERVICE', 'EXTENDED_HOURS']);
            $table->decimal('base_amount', 10, 2)->default(0)->check('base_amount >= 0');
            $table->decimal('final_amount', 10, 2)->default(0)->check('final_amount >= 0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};