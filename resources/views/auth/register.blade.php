<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} - Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="MyraStudio" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico')}}">

    <!-- App css -->
    <link href="{{ asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/theme.min.css')}}" rel="stylesheet" type="text/css" />

</head>
<body style="background-image: url('{{ asset('images/1920x949.jpg') }}')">

    <div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center min-vh-100">
                        <div class="w-100 d-block my-5" style="opacity: .95">
                            <div class="row justify-content-center">
                                <div class="col-md-8 col-lg-5">
                                     <div class="card">
                                        <div class="card-body">
                                            <div class="text-center mb-4 mt-3">
                                                <a href="/">
                                                    <span><img src="{{ asset('logo3.png') }}" alt="" height="100"></span>
                                                </a>
                                            </div>
                                            <form class="p-2" action="{{ route('register') }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input class="form-control" type="text" id="name" name="name" required="" value="{{ old('name') }}" placeholder="Michael Zenaty">
                                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="emailaddress">Email address</label>
                                                    <input class="form-control" type="email" id="emailaddress" name="email" required="" value="{{ old('email') }}" placeholder="john@deo.com">
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input class="form-control" type="password" required="" password="password" id="password" name="password" autocomplete="new-password" placeholder="Enter your password">
                                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password">Password</label>
                                                        <input class="form-control" type="password" required="" password="password" name="password_confirmation" id="password_confirmation" placeholder="Enter your password">
                                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                                </div>
                                                <div class="form-group mb-4 pb-3">
                                                    <div class="custom-control custom-checkbox checkbox-primary">
                                                        <input type="checkbox" class="custom-control-input" id="checkbox-signin">
                                                        <label class="custom-control-label" for="checkbox-signin">I accept <a href="#">Terms and Conditions</a></label>
                                                    </div>
                                                </div>
                                                <div class="mb-3 text-center">
                                                    <button class="btn btn-primary btn-block" type="submit"> Sign Up Free </button>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- end card-body -->
                                    </div>
                                    <!-- end card -->
            
                                    <div class="row mt-4">
                                        <div class="col-sm-12 text-center">
                                            <p class="text-white-50 mb-0">Already have an account? <a href="pages-login.html" class="text-white-50 ml-1"><b>Sign In</b></a></p>
                                        </div>
                                    </div>
            
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div> <!-- end .w-100 -->
                    </div> <!-- end .d-flex -->
                </div> <!-- end col-->
            </div> <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <!-- jQuery  -->
    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('js/metismenu.min.js')}}"></script>
    <script src="{{ asset('js/waves.js')}}"></script>
    <script src="{{ asset('js/simplebar.min.js')}}"></script>

    <!-- App js -->
    <script src="{{ asset('js/theme.js')}}"></script>

</body>

</html>