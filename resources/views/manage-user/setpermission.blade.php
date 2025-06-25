@extends('layouts.guest')
@section('styles')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<style>
    .text-red-600{
        color:red;
        font-weight: 600;
    }
</style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">{{__('main.Manage employees')}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">{{__('main.Set permissions')}}</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">{{__('main.details')}}</h4>
                {{-- <p class="card-subtitle mb-4">Create horizontal forms with the grid by adding the <code>.row</code> class to form groups and using the <code>.col-*-*</code> classes to specify the width of your labels and controls. Be sure to add <code>.col-form-label</code> to your <code>&lt;label&gt;</code>s as well so they’re vertically centered with their associated form controls.</p> --}}
                <form method="post" action="{{ route('manageuser.updateuser') }}" class="mt-6 space-y-6">
                    @csrf
                    <input type="hidden" name="uid" value="{{$user->safe_id}}">
                    <div class="mb-2">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full form-control" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="mb-2">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full  form-control" :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div>
                                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                    {{ __('Your email address is unverified.') }}

                                    <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                        {{ __('Click here to re-send the verification email.') }}
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                     <div class="mb-3">
                        <label for="inputEmail3" class="col-form-label">ตำแหน่ง</label>

                    <select class="form-control" id="level" name="level">
                        @if(Auth::user()->level == 0)
                            <option value="0">Super Admin</option>
                            <option value="1" @if($user->level == 1) @selected(true) @endif>Admin</option>
                            <option value="2" @if($user->level == 2) @selected(true) @endif>Staff</option>
                        @elseif(Auth::user()->level == 1)
                            <option value="2">Staff</option>
                        @endif
                    </select>

                    </div>

                    <div class="form-group mb-0 mr-0 justify-content-end row">
                        <button class="btn btn-primary waves-effect waves-light ">{{ __('Save') }}</button>
                    </div>
                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
        <!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <form class="form-horizontal" method="post" action="{{ route('manageuser.setPermission') }}">
            @csrf
            <input type="hidden" name="userid" value="{{ $user->safe_id }}" />
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{__('main.Set permissions')}}</h4>
                    <p class="card-subtitle mb-4"></p>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('main.Page')}}</th>
                                    <th>{{__('main.hide')}}</th>
                                    <th>{{__('main.show')}}</th>
                                    <th>{{__('main.edit')}}</th>
                                    <th>{{__('managemember.delete')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>{{__('main.member_manage')}}</td>
                                    <td>
                                        <input type="radio" name="member" value="1" @if($user->permissions->member == 1) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="member" value="2" @if($user->permissions->member == 2) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="member" value="3" @if($user->permissions->member == 3) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="member" value="4" @if($user->permissions->member == 4) @checked(true) @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>{{__('main.Manage employees')}}</td>
                                    <td>
                                        <input type="radio" name="manageuser" value="1" @if($user->permissions->manageuser == 1) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="manageuser" value="2" @if($user->permissions->manageuser == 2) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="manageuser" value="3" @if($user->permissions->manageuser == 3) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="manageuser" value="4" @if($user->permissions->manageuser == 4) @checked(true) @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>{{__('main.transfer_list')}}</td>
                                    <td>
                                        <input type="radio" name="transfer" value="1" @if($user->permissions->transfer == 1) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="transfer" value="2" @if($user->permissions->transfer == 2) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="transfer" value="3" @if($user->permissions->transfer == 3) @checked(true) @endif>
                                    </td>
                                    <td>
                                        {{-- <input type="radio" name="transfer" value="4" @if($user->permissions->transfer == 4) @checked(true) @endif> --}}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">4</th>
                                    <td>{{__('main.report')}}</td>
                                    <td>
                                        <input type="radio" name="report" value="1" @if($user->permissions->report == 1) @checked(true) @endif>
                                    </td>
                                    <td>
                                        <input type="radio" name="report" value="2" @if($user->permissions->report == 2) @checked(true) @endif>
                                    </td>
                                    <td>
                                        {{-- <input type="radio" name="report" value="3" @if($user->permissions->report == 3) @checked(true) @endif> --}}
                                    </td>
                                    <td>
                                        {{-- <input type="radio" name="report" value="4" @if($user->permissions->report == 4) @checked(true) @endif> --}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group mb-0 mr-0 justify-content-end row">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">{{__('main.save')}}</button>
                    </div>
                </div>
                <!-- end card-body-->
            </div>
        </form>
    </div>
    <!-- end col -->
</div>
<!-- end row-->
@endsection
@section('scripts')
    <!-- third party js -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/buttons.flash.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/buttons.print.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.keyTable.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.select.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/pdfmake.min.js')}}"></script>
    <script src="{{ asset('plugins/datatables/vfs_fonts.js')}}"></script>
    <!-- third party js ends -->

    <!-- Datatables init -->
    <script src="{{ asset('pages/datatables-demo.js')}}"></script>
    <script>
        @if (session('status'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

        @endif

        @if (session('status') === 'profile-updated')
            Swal.fire({
                position: 'top-end',
                type: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            })
        @endif
    </script>
@endsection
