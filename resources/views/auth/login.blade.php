@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>登录</h1>
        <p class="muted">登录后可以管理你的 AI 游戏任务。</p>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">邮箱</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">密码</label>
            <input id="password" type="password" name="password" required>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label>
                <input type="checkbox" name="remember" value="1">
                记住我
            </label>

            <button type="submit">登录</button>
        </form>

        <p class="muted">还没有账号？<a href="{{ route('register') }}">立即注册</a></p>
    </div>
@endsection
