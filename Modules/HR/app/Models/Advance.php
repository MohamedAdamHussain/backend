<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Advance
 *
 * @package Modules\HR\Models
 */
class Advance extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'amount',
        'approved_by',
        'request_date',
        'approval_date',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'request_date' => 'date',
        'approval_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the advance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the repayments for the advance.
     */
    public function repayments(): HasMany
    {
        return $this->hasMany(AdvanceRepayment::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
