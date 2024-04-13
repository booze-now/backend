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
            $table->string('first_name', 32);
            $table->string('middle_name', 32);
            $table->string('last_name', 32);
            $table->string('email', 64)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('role_code');  // CHECK ([role] IN ('pincér', 'pultos', 'backoffice'))
            $table->boolean('active')->default(true);
            // $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        App\Models\Employee::create([
            'first_name' => 'Zsolt',
            'middle_name' => 'Admin',
            'last_name' => 'Schopper',
            'email' => 'zschopper+admin@gmail.com',
            'password' => 'Bo0ze-nOOOw!',
            'role_code' => \App\Models\Employee::BACKOFFICE,
            'active' => 1,
        ]);

        (new \App\Models\Employee())->fill([
            'first_name' => 'StafAdmin',
            'middle_name' => 'StafAdmin',
            'last_name' => 'StafAdmin',
            'email' => 'StafAdmin@boozenow.hu',
            'role_code'=> \App\Models\Employee::BACKOFFICE,
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
