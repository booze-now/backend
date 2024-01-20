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
        Schema::create('drink_measures', function (Blueprint $table) {
            $table->id();
            $table->integer('drink_id')->relates('drink')->on('id');
            $table->decimal('amount', 8, 2);
            $table->integer('meas')->relates('dictionary')->on('id');
            $table->decimal('unit_price', 8, 2);
            $table->string('status', 10); // CHECK ([status] IN ('aktív', 'inaktív'))
            $table->unique('drink_id', 'amount', 'meas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drink_measures');
    }
};
