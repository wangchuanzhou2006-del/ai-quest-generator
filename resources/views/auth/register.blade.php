@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>注册</h1>
        <p class="muted">创建账号后即可开始生成和管理游戏任务。</p>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <label for="name">用户名</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="email">邮箱</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">密码</label>
            <input id="password" type="password" name="password" required>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">确认密码</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>

            <button type="submit">注册</button>
        </form>

        <p class="muted">已有账号？<a href="{{ route('login') }}">去登录</a></p>
    </div>
@endsection
