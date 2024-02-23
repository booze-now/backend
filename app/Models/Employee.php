<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
}
