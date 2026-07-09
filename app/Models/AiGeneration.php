<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiGeneration extends Model
{
    use HasFactory;

    const TYPE_QUEST = 'quest';
    const TYPE_NPC_DIALOGUE = 'npc_dialogue';
    const TYPE_STORY = 'story';

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'prompt',
        'prompt_parameters',
        'generated_text',
        'status',
        'provider',
        'model',
        'provider_request_id',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'cost_usd',
        'error_message',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'prompt_parameters' => 'array',
        'metadata' => 'array',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'cost_usd' => 'decimal:6',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playerQuests()
    {
        return $this->hasMany(PlayerQuest::class);
    }
}
