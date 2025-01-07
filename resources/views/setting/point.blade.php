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
            <h4 class="mb-0 font-size-18">ตั้งค่าการคำนวณแต้ม</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">ตั้งค่าการคำนวณแต้ม</li>
                </ol>
            </div>
            
        </div>
    </div>
</div>     
<!-- end page title -->
<!-- end row-->
 <div class="row">
    <div class="col-xl-6 d-flex">
        <div class="card flex-fill">
           <div class="card-body">
               <h4 class="card-title">ตั้งค่าการคำนวณแต้ม</h4>
               <form id="form-maintenance" action="{{ route('setting.point_update') }}" method="post">
                   @csrf
                   <br>
                   <div class="mb-2">
                       <label class="" for="point">จำนวนแต้ม</label>
                       <input type="text" class="form-control" name="point" value="{{ $setting->point }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="turnover_point">ยอด Turn ต่อจำนวนแต้ม</label>
                       <input type="text" class="form-control" name="turnover_point" value="{{ $setting->turnover_point }}">
                   </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"@if($setting->is_enable_point == 1) checked @endif id="is_enable_point" name="is_enable_point">
                        <label class="custom-control-label" for="is_enable_point">สถานะ 
                            @if($setting->is_enable_point == 1) 
                            <span class="badge text-bg-success text-success">ใช้งาน</span>
                            @else
                            <span class="badge text-bg-danger text-danger">ปิดใช้งาน</span>
                            @endif
                        </label>
                    </div>
                   <button type="button" onclick="formsubmit()" class="btn btn-primary waves-effect waves-light mt-4">บันทึก</button>
               </form>
           </div>
           <!-- end card-body-->
       </div>
       <!-- end card -->
   </div>
 </div>
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
        function formsubmit2() {
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
                    $('#form-maintenance2').submit();
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