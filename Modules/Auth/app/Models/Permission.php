<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Auth\Database\Factories\PermissionFactory;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'module',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission');
    }

    // protected static function newFactory(): PermissionFactory
    // {
    //     // return PermissionFactory::new();
    // }
}
