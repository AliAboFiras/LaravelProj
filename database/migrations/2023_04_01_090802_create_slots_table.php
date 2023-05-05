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
        Schema::create('slots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('US_id');
            $table->unsignedBigInteger('AvD_id');
            $table->json('AvailableSlots');
            $table->timestamps();



            $table->foreign('US_id')->references('id')->on('user_services')->onDelete('cascade');
            $table->foreign('AvD_id')->references('id')->on('available_days')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
