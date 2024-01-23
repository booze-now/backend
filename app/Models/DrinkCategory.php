<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_en',
        'name_hu',
        'parent',
    ];

    protected $appends = ['name'];
    protected $hidden = [
        'name_en',
        'name_hu',
        'created_at',
        'updated_at',
    ];

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $this->attributes["name_{$locale}"];
    }

    public function setNameAttribute($value)
    {
        $locale = app()->getLocale();
        $this->attributes["name_{$locale}"] = $value;

        return $this;
    }
}
