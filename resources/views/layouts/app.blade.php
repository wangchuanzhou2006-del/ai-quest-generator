<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Quest Generator</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f7fb; color: #1f2937; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 16px 32px; background: #111827; color: white; }
        nav a, nav button { color: white; margin-left: 16px; text-decoration: none; background: none; border: 0; cursor: pointer; font: inherit; }
        main { max-width: 720px; margin: 48px auto; padding: 0 24px; }
        .card { background: white; border-radius: 12px; padding: 32px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
        label { display: block; margin: 16px 0 8px; font-weight: 700; }
        input { box-sizing: border-box; width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; }
        input[type="checkbox"] { width: auto; }
        button, .button { display: inline-block; margin-top: 20px; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: white; cursor: pointer; text-decoration: none; }
        .error { margin-top: 6px; color: #dc2626; font-size: 14px; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ url('/') }}">AI Quest Generator</a>
        <div>
            @auth
                <span>{{ auth()->user()->name }}</span>
                <a href="{{ route('home') }}">首页</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit">退出</button>
                </form>
            @else
                <a href="{{ route('login') }}">登录</a>
                <a href="{{ route('register') }}">注册</a>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>
