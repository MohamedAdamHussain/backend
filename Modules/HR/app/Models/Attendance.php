<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Attendance
 *
 * @package Modules\HR\Models
 */
class Attendance extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'employee_id',
        'check_in_time',
        'check_out_time',
        'date',
        'checked_by',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the attendance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'checked_by');
    }
}
