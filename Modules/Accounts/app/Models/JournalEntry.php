<?php

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;

class JournalEntry extends Model
{
    protected $fillable = [
        'created_by',
        'entry_date',
        'description',
        'reference_type',
        'reference_id'
    ];

    public function lines()
{
    return $this->hasMany(JournalEntryLine::class);
}

    public function reference()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
