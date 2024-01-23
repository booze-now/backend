<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'status',
    ];

    private static $statuses = [
        'active',
        'inactive',
    ];

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
}
