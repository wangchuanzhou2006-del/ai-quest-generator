<?php

namespace App\Services\OpenAI;

use App\Exceptions\QuestGenerationException;
use App\Services\Contracts\QuestGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class OpenAiQuestGenerator implements QuestGenerator
{
    private $client;
    private $apiKey;
    private $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model');

        $clientConfig = [
            'base_uri' => config('services.openai.base_uri'),
            'timeout' => (int) config('services.openai.timeout', 30),
        ];

        $proxy = config('services.openai.proxy');
        if ($proxy) {
            $clientConfig['proxy'] = $proxy;
        }

        $this->client = new Client($clientConfig);
    }

    public function generate(array $parameters)
    {
        if (empty($this->apiKey)) {
            throw new QuestGenerationException('OpenAI API key is not configured.');
        }

        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'temperature' => 0.7,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You generate game quests. Return only valid JSON with keys: title, description, objectives.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $this->buildPrompt($parameters),
                        ],
                    ],
                ],
            ]);
        } catch (GuzzleException $exception) {
            Log::error('OpenAI API request failed: ' . $exception->getMessage());
            throw new QuestGenerationException('The AI service is temporarily unavailable. Please try again.');
        }

        $payload = json_decode((string) $response->getBody(), true);
        $choice = isset($payload['choices'][0]) ? $payload['choices'][0] : null;
        $content = isset($choice['message']['content']) ? $choice['message']['content'] : null;

        if (! is_string($content) || trim($content) === '') {
            throw new QuestGenerationException('The AI service returned an empty response. Please try again.');
        }

        $quest = $this->parseQuest($content);
        $usage = isset($payload['usage']) && is_array($payload['usage']) ? $payload['usage'] : [];

        return [
            'title' => $quest['title'],
            'description' => $quest['description'],
            'objectives' => $quest['objectives'],
            'raw_text' => $content,
            'provider' => 'openai',
            'model' => isset($payload['model']) ? $payload['model'] : $this->model,
            'provider_request_id' => isset($payload['id']) ? $payload['id'] : null,
            'usage' => [
                'prompt_tokens' => isset($usage['prompt_tokens']) ? $usage['prompt_tokens'] : null,
                'completion_tokens' => isset($usage['completion_tokens']) ? $usage['completion_tokens'] : null,
                'total_tokens' => isset($usage['total_tokens']) ? $usage['total_tokens'] : null,
            ],
            'metadata' => [
                'finish_reason' => isset($choice['finish_reason']) ? $choice['finish_reason'] : null,
            ],
        ];
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
            "- Special requirements: " . ($parameters['special_requirements'] ?: 'none') . "\n\n" .
            "Return valid JSON only with this structure:\n" .
            "{\"title\":\"short quest title\",\"description\":\"2-4 paragraph quest description\",\"objectives\":[\"objective 1\",\"objective 2\",\"objective 3\"]}";
    }

    private function parseQuest($content)
    {
        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            throw new QuestGenerationException('The AI returned an invalid quest format. Please try again.');
        }

        $title = isset($decoded['title']) ? trim((string) $decoded['title']) : '';
        $description = isset($decoded['description']) ? trim((string) $decoded['description']) : '';
        $objectives = isset($decoded['objectives']) && is_array($decoded['objectives']) ? $decoded['objectives'] : [];

        $objectives = array_values(array_filter(array_map(function ($objective) {
            return trim((string) $objective);
        }, $objectives)));

        $objectives = array_slice($objectives, 0, 10);

        if ($title === '' || $description === '' || count($objectives) === 0) {
            throw new QuestGenerationException('The AI returned an invalid quest format. Please try again.');
        }

        return [
            'title' => $title,
            'description' => $description,
            'objectives' => $objectives,
        ];
    }
}
