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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('role_code');  // CHECK ([role] IN ('pincér', 'pultos', 'backoffice'))
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        (new \App\Models\Employee())->fill([
            'name' => 'StafAdmin',
            'email' => 'StafAdmin@boozenow.hu',
            'role_code'=>1,
            'password' => 'StafAdminBo0ze-nOOOw!',
        ])->save();
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
