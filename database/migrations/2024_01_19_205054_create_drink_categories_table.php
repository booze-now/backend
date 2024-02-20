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
        Schema::create('drink_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 64)->unique();
            $table->string('name_hu', 64)->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('drink_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drink_categories');
    }
};
