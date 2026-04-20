<?php

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Accounts\Models\JournalEntryLine;

class Account extends Model
{
    protected $table = 'accounts';
    protected $fillable = [
        'name',
        'slug',
        'type',
        'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalEntryLines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }
}
