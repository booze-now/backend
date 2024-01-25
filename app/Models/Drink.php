<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Drink extends Model
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
        'category_id',
        'description_en',
        'description_hu',
        'status',
    ];

    private static $internalFieldDefs = [
        "id",
        'name_en',
        'name_hu',
        'category_id',
        'description_en',
        'description_hu',
        "status",
        "created_at",
        "updated_at"
    ];

    private static $statuses = [
        'active',
        'inactive',
    ];

    public function category2(): BelongsTo {
        return $this->belongsTo(DrinkCategory::class, 'category_id', 'id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(DrinkCategory::class, 'id', 'category_id');
    }

    public function measures(): HasMany
    {
        return $this->hasMany(DrinkMeasure::class);
    }

    public static function getStatuses(): array
    {
        return array_map(
            fn ($v): string =>
            __("drink.{$v}") ?? $v,
            Drink::$statuses
        );
    }

    public static function getStatusValue($name): string
    {
        $idx = array_search($name, Drink::getStatuses(), true);
        return ($idx >= 0)? Drink::$statuses[$idx] : $name;
    }

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
