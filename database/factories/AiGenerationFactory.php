<?php

namespace Database\Factories;

use App\Models\AiGeneration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiGenerationFactory extends Factory
{
    protected $model = AiGeneration::class;

    public function definition()
    {
        $promptTokens = $this->faker->numberBetween(200, 900);
        $completionTokens = $this->faker->numberBetween(500, 2000);

        return [
            'user_id' => User::factory(),
            'type' => AiGeneration::TYPE_QUEST,
            'title' => $this->faker->sentence(4),
            'prompt' => 'Generate a fantasy game quest for a player.',
            'prompt_parameters' => [
                'genre' => 'fantasy',
                'difficulty' => 'normal',
            ],
            'generated_text' => $this->faker->paragraphs(3, true),
            'status' => AiGeneration::STATUS_COMPLETED,
            'provider' => 'openai',
            'model' => 'gpt-placeholder',
            'provider_request_id' => $this->faker->uuid(),
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $promptTokens + $completionTokens,
            'cost_usd' => $this->faker->randomFloat(6, 0.001, 0.05),
            'error_message' => null,
            'metadata' => [
                'temperature' => 0.7,
            ],
            'completed_at' => now(),
        ];
    }

    public function quest()
    {
        return $this->state(function () {
            return ['type' => AiGeneration::TYPE_QUEST];
        });
    }

    public function npcDialogue()
    {
        return $this->state(function () {
            return ['type' => AiGeneration::TYPE_NPC_DIALOGUE];
        });
    }

    public function story()
    {
        return $this->state(function () {
            return ['type' => AiGeneration::TYPE_STORY];
        });
    }

    public function pending()
    {
        return $this->state(function () {
            return [
                'status' => AiGeneration::STATUS_PENDING,
                'generated_text' => null,
                'completed_at' => null,
            ];
        });
    }

    public function processing()
    {
        return $this->state(function () {
            return [
                'status' => AiGeneration::STATUS_PROCESSING,
                'generated_text' => null,
                'completed_at' => null,
            ];
        });
    }

    public function completed()
    {
        return $this->state(function () {
            return [
                'status' => AiGeneration::STATUS_COMPLETED,
                'completed_at' => now(),
            ];
        });
    }

    public function failed()
    {
        return $this->state(function () {
            return [
                'status' => AiGeneration::STATUS_FAILED,
                'generated_text' => null,
                'error_message' => 'The AI provider returned an error.',
                'completed_at' => now(),
            ];
        });
    }
}
