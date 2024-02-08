<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrinkUnit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'drink_id',
        'amount',
        'unit_en',
        'unit_hu',
        'unit_price',
        'active',
    ];

    protected $appends = ['unit'];
    protected $hidden = [
        'unit_en',
        'unit_hu',
        'active',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function getUnitAttribute()
    {
        $locale = app()->getLocale();
        return $this->attributes["unit_{$locale}"];
    }

    public function setUnitAttribute($value)
    {
        $locale = app()->getLocale();
        $this->attributes["unit_{$locale}"] = $value;
        return $this;
    }

    public function drink(): BelongsTo
    {
        return $this->belongsTo(Drink::class, );
    }
}
