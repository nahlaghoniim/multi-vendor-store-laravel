<x-front-layout title="Two Factor Authentication">

    <!-- Start Account Login Area -->
    <div class="account-login section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-md-10 offset-md-1 col-12">

                    <form class="card login-form" 
                          action="{{ auth()->user()->two_factor_secret ? route('two-factor.disable') : route('two-factor.enable') }}" 
                          method="post">
                        @csrf

                        @if(auth()->user()->two_factor_secret)
                            @method('delete')
                        @endif

                        <div class="card-body">
                            <div class="title">
                                <h3>Two Factor Authentication</h3>
                                <p>You can enable or disable 2FA for your account.</p>
                            </div>

                            @if (session('status') == 'two-factor-authentication-enabled')
                                <div class="mb-4 font-medium text-sm text-green-600">
                                    Please finish configuring two-factor authentication below.
                                </div>
                            @endif

                            <div class="button">
                                @if (!auth()->user()->two_factor_secret)
                                    <button class="btn btn-primary" type="submit">Enable 2FA</button>
                                @else
                                    <div class="p-4">
                                        {!! auth()->user()->twoFactorQrCodeSvg() !!}
                                    </div>

                                    <h4>Recovery Codes</h4>
                                    <ul class="mb-3">
                                        @foreach(auth()->user()->recoveryCodes() as $code)
                                            <li>{{ $code }}</li>
                                        @endforeach
                                    </ul>

                                    <button class="btn btn-danger" type="submit">Disable 2FA</button>
                                @endif
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
    <!-- End Account Login Area -->

</x-front-layout>
