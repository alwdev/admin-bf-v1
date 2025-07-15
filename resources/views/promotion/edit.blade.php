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
            <h4 class="mb-0 font-size-18">{{__('main.Edit Promotion')}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">{{__('main.Edit Promotion')}}</li>
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
                </div>
                <form class="p-2" action="{{ route('promotion.update',$promotion->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">{{__('main.promotion')}}</label>
                        <input class="form-control" type="text" id="name" name="name" required value="{{ $promotion->name }}">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="deposit">{{__('dashboard.deposit')}}</label>
                        <input class="form-control"  type="number" id="deposit" name="deposit" required onkeypress="return isNumberKey(event)"   value="{{ $promotion->deposit }}">
                        <x-input-error :messages="$errors->get('deposit')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="bonus">Bonus</label>
                        <input class="form-control"  type="number" id="bonus" name="bonus" min="0" required onkeypress="return isNumberKey(event)"  value="{{ $promotion->bonus }}">
                        <x-input-error :messages="$errors->get('bonus')" class="mt-2" />
                    </div>
                    <div class="custom-control custom-radio custom-control-inline mb-3">
                        <input type="radio" class="custom-control-input" id="bonus_type_amount" name="bonus_type" value="amount"
                            {{ $promotion->bonus_type == 'amount' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="bonus_type_amount">Amount</label>
                    </div>

                    <div class="custom-control custom-radio custom-control-inline mb-3">
                        <input type="radio" class="custom-control-input" id="bonus_type_percent" name="bonus_type" value="percent"
                            {{ $promotion->bonus_type == 'percent' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="bonus_type_percent">Percent</label>
                    </div>

                    <div class="form-group">
                        <label for="turnover">{{__('main.Turnover')}}</label>
                        <input class="form-control float-number"  type="text" id="turnover" name="turnover" min="0" max="100" required value="{{ $promotion->turnover }}">
                        <x-input-error :messages="$errors->get('turnover')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="withdraw_limit">{{__('main.Maximum withdrawal')}}</label>
                        <input class="form-control"  type="number" id="withdraw_limit" name="withdraw_limit" min="0" required onkeypress="return isNumberKey(event)"  value="{{ $promotion->withdraw_limit }}">
                        <x-input-error :messages="$errors->get('withdraw_limit')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="image">{{__('main.Image (size 400x400px)')}}</label><br>
                        @if ($promotion->image)

                            <img src="{{ $promotion->image }}" class="img-thumbnail rounded" style="height:200px;cursor: pointer;"  onclick="showImage('{{ $promotion->image }}')">
                        @endif
                        <input class="form-control" type="file" id="image" name="image" value="{{ old('image') }}"  accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps">
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="description">{{__('main.details')}}</label>
                        <textarea class="form-control" id="description" rows="15" name="description">{{ $promotion->description }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div class="custom-control custom-checkbox custom-control-inline mb-3">
                        <input type="checkbox" class="custom-control-input" id="is_newuser" name="is_newuser" @if($promotion->is_newuser==1) checked @endif value="1">
                        <label class="custom-control-label" for="is_newuser">{{__('main.For new players only')}}</label>
                    </div>
                    <div class="custom-control custom-checkbox custom-control-inline mb-3">
                        <input type="checkbox" class="custom-control-input" id="enable" name="enable" @if($promotion->enable==1) checked @endif  value="1">
                        <label class="custom-control-label" for="enable">Enable ({{__('main.Publish')}})</label>
                    </div>
                    <div class="mb-3 text-center">
                        <button class="btn btn-primary btn-block" type="submit"> {{__('main.save')}} </button>
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
    <script>
        $(document).ready(function() {
            $('#bank_name').change(function() {
                if($('option:selected').val() == ''){
                    $('#bank-logo').html('');
                    $('#bank_logo').val('');
                }else{
                    $('#bank-logo').html('<img src="'+$('option:selected').attr('data-logo')+'" alt="" width="54"/>');
                    $('#bank_logo').val($('option:selected').attr('data-img'));
                }
            });

        });

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
        jQuery(document).ready(function() {
            $('.float-number').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
        });

        function showImage(image){
            Swal.fire({
                imageUrl: image,
                imageHeight: 500,
                imageAlt: "A tall image"
            });
        }
    </script>
@endsection
