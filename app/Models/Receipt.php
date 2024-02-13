<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends Model
{
    use HasFactory;
    /**
     * serno: string
     * guest_id: integer
     * issued_at: datetime
     * paid_for: integer
     * paid_at: datetime
     * payment_method: string ('készpénz', 'bankkártya')
     * table: ?string
     *
     * Relations
     *
     * serno ux
     * guest_id => guest.id
     * paid_for => employee.id
     * receipt.id <= order_detail.receipt_id
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'serno',
        'guest_id',
        'issued_at',
        'paid_for',
        'paid_at',
        'payment_method',
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

    function details(): HasMany {
        return $this->hasMany(OrderDetail::class, 'receipt_id', 'id');
    }

    function guest(): BelongsTo {
        return $this->belongsTo(Guest::class, 'guest_id', 'id');
    }
}
