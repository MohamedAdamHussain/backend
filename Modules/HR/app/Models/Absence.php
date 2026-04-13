<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Absence
 *
 * @package Modules\HR\Models
 */
class Absence extends Model
{

    /**
      * The attributes that are mass assignable.
      *
      * @var array<int, string>
      */
     protected $fillable = [
         'employee_id',
         'date',
         'reason',
         'approved_by',
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
     * Get the employee that owns the absence.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    /**
     * Get the approver of the absence.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
