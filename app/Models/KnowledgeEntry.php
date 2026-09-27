<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_base_id',
        'knowledge_id',
        'category_id',
        'notes',
        'order'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBase::class, 'knowledge_base_id');
    }

    public function knowledge()
    {
        return $this->belongsTo(Knowledge::class, 'knowledge_id');
    }

    public function category()
    {
        return $this->belongsTo(KnowledgeBaseCategory::class, 'category_id');
    }
}
