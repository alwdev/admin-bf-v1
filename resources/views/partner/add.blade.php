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
                <form class="p-2" action="{{ route('partner.create') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="slug_name">รหัส</label>
                        <input class="form-control" type="text" id="slug_name" name="slug_name" value="{{ $code }}" required value="{{ old('slug_name') }}" placeholder="รหัส">
                        <x-input-error :messages="$errors->get('slug_name')" class="mt-2" />
                    </div>
                    {{-- <div class="form-group">
                        <label for="url">URL Link</label>
                        <input class="form-control" type="text" id="url" name="url" required value="{{ old('url') }}" placeholder="URL Link">
                        <x-input-error :messages="$errors->get('url')" class="mt-2" />
                    </div> --}}
                    <div class="form-group">
                        <label for="rate">ส่วนแบ่ง %</label>
                        <input class="form-control" type="text" id="rate" name="rate" onkeypress="return isNumberKey(event)" required value="{{ old('rate') }}" placeholder="ส่วนแบ่ง %">
                        <x-input-error :messages="$errors->get('rate')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="contact_name">ชื่อพันธมิตร</label>
                        <input class="form-control" type="text" id="contact_name" name="contact_name" required value="{{ old('contact_name') }}" placeholder="ชื่อพันธมิตร">
                        <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="contact_phonenumber">เบอร์โทรศัพท์</label>
                        <input class="form-control" type="text" id="contact_phonenumber" name="contact_phonenumber" value="{{ old('contact_phonenumber') }}" placeholder="เบอร์โทรศัพท์">
                        <x-input-error :messages="$errors->get('contact_phonenumber')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="contact_email">อีเมล</label>
                        <input class="form-control" type="text" id="contact_email" name="contact_email"  value="{{ old('contact_email') }}" placeholder="อีเมล">
                        <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
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