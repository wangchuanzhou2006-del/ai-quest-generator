<?php

namespace Database\Factories;

use App\Models\PlayerQuest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerQuestFactory extends Factory
{
    protected $model = PlayerQuest::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'ai_generation_id' => null,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'objectives' => [
                'Talk to the village elder',
                'Find the lost artifact',
                'Return for a reward',
            ],
            'status' => PlayerQuest::STATUS_NOT_STARTED,
            'notes' => $this->faker->sentence(),
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function inProgress()
    {
        return $this->state(function () {
            return [
                'status' => PlayerQuest::STATUS_IN_PROGRESS,
                'started_at' => now(),
                'completed_at' => null,
            ];
        });
    }

    public function completed()
    {
        return $this->state(function () {
            return [
                'status' => PlayerQuest::STATUS_COMPLETED,
                'started_at' => now()->subDay(),
                'completed_at' => now(),
            ];
        });
    }

    public function failed()
    {
        return $this->state(function () {
            return [
                'status' => PlayerQuest::STATUS_FAILED,
                'completed_at' => now(),
            ];
        });
    }

    public function archived()
    {
        return $this->state(function () {
            return ['status' => PlayerQuest::STATUS_ARCHIVED];
        });
    }
}
