<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <style>
        .login-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-box {
            width: 400px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .card-header {
            background: transparent;
            border-bottom: none;
            padding: 30px 30px 0;
        }
        .card-header .h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        .card-body {
            padding: 30px;
        }
        .login-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 30px;
            font-weight: 400;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            height: auto;
        }
        .input-group-text {
            border-radius: 0 8px 8px 0;
            background-color: #f8f9fa;
            border-left: none;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .links-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .links-section a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.2s;
        }
        .links-section a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .icheck-primary label {
            font-weight: 400;
            color: #6c757d;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline">
        <div class="card-header text-center">
            <a href="{{ route('login') }}" class="h1">
                <i class="fas fa-store text-primary"></i> <b>StoreX</b>
            </a>
            <p class="login-subtitle">Welcome back! Please login to your account</p>
        </div>
        <div class="card-body">
            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email/Username/Phone -->
                <div class="input-group mb-3">
                    <input type="text" 
                           name="{{ config('fortify.username') }}" 
                           class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Email, Phone, or Username"
                           value="{{ old('email') }}"
                           required 
                           autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input-group mb-3">
                    <input type="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Password"
                           required 
                           autocomplete="current-password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row align-items-center mb-3">
                    <div class="col-7">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                </div>
            </form>

            <div class="links-section text-center">
                @if (Route::has('password.request'))
                    <p class="mb-2">
                        <a href="{{ route('password.request') }}">
                            <i class="fas fa-key"></i> Forgot your password?
                        </a>
                    </p>
                @endif
                
                @if (Route::has('register'))
                    <p class="mb-0">
                        Don't have an account? 
                        <a href="{{ route('register') }}"><strong>Sign up</strong></a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
</body>
</html>