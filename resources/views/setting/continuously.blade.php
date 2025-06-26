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
            <h4 class="mb-0 font-size-18">{{__('main.continuous_deposit_setting')}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">{{__('main.continuous_deposit_setting')}}</li>
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
               <h4 class="card-title">{{__('main.continuous_deposit_setting')}}</h4>
               <form id="form-maintenance" action="{{ route('setting.deposit_continuously_update') }}" method="post">
                   @csrf
                   <br>
                   <div class="mb-2">
                       <label class="" for="continuously_receive">{{__('setting.Amount of credits given')}}</label>
                       <input type="text" class="form-control" name="continuously_receive" value="{{ $setting->continuously_receive }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="continuously_login">{{__('setting.Number of days of login')}}</label>
                       <input type="text" class="form-control" name="continuously_login" value="{{ $setting->continuously_login }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="continuously_min_deposit">{{__('setting.Minimum daily deposit amount')}}</label>
                       <input type="text" class="form-control" name="continuously_min_deposit" value="{{ $setting->continuously_min_deposit }}">
                   </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"@if($setting->is_enable_continuously == 1) checked @endif id="is_enable_continuously" name="is_enable_continuously">
                        <label class="custom-control-label" for="is_enable_continuously">{{__('setting.status')}}
                            @if($setting->is_enable_continuously == 1)
                            <span class="badge text-bg-success text-success">{{__('main.Use')}}</span>
                            @else
                            <span class="badge text-bg-danger text-danger">{{__('main.Not in use')}}</span>
                            @endif
                        </label>
                    </div>
                   <button type="button" onclick="formsubmit()" class="btn btn-primary waves-effect waves-light mt-4">{{__('main.save')}}</button>
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
            title: "{{__('setting.Do you want to save the data?')}}",
            // text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{__('main.save')}}",
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
