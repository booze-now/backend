<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Guest extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $guard = 'guard_guest';

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
        'email_verified_at',
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
        'active' => 'boolean',
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => 'guest'
        ];

    }

    public function checkCustomClaims($claims)
    {
        echo json_encode($claims);
        return $claims['role'] && $claims['role'] == 'guest';
    }
}
