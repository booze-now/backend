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
        Schema::create('price_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('drink_measure_id')->relates('drink_measure')->on('id');
            $table->datetime('end');
            $table->decimal('unit_price', 8, 2);
            $table->timestamps();
            $table->unique('drink_measure_id', 'end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_logs');
    }
};
