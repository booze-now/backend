<?php

use App\Http\Controllers\PromoController;
use App\Http\Controllers\PromoTypeController;
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
        Schema::create('promo_types', function (Blueprint $table) {
            $table->id();
            $table->string('description_en');
            $table->string('description_hu');
            $table->timestamps();
        });

       /*  $type= new PromoTypeController();
        $type->create(['description_hu'=>'Húsvéti akció','description_en'=>'Easter sale' ]);
 */
(new \App\Models\PromoType())->fill([
    'description_hu'=>'Húsvéti akció',
    'description_en'=>'Easter sale'
])->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_types');
    }
};
