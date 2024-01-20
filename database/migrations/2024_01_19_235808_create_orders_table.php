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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('guest_id')->relates('guest')->on('id');
            $table->integer('recorded_by')->relates('employee')->on('id')->nullable();
            $table->datetime('recorded_at')->nullable();
            $table->integer('made_by')->relates('employee')->on('id')->nullable();
            $table->datetime('made_at')->nullable();
            $table->integer('served_by')->relates('employee')->on('id')->nullable();
            $table->datetime('served_at')->nullable();
            $table->string('table', 36)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
