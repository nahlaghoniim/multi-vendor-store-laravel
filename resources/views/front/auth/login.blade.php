<x-front-layout title="Login">

<!-- Start Account Login Area -->
<div class="account-login section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-md-10 offset-md-1 col-12">
                    <form class="card login-form" action="{{ route('login') }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="title">
                                <h3>Login Now</h3>
                                <p>You can login using your social media account or email address.</p>
                            </div>
<div class="social-login mb-4">
    <div class="d-grid gap-2">
    <!-- Google Login -->
    <a href="{{ route('auth.google') }}" class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2">
        <img src="https://developers.google.com/identity/images/g-logo.png"
             alt="Google"
             width="18"
             height="18">
        <span>Continue with Google</span>
    </a>

    <!-- Facebook Login -->
    <a href="{{ url('login/facebook') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
        <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png"
             alt="Facebook"
             width="18"
             height="18">
        <span>Continue with Facebook</span>
    </a>
</div>



<div class="alt-option text-center mb-4">
    <span>or login with email</span>
</div>
                          {{--  <div class="social-login">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-12"><a class="btn facebook-btn"
                                            href="{{ route('auth.socilaite.redirect', 'facebook') }}"><i class="lni lni-facebook-filled"></i> Facebook
                                            login</a></div>
                                    <div class="col-lg-4 col-md-4 col-12"><a class="btn twitter-btn"
                                            href="javascript:void(0)"><i class="lni lni-twitter-original"></i> Twitter
                                            login</a></div>
                                  <div class="col-lg-4 col-md-4 col-12"><a class="btn google-btn"
                                            href="{{ route('auth.socilaite.redirect', 'google') }}"><i class="lni lni-google"></i> Google login</a>
                                    </div>
                                </div>
                            </div>
                            <div class="alt-option">
                                <span>Or</span>
                            </div> --}}
                            @if ($errors->has(config('fortify.username')))
                            <div class="alert alert-danger">
                                {{ $errors->first(config('fortify.username')) }}
                            </div>
                            @endif
                            <div class="form-group input-group">
                                <label for="reg-fn">Email</label>
                                <input class="form-control" type="text" name="{{ config('fortify.username') }}" id="reg-email" required>
                            </div>
                            <div class="form-group input-group">
                                <label for="reg-fn">Password</label>
                                <input class="form-control" type="password" name="password" id="reg-pass" required>
                            </div>
                            <div class="d-flex flex-wrap justify-content-between bottom-content">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" value="1" class="form-check-input width-auto" id="exampleCheck1">
                                    <label class="form-check-label">Remember me</label>
                                </div>
                                @if (Route::has('password.request'))
                                <a class="lost-pass" href="{{ route('password.request') }}">Forgot password?</a>
                                @endif
                            </div>
                            <div class="button">
                                <button class="btn" type="submit">Login</button>
                            </div>
                            @if (Route::has('register'))
                            <p class="outer-link">Don't have an account? <a href="{{ route('register') }}">Register here </a>
                            </p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Account Login Area -->

</x-front-layout>