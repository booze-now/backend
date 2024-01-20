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
            $table->integer('drink_measure_id');
            $table->integer('amount');
            $table->integer('promo_id')->relates('promo')->on('id')->nullable();
            $table->integer('unit_price');
            $table->decimal('discount', 5, 2)->nullable()->default(0);
            $table->integer('receipt_id')->relates('receipt')->on('id');
            $table->timestamps();
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
