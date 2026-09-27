<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;

class User extends Model
{
    use HasFactory, Notifiable, SanctumHasApiTokens;

    protected $fillable = [
        'email',
        'password',
        'nickname',
        'name',
        'avatar',
        'bio',
        'github_id',
        'github_username',
        'email_verified_at',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function knowledgeBases()
    {
        return $this->hasMany(KnowledgeBase::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function isAdmin()
    {
        return $this->hasRole('system-admin') || $this->hasRole('content-admin');
    }

    public function isSystemAdmin()
    {
        return $this->hasRole('system-admin');
    }

    public function isContentAdmin()
    {
        return $this->hasRole('content-admin');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('status', '!=', 'deleted');
    }
}
