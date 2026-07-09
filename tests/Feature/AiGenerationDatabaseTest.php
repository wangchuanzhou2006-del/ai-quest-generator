<?php

namespace Tests\Feature;

use App\Models\AiGeneration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiGenerationDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_ai_generations()
    {
        $user = User::factory()->create();
        $generation = AiGeneration::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('ai_generations', [
            'id' => $generation->id,
            'user_id' => $user->id,
        ]);
        $this->assertTrue($user->aiGenerations->contains($generation));
    }

    public function test_ai_generation_stores_prompt_response_and_usage_metadata()
    {
        $generation = AiGeneration::factory()->create([
            'prompt' => 'Generate a desert quest.',
            'generated_text' => 'Recover the sun crystal from the buried temple.',
            'provider' => 'openai',
            'model' => 'gpt-test',
            'prompt_tokens' => 120,
            'completion_tokens' => 340,
            'total_tokens' => 460,
            'cost_usd' => '0.012345',
        ]);

        $generation->refresh();

        $this->assertSame('Generate a desert quest.', $generation->prompt);
        $this->assertSame('Recover the sun crystal from the buried temple.', $generation->generated_text);
        $this->assertSame('openai', $generation->provider);
        $this->assertSame('gpt-test', $generation->model);
        $this->assertSame(120, $generation->prompt_tokens);
        $this->assertSame(340, $generation->completion_tokens);
        $this->assertSame(460, $generation->total_tokens);
        $this->assertSame('0.012345', $generation->cost_usd);
    }

    public function test_ai_generation_casts_structured_json_fields()
    {
        $generation = AiGeneration::factory()->create([
            'prompt_parameters' => [
                'genre' => 'sci-fi',
                'difficulty' => 'hard',
            ],
            'metadata' => [
                'temperature' => 0.8,
                'seed' => 42,
            ],
        ]);

        $generation->refresh();

        $this->assertSame('sci-fi', $generation->prompt_parameters['genre']);
        $this->assertSame('hard', $generation->prompt_parameters['difficulty']);
        $this->assertSame(0.8, $generation->metadata['temperature']);
        $this->assertSame(42, $generation->metadata['seed']);
    }

    public function test_deleting_user_deletes_ai_generations()
    {
        $user = User::factory()->create();
        $generation = AiGeneration::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('ai_generations', [
            'id' => $generation->id,
        ]);
    }
}
