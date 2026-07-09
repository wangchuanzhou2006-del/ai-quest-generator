<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateQuestRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'genre' => ['required', 'string', 'max:80'],
            'setting' => ['required', 'string', 'max:120'],
            'difficulty' => ['required', 'in:easy,normal,hard,deadly'],
            'character_level' => ['required', 'integer', 'min:1', 'max:100'],
            'party_size' => ['required', 'integer', 'min:1', 'max:12'],
            'quest_type' => ['required', 'in:exploration,combat,mystery,diplomacy,rescue,treasure_hunt'],
            'tone' => ['nullable', 'string', 'max:80'],
            'special_requirements' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function questParameters()
    {
        return $this->validated();
    }
}
