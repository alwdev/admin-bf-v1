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
            <h4 class="mb-0 font-size-18">{{__('main.setting')}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">{{__('main.setting')}}</li>
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
                <h4 class="card-title">Logo เว็บไซต์</h4>
                <form id="form-logo" action="{{ route('setting.logo_update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        @if($setting->logo)
                            <div class="mb-2">
                                <img src="{{ asset('images/' . $setting->logo) }}" height="60" style="max-width:200px;height:auto;" />
                            </div>
                        @endif
                        <label class="form-label" for="logo">อัปโหลด Logo ใหม่</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <small class="text-muted">รองรับ JPG, PNG, GIF, WEBP, SVG (สูงสุด 2MB)</small>
                    </div>
                    @if($setting->logo)
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="remove_logo" name="remove_logo" value="1">
                            <label class="custom-control-label" for="remove_logo">ลบ Logo ออก</label>
                        </div>
                    @endif
                    <button type="button" onclick="formsubmit('#form-logo')" class="btn btn-primary waves-effect waves-light">{{__('main.save')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
         <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{__('setting.website_setting')}}</h4>
                <form id="form-maintenance" action="{{ route('setting.maintenance') }}" method="post">
                    @csrf
                    <br>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" @if($setting->maintenance == 1) checked @endif id="ismaintence" name="ismaintence">
                        <label class="custom-control-label" for="ismaintence">Maintenance</label>
                    </div>
                    <button type="button" onclick="formsubmit('#form-maintenance')" class="btn btn-primary waves-effect waves-light mt-4">{{__('main.save')}}</button>
                </form>
            </div>
            <!-- end card-body-->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
    <!-- end col -->
    <div class="row">
        <div class="col-xl-4 d-flex">
            <div class="card flex-fill">
               <div class="card-body">
                   <h4 class="card-title">{{__('setting.deposit_setting')}}</h4>
                   <form id="form-maintenance2" action="{{ route('setting.deposit') }}" method="post">
                       @csrf
                       <br>
                       <div class="mb-2">
                           <label class="" for="min_deposit">{{__('setting.Minimum deposit')}}</label>
                           <input type="text" class="form-control" name="min_deposit" value="{{ $setting->min_deposit }}">
                       </div>
                       <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"@if($setting->is_enable_min_deposit == 1) checked @endif id="is_enable_min_deposit" name="is_enable_min_deposit">
                            <label class="custom-control-label" for="is_enable_min_deposit">{{__('dashboard.status')}}
                               @if($setting->is_enable_min_deposit == 1)
                               <span class="badge text-bg-success text-success">{{__('main.Use')}}</span>
                               @else
                               <span class="badge text-bg-danger text-danger">{{__('main.Not in use')}}</span>
                                @endif
                            </label>
                        </div>
                       <button type="button" onclick="formsubmit('#form-maintenance2')" class="btn btn-primary waves-effect waves-light mt-4">{{__('main.save')}}</button>
                   </form>
               </div>
               <!-- end card-body-->
           </div>
           <!-- end card -->
       </div>
        <div class="col-xl-4 d-flex">
            <div class="card flex-fill">
               <div class="card-body">
                   <h4 class="card-title">{{__('setting.withdraw_setting')}}</h4>
                   <form id="form-maintenance3" action="{{ route('setting.withdraw') }}" method="post">
                       @csrf
                       <br>
                       <div class="mb-2">
                           <label class="" for="min_withdraw">{{__('setting.Minimum withdrawal')}}</label>
                           <input type="text" class="form-control" name="min_withdraw" value="{{ $setting->min_withdraw }}">
                       </div>
                       <div class="mb-2">
                           <label class="" for="auto_min_withdraw">{{__('setting.Auto-withdraw when the amount is less than or equal to')}}</label>
                           <input type="text" class="form-control" name="auto_min_withdraw" value="{{ $setting->auto_min_withdraw }}">
                       </div>
                       <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" @if($setting->is_enable_auto_withdraw == 1) checked @endif id="is_enable_auto_withdraw" name="is_enable_auto_withdraw">
                            <label class="custom-control-label" for="is_enable_auto_withdraw">{{__('setting.Auto Withdrawal')}}
                               @if($setting->is_enable_auto_withdraw == 1)
                               <span class="badge text-bg-success text-success">{{__('main.Use')}}</span>
                               @else
                               <span class="badge text-bg-danger text-danger">{{__('main.Not in use')}}</span>
                                @endif
                            </label>
                        </div>
                       <button type="button" onclick="formsubmit('#form-maintenance3')" class="btn btn-primary waves-effect waves-light mt-4">{{__('main.save')}}</button>
                   </form>
               </div>
               <!-- end card-body-->
           </div>
           <!-- end card -->
       </div>
        <div class="col-xl-4 d-flex">
            <div class="card flex-fill">
               <div class="card-body">
                   <h4 class="card-title">{{__('setting.commission_setting')}}</h4>
                   <form id="form-maintenance4" action="{{ route('setting.cashback') }}" method="post">
                       @csrf
                       <br>
                       <div class="mb-2">
                           <label class="" for="cashback_percent">{{__('setting.Return(percent)')}}</label>
                           <input type="text" class="form-control" name="cashback_percent" value="{{ $setting->cashback_percent }}">
                       </div>
                       <div class="mb-2">
                           <label class="" for="cashback_turnover">{{__('setting.Turns to be made(times)')}}</label>
                           <input type="text" class="form-control" name="cashback_turnover" value="{{ $setting->cashback_turnover }}">
                       </div>
                       <div class="mb-2">
                           <label class="" for="cashback_min_withdraw">{{__('setting.Minimum withdrawal amount (units)')}}</label>
                           <input type="text" class="form-control" name="cashback_min_withdraw" value="{{ $setting->cashback_min_withdraw }}">
                       </div>
                       <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" @if($setting->is_enable_cashback == 1) checked @endif id="is_enable_cashback" name="is_enable_cashback">
                            <label class="custom-control-label" for="is_enable_cashback">{{__('dashboard.status')}}
                               @if($setting->is_enable_cashback == 1)
                               <span class="badge text-bg-success text-success">{{__('main.Use')}}</span>
                               @else
                               <span class="badge text-bg-danger text-danger">{{__('main.Not in use')}}</span>
                                @endif
                            </label>
                        </div>
                       <button type="button" onclick="formsubmit('#form-maintenance4')" class="btn btn-primary waves-effect waves-light mt-4">{{__('main.save')}}</button>
                   </form>
               </div>
               <!-- end card-body-->
           </div>
           <!-- end card -->
       </div>
        <!-- end col -->
    </div>
<!-- end row-->
@endsection
@section('scripts')
    <script>
         function formsubmit(form) {
            Swal.fire({
            title: '{{__('setting.Do you want to save the data?')}}',
            // text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: '{{__('main.save')}}',
            cancelButtonText: '{{__('main.cancel')}}',
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
