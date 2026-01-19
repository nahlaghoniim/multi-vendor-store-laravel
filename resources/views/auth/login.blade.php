<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }} | Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ShopGrid CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/LineIcons.3.0.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #111827, #1f2933);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            border-radius: 18px;
            padding: 40px;
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.5);
            color: #fff;
        }

        .auth-card h3 {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .auth-card p {
            font-size: 0.95rem;
            opacity: 0.85;
            margin-bottom: 30px;
        }

        .auth-card .form-control {
            background: rgba(255,255,255,0.1);
            border: none;
            color: #fff;
            padding: 14px 16px;
            border-radius: 10px;
        }

        .auth-card .form-control::placeholder {
            color: rgba(255,255,255,0.6);
        }

        .auth-card .form-control:focus {
            background: rgba(255,255,255,0.15);
            box-shadow: none;
            outline: none;
        }

        .auth-card .btn {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            font-weight: 600;
        }

        .auth-card .btn:hover {
            transform: translateY(-1px);
        }

        .auth-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        .auth-meta a {
            color: #c7d2fe;
        }

        .invalid-feedback {
            color: #fca5a5;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>

<div class="auth-card">
    <h3>Admin Login</h3>
    <p>Secure access to the dashboard</p>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <input
                type="text"
                name="{{ config('fortify.username') }}"
                class="form-control @error(config('fortify.username')) is-invalid @enderror"
                placeholder="Email or Username"
                value="{{ old(config('fortify.username')) }}"
                required
                autofocus
            >
            @error(config('fortify.username'))
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <input
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Password"
                required
            >
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-meta">
            <label>
                <input type="checkbox" name="remember"> Remember
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot?</a>
            @endif
        </div>

        <button type="submit" class="btn">
            Sign in
        </button>
    </form>
</div>

<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
