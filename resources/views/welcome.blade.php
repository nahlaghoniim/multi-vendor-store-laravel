<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <h1>Welcome</h1>

    @auth
        <a href="{{ route('dashboard') }}">Dashboard</a>
    @else
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
    @endauth
</body>
</html>
