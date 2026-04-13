<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }

    private array $permissionsCache = [];

    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissionsCache)) {
            $direct = $this->permissions->pluck('name')->toArray();
            $viaRoles = $this->roles->flatMap->permissions->pluck('name')->toArray();

            $this->permissionsCache = array_unique(array_merge($direct, $viaRoles));
        }

        return in_array($permission, $this->permissionsCache);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
