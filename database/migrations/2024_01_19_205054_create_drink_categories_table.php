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
            $table->string('name_en', 32)->unique();
            $table->string('name_hu', 32)->unique();
            $table->integer('parent')->nullable()->relates('drink_category')->on('id');
            $table->timestamps();
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
