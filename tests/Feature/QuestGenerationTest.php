<?php

namespace Tests\Feature;

use App\Exceptions\QuestGenerationException;
use App\Models\AiGeneration;
use App\Models\PlayerQuest;
use App\Models\User;
use App\Services\Contracts\QuestGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_generation_page_to_login()
    {
        $response = $this->get('/quests/generate');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_generation_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/quests/generate');

        $response->assertStatus(200);
        $response->assertSee('生成 AI 游戏任务');
    }

    public function test_invalid_submission_does_not_create_records()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/quests/generate', []);

        $response->assertSessionHasErrors(['genre', 'setting', 'difficulty']);
        $this->assertDatabaseCount('ai_generations', 0);
        $this->assertDatabaseCount('player_quests', 0);
    }

    public function test_successful_generation_creates_ai_generation_and_player_quest()
    {
        $this->fakeSuccessfulGenerator();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/quests/generate', $this->validPayload());

        $quest = $user->playerQuests()->first();

        $response->assertRedirect(route('quests.show', $quest));
        $this->assertDatabaseHas('ai_generations', [
            'user_id' => $user->id,
            'type' => AiGeneration::TYPE_QUEST,
            'status' => AiGeneration::STATUS_COMPLETED,
            'provider' => 'openai',
            'model' => 'gpt-test',
            'prompt_tokens' => 10,
            'completion_tokens' => 20,
            'total_tokens' => 30,
        ]);
        $this->assertDatabaseHas('player_quests', [
            'user_id' => $user->id,
            'title' => 'The Sunken Forge',
            'status' => PlayerQuest::STATUS_NOT_STARTED,
        ]);
        $this->assertSame('The Sunken Forge', $quest->title);
        $this->assertSame(['Find the flooded mine entrance', 'Defeat the forge guardian', 'Recover the ember key'], $quest->objectives);
    }

    public function test_generated_quest_result_page_displays_the_quest()
    {
        $this->fakeSuccessfulGenerator();
        $user = User::factory()->create();

        $this->actingAs($user)->post('/quests/generate', $this->validPayload());
        $quest = $user->playerQuests()->first();

        $response = $this->actingAs($user)->get(route('quests.show', $quest));

        $response->assertStatus(200);
        $response->assertSee('The Sunken Forge');
        $response->assertSee('Recover the ember key');
        $response->assertSee('gpt-test');
    }

    public function test_generation_failure_saves_failed_ai_generation_without_player_quest()
    {
        $this->app->instance(QuestGenerator::class, new class implements QuestGenerator {
            public function generate(array $parameters)
            {
                throw new QuestGenerationException('The AI service is temporarily unavailable. Please try again.');
            }
        });

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/quests/generate', $this->validPayload());

        $response->assertSessionHasErrors('generation');
        $this->assertDatabaseHas('ai_generations', [
            'user_id' => $user->id,
            'status' => AiGeneration::STATUS_FAILED,
            'error_message' => 'The AI service is temporarily unavailable. Please try again.',
        ]);
        $this->assertDatabaseCount('player_quests', 0);
    }

    public function test_user_cannot_view_another_users_quest()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $quest = PlayerQuest::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->get(route('quests.show', $quest));

        $response->assertStatus(403);
    }

    private function fakeSuccessfulGenerator()
    {
        $this->app->instance(QuestGenerator::class, new class implements QuestGenerator {
            public function generate(array $parameters)
            {
                return [
                    'title' => 'The Sunken Forge',
                    'description' => 'Recover the ember key from a drowned dwarven forge.',
                    'objectives' => [
                        'Find the flooded mine entrance',
                        'Defeat the forge guardian',
                        'Recover the ember key',
                    ],
                    'raw_text' => '{"title":"The Sunken Forge","description":"Recover the ember key from a drowned dwarven forge.","objectives":["Find the flooded mine entrance","Defeat the forge guardian","Recover the ember key"]}',
                    'provider' => 'openai',
                    'model' => 'gpt-test',
                    'provider_request_id' => 'chatcmpl-test',
                    'usage' => [
                        'prompt_tokens' => 10,
                        'completion_tokens' => 20,
                        'total_tokens' => 30,
                    ],
                    'metadata' => [
                        'finish_reason' => 'stop',
                    ],
                ];
            }
        });
    }

    private function validPayload()
    {
        return [
            'genre' => 'fantasy',
            'setting' => 'flooded dwarven forge',
            'difficulty' => 'normal',
            'character_level' => 3,
            'party_size' => 4,
            'quest_type' => 'exploration',
            'tone' => 'heroic',
            'special_requirements' => 'Include a magical key.',
        ];
    }
}
