<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Salary
 *
 * @package Modules\HR\Models
 */
class Salary extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'employee_id',
        'basic_salary',
        'transport_allowance',
        'housing_allowance',
        'absences_deduction',
        'advances_deduction',
        // 'bonus',
        'net_salary',
        'month',
        // 'year',
        // 'payment_date',
        'payment_status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'basic_salary' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'absences_deduction' => 'decimal:2',
        'advances_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'month' => 'date',
        // 'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the salary record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

}
