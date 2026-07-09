<?php

namespace Tests\Feature;

use App\Models\AiGeneration;
use App\Models\PlayerQuest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerQuestDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_player_quests()
    {
        $user = User::factory()->create();
        $quest = PlayerQuest::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('player_quests', [
            'id' => $quest->id,
            'user_id' => $user->id,
        ]);
        $this->assertTrue($user->playerQuests->contains($quest));
    }

    public function test_player_quest_can_be_created_from_ai_generation()
    {
        $user = User::factory()->create();
        $generation = AiGeneration::factory()->quest()->create(['user_id' => $user->id]);
        $quest = PlayerQuest::factory()->create([
            'user_id' => $user->id,
            'ai_generation_id' => $generation->id,
        ]);

        $this->assertTrue($generation->playerQuests->contains($quest));
        $this->assertTrue($quest->aiGeneration->is($generation));
    }

    public function test_deleting_ai_generation_nulls_player_quest_source()
    {
        $user = User::factory()->create();
        $generation = AiGeneration::factory()->create(['user_id' => $user->id]);
        $quest = PlayerQuest::factory()->create([
            'user_id' => $user->id,
            'ai_generation_id' => $generation->id,
        ]);

        $generation->delete();
        $quest->refresh();

        $this->assertNull($quest->ai_generation_id);
    }

    public function test_deleting_user_deletes_player_quests()
    {
        $user = User::factory()->create();
        $quest = PlayerQuest::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('player_quests', [
            'id' => $quest->id,
        ]);
    }
}
