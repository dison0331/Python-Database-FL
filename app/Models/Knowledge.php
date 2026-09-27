<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Knowledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'version',
        'category',
        'tags',
        'author',
        'source',
        'is_system',
        'order',
        'status'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_system' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function entries()
    {
        return $this->hasMany(KnowledgeEntry::class, 'knowledge_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'knowledge_id');
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', '%' . $keyword . '%')
              ->orWhere('content', 'like', '%' . $keyword . '%')
              ->orWhere('tags', 'like', '%' . $keyword . '%');
        });
    }
}
