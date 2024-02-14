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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->relates('order')->on('id');
            $table->integer('drink_unit_id');
            $table->integer('amount');
            $table->unsignedBigInteger('promo_id')->nullable();
            $table->integer('unit_price');
            $table->decimal('discount', 5, 2)->nullable()->default(0);
            $table->unsignedBigInteger('receipt_id')->nullable();
            $table->timestamps();

            $table->foreign('promo_id')->references('id')->on('promos');
            $table->foreign('receipt_id')->references('id')->on('receipts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
