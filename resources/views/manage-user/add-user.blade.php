@extends('layouts.guest')
@section('styles')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">ตั้งค่า</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">ตั้งค่า</li>
                </ol>
            </div>
            
        </div>
    </div>
</div>     
<!-- end page title -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-4 mt-3">
                    <a href="index.html">
                        <span><img src="assets/images/logo-dark.png" alt="" height="26"></span>
                    </a>
                </div>
                <form class="p-2" action="{{ route('manageuser.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">ชื่อ</label>
                        <input class="form-control" type="text" id="name" name="name" required="" value="{{ old('name') }}" placeholder="Michael Zenaty">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="emailaddress">ชื่อผู้ใช้ (Email)</label>
                        <input class="form-control" type="email" id="emailaddress" name="email" required="" value="{{ old('email') }}" placeholder="john@deo.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="password">รหัสผ่าน</label>
                        <input class="form-control" type="text" required="" value="{{ $password }}" password="password" id="password" name="password" autocomplete="new-password" placeholder="Enter your password">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    
                    </div>
                    <div class="form-group">
                        <label for="emailaddress">ตำแหน่ง</label>
                        <select class="form-control" id="level" name="level">
                            @if(Auth::user()->level == 0)
                                <option value="0">Super Admin</option>
                                <option value="1">Admin</option>
                                <option value="2">Staff</option>
                            @elseif(Auth::user()->level == 1)
                                <option value="2">Staff</option>
                            @endif
                        </select>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="mb-3 text-center">
                        <button class="btn btn-primary btn-block" type="submit"> บันทึก </button>
                    </div>
                </form>
            </div>
            <!-- end card-body -->
        </div>
    </div> <!-- end col-->
</div> <!-- end row -->
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
@endsection