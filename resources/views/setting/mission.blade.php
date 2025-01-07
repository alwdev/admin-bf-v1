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
            <h4 class="mb-0 font-size-18">ตั้งค่าเควสประจำวัน</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">ตั้งค่าเควสประจำวัน</li>
                </ol>
            </div>
            
        </div>
    </div>
</div>     
<!-- end page title -->
<!-- end row-->
 <div class="row">
    <div class="col-xl-4 d-flex">
        <div class="card flex-fill">
           <div class="card-body">
               <h4 class="card-title">ตั้งค่าเควสยอดการฝาก</h4>
               <form id="form-maintenance" action="{{ route('setting.mission_deposit_update') }}" method="post">
                   @csrf
                   <br>
                   <div class="mb-2">
                       <label class="" for="mission_deposit_goal">เป้าหมาย</label>
                       <input type="text" class="form-control" name="mission_deposit_goal" value="{{ $setting->mission_deposit_goal }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="mission_deposit_point">แต้มที่ได้</label>
                       <input type="text" class="form-control" name="mission_deposit_point" value="{{ $setting->mission_deposit_point }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="mission_deposit_credit">เครดิตที่ได้</label>
                       <input type="text" class="form-control" name="mission_deposit_credit" value="{{ $setting->mission_deposit_credit }}">
                   </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"@if($setting->is_enable_mission_deposit == 1) checked @endif id="is_enable_mission_deposit" name="is_enable_mission_deposit">
                        <label class="custom-control-label" for="is_enable_mission_deposit">สถานะ 
                            @if($setting->is_enable_mission_deposit == 1) 
                            <span class="badge text-bg-success text-success">ใช้งาน</span>
                            @else
                            <span class="badge text-bg-danger text-danger">ปิดใช้งาน</span>
                            @endif
                        </label>
                    </div>
                   <button type="button" onclick="formsubmit('#form-maintenance')" class="btn btn-primary waves-effect waves-light mt-4">บันทึก</button>
               </form>
           </div>
           <!-- end card-body-->
       </div>
       <!-- end card -->
   </div>
    <div class="col-xl-4 d-flex">
        <div class="card flex-fill">
           <div class="card-body">
               <h4 class="card-title">ตั้งค่าเควสยอดการเล่น</h4>
               <form id="form-maintenance2" action="{{ route('setting.mission_play_update') }}" method="post">
                   @csrf
                   <br>
                   <div class="mb-2">
                       <label class="" for="mission_play_goal">เป้าหมาย</label>
                       <input type="text" class="form-control" name="mission_play_goal" value="{{ $setting->mission_play_goal }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="mission_play_point">แต้มที่ได้</label>
                       <input type="text" class="form-control" name="mission_play_point" value="{{ $setting->mission_play_point }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="mission_play_credit">เครดิตที่ได้</label>
                       <input type="text" class="form-control" name="mission_play_credit" value="{{ $setting->mission_play_credit }}">
                   </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"@if($setting->is_enable_mission_play == 1) checked @endif id="is_enable_mission_play" name="is_enable_mission_play">
                        <label class="custom-control-label" for="is_enable_mission_play">สถานะ 
                            @if($setting->is_enable_mission_play == 1) 
                            <span class="badge text-bg-success text-success">ใช้งาน</span>
                            @else
                            <span class="badge text-bg-danger text-danger">ปิดใช้งาน</span>
                            @endif
                        </label>
                    </div>
                   <button type="button" onclick="formsubmit('#form-maintenance2')" class="btn btn-primary waves-effect waves-light mt-4">บันทึก</button>
               </form>
           </div>
           <!-- end card-body-->
       </div>
       <!-- end card -->
   </div>
    <div class="col-xl-4 d-flex">
        <div class="card flex-fill">
           <div class="card-body">
               <h4 class="card-title">ตั้งค่าเควสยอดชนะ</h4>
               <form id="form-maintenance3" action="{{ route('setting.mission_win_update') }}" method="post">
                   @csrf
                   <br>
                   <div class="mb-2">
                       <label class="" for="mission_win_goal">เป้าหมาย</label>
                       <input type="text" class="form-control" name="mission_win_goal" value="{{ $setting->mission_win_goal }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="mission_win_point">แต้มที่ได้</label>
                       <input type="text" class="form-control" name="mission_win_point" value="{{ $setting->mission_win_point }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="mission_win_credit">เครดิตที่ได้</label>
                       <input type="text" class="form-control" name="mission_win_credit" value="{{ $setting->mission_win_credit }}">
                   </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"@if($setting->is_enable_mission_win == 1) checked @endif id="is_enable_mission_win" name="is_enable_mission_win">
                        <label class="custom-control-label" for="is_enable_mission_win">สถานะ 
                            @if($setting->is_enable_mission_win == 1) 
                            <span class="badge text-bg-success text-success">ใช้งาน</span>
                            @else
                            <span class="badge text-bg-danger text-danger">ปิดใช้งาน</span>
                            @endif
                        </label>
                    </div>
                   <button type="button" onclick="formsubmit('#form-maintenance3')" class="btn btn-primary waves-effect waves-light mt-4">บันทึก</button>
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
        function formsubmit(form) {
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
                    $(form).submit();
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