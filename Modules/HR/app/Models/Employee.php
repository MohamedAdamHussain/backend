<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Models\User;

/**
 * Class Employee
 *
 * @package Modules\HR\Models
 */
class Employee extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'occupation',
        'email',
        'phone_number',
        'hire_date',
        'is_active',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the departments for the employee.
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'employee_departments')->withPivot('start_date', 'end_date', 'type_of_working')
                ->withTimestamps();
    }

    /**
     * Get the attendances for the employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the absences for the employee.
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Get the advances for the employee.
     */
    public function advances(): HasMany
    {
        return $this->hasMany(Advance::class);
    }

    /**
     * Get the salaries for the employee.
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    /**
     * Get the user that owns the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
