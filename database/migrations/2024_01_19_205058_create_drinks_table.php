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
        Schema::create('drinks', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 32)->unique();
            $table->string('name_hu', 32)->unique();
            $table->integer('category_id')->relates('drink_category')->on('id');
            $table->string('description_en')->nullable();
            $table->string('description_hu')->nullable();
            $table->string('status', 10); // CHECK ([status] IN ('aktív', 'inaktív'))
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drinks');
    }
};
