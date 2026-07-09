<?php

namespace App\Services;

use App\Exceptions\QuestGenerationException;
use App\Models\AiGeneration;
use App\Models\PlayerQuest;
use App\Models\User;
use App\Services\Contracts\QuestGenerator;
use Illuminate\Support\Facades\DB;

class QuestGenerationService
{
    private $generator;

    public function __construct(QuestGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function generateForUser(User $user, array $parameters)
    {
        $generation = AiGeneration::create([
            'user_id' => $user->id,
            'type' => AiGeneration::TYPE_QUEST,
            'prompt' => $this->buildPrompt($parameters),
            'prompt_parameters' => $parameters,
            'status' => AiGeneration::STATUS_PROCESSING,
            'provider' => 'openai',
            'model' => config('services.openai.model'),
        ]);

        try {
            $result = $this->generator->generate($parameters);
        } catch (QuestGenerationException $exception) {
            $generation->update([
                'status' => AiGeneration::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);

            throw $exception;
        }

        return DB::transaction(function () use ($user, $generation, $result) {
            $usage = isset($result['usage']) && is_array($result['usage']) ? $result['usage'] : [];

            $generation->update([
                'title' => $result['title'],
                'generated_text' => $result['raw_text'],
                'status' => AiGeneration::STATUS_COMPLETED,
                'provider' => isset($result['provider']) ? $result['provider'] : 'openai',
                'model' => isset($result['model']) ? $result['model'] : config('services.openai.model'),
                'provider_request_id' => isset($result['provider_request_id']) ? $result['provider_request_id'] : null,
                'prompt_tokens' => isset($usage['prompt_tokens']) ? $usage['prompt_tokens'] : null,
                'completion_tokens' => isset($usage['completion_tokens']) ? $usage['completion_tokens'] : null,
                'total_tokens' => isset($usage['total_tokens']) ? $usage['total_tokens'] : null,
                'metadata' => isset($result['metadata']) ? $result['metadata'] : null,
                'completed_at' => now(),
            ]);

            $quest = PlayerQuest::create([
                'user_id' => $user->id,
                'ai_generation_id' => $generation->id,
                'title' => $result['title'],
                'description' => $result['description'],
                'objectives' => $result['objectives'],
                'status' => PlayerQuest::STATUS_NOT_STARTED,
            ]);

            return [
                'generation' => $generation,
                'quest' => $quest,
            ];
        });
    }

    private function buildPrompt(array $parameters)
    {
        return "Generate one game quest using these parameters:\n" .
            "- Genre: " . $parameters['genre'] . "\n" .
            "- Setting: " . $parameters['setting'] . "\n" .
            "- Difficulty: " . $parameters['difficulty'] . "\n" .
            "- Character level: " . $parameters['character_level'] . "\n" .
            "- Party size: " . $parameters['party_size'] . "\n" .
            "- Quest type: " . $parameters['quest_type'] . "\n" .
            "- Tone: " . ($parameters['tone'] ?: 'balanced') . "\n" .
            "- Special requirements: " . ($parameters['special_requirements'] ?: 'none');
    }
}
