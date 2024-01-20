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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('serno', 16)->unique();
            $table->integer('guest_id')->relates('guest')->on('id');
            $table->datetime('issued_at');
            $table->integer('paid_for')->relates('employee')->on('id');
            $table->datetime('paid_at');
            $table->string('payment_method', 16); // CHECK ([payment_method] IN ('készpénz', 'bankkártya'))
            $table->string('table', 36)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
