<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'tags',
        'is_shared',
        'share_permission',
        'share_id',
        'share_expires_at',
        'view_count',
        'favorite_count',
        'status'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_shared' => 'boolean',
        'share_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function entries()
    {
        return $this->hasMany(KnowledgeEntry::class, 'knowledge_base_id');
    }

    public function categories()
    {
        return $this->hasMany(KnowledgeBaseCategory::class, 'knowledge_base_id');
    }

    public function shareRecords()
    {
        return $this->hasMany(ShareRecord::class, 'knowledge_base_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'knowledge_base_id');
    }

    public function isOwner($userId)
    {
        return $this->user_id === $userId;
    }

    public function canView($userId)
    {
        if ($this->isOwner($userId)) {
            return true;
        }
        if ($this->is_shared && $this->status === 'published') {
            return true;
        }
        return false;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }

    public function scopeByOwner($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
