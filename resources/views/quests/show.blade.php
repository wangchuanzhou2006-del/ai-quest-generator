@extends('layouts.app')

@section('content')
    <div class="card">
        @if(session('status'))
            <p class="muted">{{ session('status') }}</p>
        @endif

        <h1>{{ $quest->title }}</h1>
        <p class="muted">状态：{{ $quest->status }}</p>

        <h2>任务描述</h2>
        <p>{!! nl2br(e($quest->description)) !!}</p>

        <h2>任务目标</h2>
        <ul>
            @foreach($quest->objectives ?: [] as $objective)
                <li>{{ $objective }}</li>
            @endforeach
        </ul>

        @if($quest->aiGeneration)
            <p class="muted">
                生成模型：{{ $quest->aiGeneration->model ?: 'unknown' }}
                @if($quest->aiGeneration->total_tokens)
                    ，Token：{{ $quest->aiGeneration->total_tokens }}
                @endif
            </p>
        @endif

        <a class="button" href="{{ route('quests.generate.create') }}">继续生成任务</a>
    </div>
@endsection
