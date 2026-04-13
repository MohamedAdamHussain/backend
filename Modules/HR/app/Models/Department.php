<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Department
 *
 * @package Modules\HR\Models
 */
class Department extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employees for the department.
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_departments')
            ->withPivot('start_date', 'end_date', 'type_of_working')
            ->withTimestamps();
    }
}
