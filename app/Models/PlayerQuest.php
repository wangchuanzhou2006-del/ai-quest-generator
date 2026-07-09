<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerQuest extends Model
{
    use HasFactory;

    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'user_id',
        'ai_generation_id',
        'title',
        'description',
        'objectives',
        'status',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'objectives' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aiGeneration()
    {
        return $this->belongsTo(AiGeneration::class);
    }
}
