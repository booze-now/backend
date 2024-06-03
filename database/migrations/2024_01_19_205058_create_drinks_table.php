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
            $table->string('name_en', 64)->unique();
            $table->string('name_hu', 64)->unique();
            $table->unsignedBigInteger('category_id');
            $table->string('description_en')->nullable();
            $table->string('description_hu')->nullable();
            $table->boolean('active')->default(true);
            $table->string('image_url')->default("https://www.ormistonhospital.co.nz/wp-content/uploads/2016/05/No-Image.jpg");
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('drink_categories');
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
