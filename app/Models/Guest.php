<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guest extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Fields
     *
     * name: string
     * email: string
     * email_verified_at: ?timestamp
     * password: string
     * table: ?string
     * reservee: ?boolean
     * active: boolean=false
     *
     * Relations
     *
     * email ux
     * id <= order.guest_id
     * id <= receipt.guest_id
     */

    public const INACTIVE = 0;
    public const ACTIVE = 1;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'table',
        'reservee',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean'
    ];

    /**
     * Aktívnak számít, ha megerősítette az e-mail címét, aktív és nem törölték a fiókot
     *
     * @param [type] $query
     * @return void
     */
    public function  scopeActive($query)
    {
        return $query
            ->whereNotNull("{$this->getTable()}.email_verified_at")
            ->whereNull("{$this->getTable()}.deleted_at")
            ->where("{$this->getTable()}.active", Guest::ACTIVE);
    }
}
