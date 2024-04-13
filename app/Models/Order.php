<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'guest_id',
        'recorded_by',
        'recorded_at',
        'made_by',
        'made_at',
        'served_by',
        'served_at',
        'table',
    ];
protected $dates = [
    'recorded_at',
    'made_at',
    'served_at',
];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'status'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function getStatusAttribute()
    {
        if ($this->recorded_at === null) {
            $status = __('pending');
        } elseif ($this->made_at === null) {
            $status = __('in progress');
        } elseif ($this->served_at === null) {
            $status = __('ready');
        } else {
            $status = __('served');
        }
        return $status;
    }
}
