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


    public function getStatusAttribute() {
        // pending awaiting 'feldolgozás alatt'
        // in progress 'elkészítés alatt'
        // ready 'kész'
        // served 'kiszolgálva'
        // paid 'fizetve'
        return _('');
    }

}
