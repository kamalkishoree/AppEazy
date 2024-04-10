<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.shared.title-meta', ['title' => "Log In"])
        @include('layouts.shared.head-content')
        <script src="{{asset('assets/js/jquery-3.1.1.min.js')}}"></script>
        <script src="{{asset('assets/js/vendor.min.js')}}"></script>
        <script src="{{asset('assets/js/jquery-ui.min.js')}}" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <style type="text/css">
            
            body.authentication-bg {
                background-color: <?= ($client_preference_detail) ? $client_preference_detail->web_color : '#ff4c3b' ?>;
                background-size: cover;
                background-position: center;
            }
            .primary_bg_color{
                background-color: <?= ($client_preference_detail) ? $client_preference_detail->web_color : '#ff4c3b' ?>;
            }
            
            body.authentication-bg-pattern {
                background-image: url('https://images.royoorders.com/insecure/fill/600/400/ce/0/plain/https://s3.us-west-2.amazonaws.com/royoorders2.0-assets/{{ getAdditionalPreference(['admin_signin_image'])['admin_signin_image'] }}');
            }
        </style>
    
    </head>
    @php
    $clientData = App\Models\Client::first();
    @endphp
    <body class="authentication-bg authentication-bg-pattern">
        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card bg-pattern">
                            <div class="card-body p-4">
                                <div class="text-center w-75 m-auto">
                                    <div class="auth-logo">
                                        <a href="{{route('client.index')}}" class="logo logo-dark text-center">
                                            <span class="logo-lg">
                                                <img src="{{$clientData ? $clientData->logo['image_fit'].'200/100'.$clientData->logo['image_path']:asset('assets/images/logo-dark.png')}}" alt="" height="50">
                                            </span>
                                        </a>
                                        <a href="{{route('client.index')}}" class="logo logo-light text-center">
                                            <span class="logo-lg">
                                                <img src="{{asset('assets/images/logo-light.png')}}" alt="" height="40">
                                            </span>
                                        </a>
                                    </div>
                                    <p class="text-muted mb-4 mt-3">Enter your email address and password to access admin panel.</p>
                                </div>
                                <form action="{{route('client.login')}}" method="POST" novalidate>
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="emailaddress">Email address</label>
                                        <input class="form-control  @if($errors->has('email')) is-invalid @endif" name="email" type="email" id="emailaddress" required="" value="{{ old('email')}}" placeholder="Enter your email" />
                                        @if($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password">Password</label>
                                        <div class="input-group input-group-merge @if($errors->has('password')) is-invalid @endif">
                                            <input class="form-control @if($errors->has('password')) is-invalid @endif" name="password" type="password" required=""
                                                id="password" placeholder="Enter your password" />
                                                <div class="input-group-append" data-password="false">
                                                <div class="input-group-text">
                                                    <span class="password-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-sm-left">
                                        @if (\Session::has('Error'))
                                        <span class="text-danger" role="alert">
                                            <strong>{!! \Session::get('Error') !!}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    </div>
                                  {{-- <div class="form-group mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkbox-signin" checked>
                                            <label class="custom-control-label" for="checkbox-signin">Remember me</label>
                                        </div>
                                    </div>--}}
                                    <div class="form-group mb-0 text-center">
                                        <button class="btn btn-primary btn-block primary_bg_color" type="submit"> Log In </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <footer class="footer footer-alt">
            <script>document.write(new Date().getFullYear())</script> &copy; {{__('All rights reserved')}} by
        </footer>

        @include('layouts.shared.footer-script')
        <script src="{{asset('assets/js/app.min.js')}}"></script>
       
    </body>
</html>
