<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description_en',
        'description_hu',
    ];

    protected $appends = ['description'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $this->attributes["description_{$locale}"];
    }

    public function setDescriptionAttribute($value)
    {
        $locale = app()->getLocale();
        $this->attributes["description_{$locale}"] = $value;
        return $this;
    }

}
