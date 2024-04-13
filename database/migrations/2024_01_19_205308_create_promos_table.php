<?php

use App\Http\Controllers\PromoController;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promo_id');
            $table->datetime('start');
            $table->datetime('end')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();

            $table->foreign('promo_id')->references('id')->on('promo_types');
            $table->foreign('category_id')->references('id')->on('drink_categories');
        });

        (new \App\Models\Promo())->fill([
            'promo_id' => 1, 'start' => Carbon::now(), 'end' => Carbon::tomorrow(), 'category_id' => null
        ])->save();

        (new \App\Models\Promo())->fill([
            'promo_id'=>1, 'start'=>Carbon::now(),'end'=> Carbon::tomorrow(),'category_id'=>null
        ])->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
