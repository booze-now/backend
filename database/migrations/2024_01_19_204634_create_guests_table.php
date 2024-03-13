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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 32);
            $table->string('middle_name', 32);
            $table->string('last_name', 32);
            $table->string('email', 64)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('table', 36)->nullable();
            $table->boolean('reservee')->nullable();
            $table->boolean('active')->default(false);
            // $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        (new \App\Models\Guest())->fill([
            'first_name' => 'Zsolt',
            'middle_name' => 'Guest',
            'last_name' => 'Schopper',
            'email' => 'zschopper+guest@gmail.com',
            'password' => 'Bo0ze-nOOOw!'

        ])->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
