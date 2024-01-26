<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'parent_id',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DrinkCategory::class, 'parent_id', 'id');
    }

    public function drinks(): HasMany
    {
        return $this->hasMany(Drink::class, 'category_id', 'id');
    }
}
