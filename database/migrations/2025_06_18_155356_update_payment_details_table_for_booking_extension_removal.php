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
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropForeign(['extension_id']);
            $table->dropColumn('extension_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->unsignedBigInteger('extension_id')->nullable();
            $table->foreign('extension_id')->references('id')->on('booking_extensions')->onDelete('cascade');
        });
    }
};