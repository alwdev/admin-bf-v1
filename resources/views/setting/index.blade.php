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
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">ตั้งค่า</li>
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
                <h4 class="card-title">ตั้งค่าเว็บไซต์</h4>
                <form id="form-maintenance" action="{{ route('setting.maintenance') }}" method="post">
                    @csrf
                    <br>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" @if($setting->maintenance == 1) checked @endif id="ismaintence" name="ismaintence">
                        <label class="custom-control-label" for="ismaintence">Maintenance</label>
                    </div>
                    <button type="button" onclick="formsubmit()" class="btn btn-primary waves-effect waves-light mt-4">บันทึก</button>
                </form>
            </div>
            <!-- end card-body-->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">


    </div>
    <!-- end col -->
</div>
<!-- end row-->
@endsection
@section('scripts')
    <script>
        function formsubmit() {
            Swal.fire({
            title: "ต้องการบันทึกข้อมูลหรือไม่?",
            // text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "บันทึก",
            cancelButtonText: "ยกเลิก",
            }).then((result) => {
                if (result.value) {
                    $('#form-maintenance').submit();
                }
            });
        }
        @if (session('status'))

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