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
<!-- end row-->
 <div class="row">
    <div class="col-xl-6 d-flex">
        <div class="card flex-fill">
           <div class="card-body">
               <h4 class="card-title">{{__('setting.refer_friend_deposit')}}</h4>
               <form id="form-maintenance" action="{{ route('setting.affiliate_deposit_update') }}" method="post">
                   @csrf
                   <br>
                   <div class="mb-2">
                       <label class="" for="af_min_deposit">{{__('setting.min_deposit_required')}}</label>
                       <input type="text" class="form-control" name="af_min_deposit" value="{{ $affiliate->af_min_deposit }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="af_deposit_receive_lv_1">{{__('setting.level_1_gets')}}</label>
                       <input type="text" class="form-control" name="af_deposit_receive_lv_1" value="{{ $affiliate->af_deposit_receive_lv_1 }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="af_deposit_receive_lv_2">{{__('setting.level_2_gets')}}</label>
                       <input type="text" class="form-control" name="af_deposit_receive_lv_2" value="{{ $affiliate->af_deposit_receive_lv_2 }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="af_type">{{__('setting.type')}}</label>
                       <select type="text" class="form-control" name="af_type" required >
                           <option></option>
                           <option value="%" @if($affiliate->af_deposit_type == '%') @selected(true) @endif>%</option>
                           <option value="บาท" @if($affiliate->af_deposit_type == 'บาท') @selected(true) @endif>$</option>
                       </select>
                   </div>
                   <div class="mb-2">
                    <label class="" for="af_max_receive_percent">{{__('setting.max_credit_percent')}}</label>
                    <input type="text" class="form-control" name="af_max_receive_percent"  value="{{ $affiliate->af_max_receive_deposit_percent }}">
                    </div>
                   <div class="mb-2">
                    <label class="" for="af_max_receive_baht">{{__('setting.reduce_amount_from_friend_deposit')}}</label>
                    <input type="text" class="form-control" name="af_max_receive_baht"  value="{{ $affiliate->af_max_receive_deposit_baht }}">
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"@if($affiliate->is_enable_af_deposit == 1) checked @endif id="is_enable_af_deposit" name="is_enable_af_deposit">
                        <label class="custom-control-label" for="is_enable_af_deposit">{{__('setting.status')}}
                            @if($affiliate->is_enable_af_deposit == 1)
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
    <div class="col-xl-6 d-flex">
        <div class="card flex-fill">
           <div class="card-body">
               <h4 class="card-title">{{__('setting.refer_friend_bet_loss')}}</h4>
               <form id="form-maintenance2" action="{{ route('setting.affiliate_winlose_update') }}" method="post">
                   @csrf
                   <br>
                   <div class="mb-2">
                       <label class="" for="af_receive_percent_winlose_2">{{__('setting.percent_from_level_1_bet')}}</label>
                       <input type="text" class="form-control" name="af_receive_percent_winlose_2" value="{{ $affiliate->af_receive_percent_winlose_2 }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="af_receive_percent_winlose_3">{{__('setting.percent_from_level_2_bet')}}</label>
                       <input type="text" class="form-control" name="af_receive_percent_winlose_3" value="{{ $affiliate->af_receive_percent_winlose_3 }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="af_receive_percent_winlose_1">{{__('setting.percent_from_friend_loss')}}</label>
                        {{-- <input type="text" class="form-control" name="af_receive_percent_winlose_1" value="{{ $affiliate->af_receive_percent_winlose_1 }}"> --}}
                        <select type="text" class="form-control" name="af_receive_percent_winlose_1" required >
                        <option></option>
                        <option value="ยอดเสีย" @if($affiliate->af_receive_percent_winlose_1 == 'ยอดเสีย') @selected(true) @endif>{{__('setting.loss')}}</option>
                        <option value="ยอดเดิมพัน" @if($affiliate->af_receive_percent_winlose_1 == 'ยอดเดิมพัน') @selected(true) @endif>{{__('setting.Total Bet')}}</option>
                    </select>
                   </div>
                   <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"@if($affiliate->is_enable_af_winlose == 1) checked @endif id="is_enable_af_winlose" name="is_enable_af_winlose">
                        <label class="custom-control-label" for="is_enable_af_winlose">{{__('setting.status')}}
                           @if($affiliate->is_enable_af_winlose == 1)
                           <span class="badge text-bg-success text-success">{{__('main.Use')}}</span>
                           @else
                           <span class="badge text-bg-danger text-danger">{{__('main.Not in use')}}</span>
                            @endif
                        </label>
                    </div>
                   <button type="button" onclick="formsubmit2()" class="btn btn-primary waves-effect waves-light mt-4">{{__('main.save')}}</button>
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
            title: '{{__('setting.Do you want to save the data?')}}',
            // text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: '{{__('main.save')}}',
            cancelButtonText: "{{__('main.cancel')}}",
            }).then((result) => {
                if (result.value) {
                    $('#form-maintenance').submit();
                }
            });
        }
        function formsubmit2() {
            Swal.fire({
            title: '{{__('setting.Do you want to save the data?')}}',
            // text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{__('main.save')}}",
            cancelButtonText: "{{__('main.cancel')}}",
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
