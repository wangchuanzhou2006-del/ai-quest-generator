@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>生成 AI 游戏任务</h1>
        <p class="muted">填写任务参数，AI 会生成任务标题、剧情描述和目标列表。</p>

        @error('generation')
            <div class="error">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('quests.generate.store') }}">
            @csrf

            <label for="genre">游戏类型</label>
            <input id="genre" type="text" name="genre" value="{{ old('genre', 'fantasy') }}" required>
            @error('genre')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="setting">任务场景</label>
            <input id="setting" type="text" name="setting" value="{{ old('setting') }}" placeholder="例如：被诅咒的山村、废弃太空站" required>
            @error('setting')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="difficulty">难度</label>
            <select id="difficulty" name="difficulty" required>
                @foreach(['easy' => '简单', 'normal' => '普通', 'hard' => '困难', 'deadly' => '致命'] as $value => $label)
                    <option value="{{ $value }}" @if(old('difficulty', 'normal') === $value) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
            @error('difficulty')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="character_level">角色等级</label>
            <input id="character_level" type="number" name="character_level" min="1" max="100" value="{{ old('character_level', 1) }}" required>
            @error('character_level')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="party_size">队伍人数</label>
            <input id="party_size" type="number" name="party_size" min="1" max="12" value="{{ old('party_size', 4) }}" required>
            @error('party_size')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="quest_type">任务类型</label>
            <select id="quest_type" name="quest_type" required>
                @foreach(['exploration' => '探索', 'combat' => '战斗', 'mystery' => '悬疑', 'diplomacy' => '外交', 'rescue' => '营救', 'treasure_hunt' => '寻宝'] as $value => $label)
                    <option value="{{ $value }}" @if(old('quest_type', 'exploration') === $value) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
            @error('quest_type')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="tone">任务风格</label>
            <input id="tone" type="text" name="tone" value="{{ old('tone') }}" placeholder="例如：黑暗、英雄、幽默">
            @error('tone')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="special_requirements">特殊要求</label>
            <textarea id="special_requirements" name="special_requirements" placeholder="例如：必须包含一个背叛角色，结尾有两个选择">{{ old('special_requirements') }}</textarea>
            @error('special_requirements')
                <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit">生成任务</button>
        </form>
    </div>
@endsection
