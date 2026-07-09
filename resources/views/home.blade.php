@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>登录成功</h1>
        <p>欢迎，{{ auth()->user()->name }}。</p>
        <p class="muted">当前邮箱：{{ auth()->user()->email }}</p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">退出登录</button>
        </form>
    </div>
@endsection
