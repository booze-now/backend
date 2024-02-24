<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employee extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $guard = 'guard_employee';

    public const ROLES = [ //'pincér', 'pultos', 'backoffice'
        'waiter',
        'bartender',
        'backoffice',
    ];

    public const WAITER = 0;
    public const BARTENDER = 1;
    public const BACKOFFICE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_code',
        'active',
    ];

    protected $appends = ['role'];


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

    public function scopeBartender($query)
    {
        return $query->where('role_code', Employee::BARTENDER);
    }

    public function scopeWaiter($query)
    {
        return $query->where('role_code', Employee::WAITER);
    }

    public function scopeBackoffice($query)
    {
        return $query->where('role_code', Employee::BACKOFFICE);
    }

    public function getRoleAttribute()
    {
        return __(Employee::ROLES[$this->role_code]);
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
            'role' => 'staff'
        ];
    }

    public function checkCustomClaims($claims)
    {
        return $claims['role'] && $claims['role'] == 'staff';
    }
}
