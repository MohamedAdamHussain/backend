<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AdvanceRepayment
 *
 * @package Modules\HR\Models
 */
class AdvanceRepayment extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'advance_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the advance that owns the repayment.
     */
    public function advance(): BelongsTo
    {
        return $this->belongsTo(Advance::class);
    }
}
