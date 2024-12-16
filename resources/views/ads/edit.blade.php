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
            <h4 class="mb-0 font-size-18">แก้ไข Ads</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">เพิ่ม Ads - {{ $ads->title }}</li>
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
                <form class="p-2" action="{{ route('promotion_ads.update',$ads->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $title =  (!empty(old('title')))? old('title'):$ads->title;
                        $description =  (!empty(old('description')))? old('description'):$ads->description;
                    @endphp
                    <div class="form-group">
                        <label for="title">โปรโมชั่น</label>
                        <input class="form-control" type="text" id="title" name="title" required value="{{ $title }}">
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="image">รูปภาพ</label><br>
                        @if ($ads->image)

                            <img src="{{ $ads->image }}" class="img-thumbnail rounded" style="height:200px;cursor: pointer;"  onclick="showImage('{{ $ads->image }}')">
                        @endif
                        <input class="form-control" type="file" id="image" name="image" value="{{ old('image') }}"  accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps">
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="description">รายละเอียด</label>
                        <textarea class="form-control" id="description" rows="15" name="description">{{ $description }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div class="custom-control custom-checkbox custom-control-inline mb-3">
                        <input type="checkbox" class="custom-control-input" id="enable" name="enable" @if($ads->enable) checked @endif value="1">
                        <label class="custom-control-label" for="enable">Enable (เผยแพร่)</label>
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

        function showImage(image){
            Swal.fire({
                imageUrl: image,
                imageHeight: 500,
                imageAlt: "A tall image"
            });
        }
    </script>
@endsection
