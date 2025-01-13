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
            <h4 class="mb-0 font-size-18">เพิ่มสมุดบัญชี</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">เพิ่มสมุดบัญชี</li>
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
                <form class="p-2" action="{{ route('bankaccount.store') }}" method="POST"  enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <div id="bank-logo" class="mb-3"></div>
                        <input type="hidden" value="" name="bank_logo" id="bank_logo">
                        <select name="bank_name" id="bank_name" required class="form-control">
                            <option value="">เลือกธนาคาร</option>
                            <option value="ธนาคารกรุงเทพ" data-img="bbl.png" data-logo="{{ asset('images/bank/bbl.png') }}"> ธนาคารกรุงเทพ</option>
                            <option value="ธนาคารกสิกรไทย" data-img="kbank.png" data-logo="{{ asset('images/bank/kbank.png') }}"> ธนาคารกสิกรไทย</option>
                            <option value="ธนาคารกรุงไทย" data-img="ktb.png" data-logo="{{ asset('images/bank/ktb.png') }}"> ธนาคารกรุงไทย</option>
                            <option value="ธนาคารทหารไทยธนชาต" data-img="ttb.png" data-logo="{{ asset('images/bank/ttb.png') }}"> ธนาคารทหารไทยธนชาต</option>
                            <option value="ธนาคารไทยพาณิชย์" data-img="scb.png" data-logo="{{ asset('images/bank/scb.png') }}"> ธนาคารไทยพาณิชย์</option>
                            <option value="ธนาคารกรุงศรีอยุธยา" data-img="bay.png" data-logo="{{ asset('images/bank/bay.png') }}"> ธนาคารกรุงศรีอยุธยา</option>
                            <option value="ธนาคารเกียรตินาคินภัทร" data-img="kk.png" data-logo="{{ asset('images/bank/kk.png') }}"> ธนาคารเกียรตินาคินภัทร</option>
                            <option value="ธนาคารซีไอเอ็มบีไทย" data-img="cimb.png" data-logo="{{ asset('images/bank/cimb.png') }}"> ธนาคารซีไอเอ็มบีไทย</option>
                            <option value="ธนาคารทิสโก้" data-img="tisco.png" data-logo="{{ asset('images/bank/tisco.png') }}"> ธนาคารทิสโก้</option>
                            <option value="ธนาคารยูโอบี" data-img="uob.png" data-logo="{{ asset('images/bank/uob.png') }}"> ธนาคารยูโอบี</option>
                            <option value="ธนาคารไทยเครดิต" data-img="tcrb.png" data-logo="{{ asset('images/bank/tcrb.png') }}"> ธนาคารไทยเครดิต</option>
                            <option value="ธนาคารออมสิน" data-img="gsb.png" data-logo="{{ asset('images/bank/gsb.png') }}"> ธนาคารออมสิน</option>
                            <option value="ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร" data-img="baac.png" data-logo="{{ asset('images/bank/baac.png') }}"> ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร</option>
                            <option value="TrueMoney Wallet" data-img="truemoney.png" data-logo="{{ asset('images/bank/truemoney.png') }}"> TrueMoney Wallet</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="account_name">ชื่อบัญชี</label>
                        <input class="form-control" type="text" id="account_name" name="account_name" required value="{{ old('account_name') }}">
                        <x-input-error :messages="$errors->get('account_name')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="account_no">เลขที่บัญชี</label>
                        <input class="form-control"  type="number" id="account_no" name="account_no" required onkeypress="return isNumberKey(event)"  value="{{ old('account_no') }}">
                        <x-input-error :messages="$errors->get('account_no')" class="mt-2" />
                    </div>
                    {{-- <div class="form-group">
                        <label for="balance">ยอดคงเหลือในบัญชี</label>
                        <input class="form-control"  type="text" id="balance" name="balance" required onkeypress="return isNumberKey(event)"  value="{{ old('balance') }}">
                        <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                    </div> --}}
                    <div class="custom-control custom-checkbox custom-control-inline mb-3">
                        <input type="checkbox" class="custom-control-input" id="enable" name="enable" checked value="1">
                        <label class="custom-control-label" for="enable">Enable (เผยแพร่)</label>
                    </div>
                    <div class="form-group">
                        <label for="qr_code">QR CODE</label>
                        <input class="form-control" type="file" id="qr_code" name="qr_code"   accept="image/*"  value="{{ old('qr_code') }}">
                        <x-input-error :messages="$errors->get('qr_code')" class="mt-2" />
                        <div class="mt-3">
                            <img src="" id="img_qrcode" class="image-responsive">
                        </div>
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


            $('#qr_code').change(function(e){
                e.preventDefault();
                const fsize = (this.files[0].size)/1024;
                const fname = (this.files[0].name).split('.').pop();

                if( fname!="png" && fname!="jpg" && fname!="jpeg"){
                    Swal.fire({
                                position: 'top-end',
                                type: 'error',
                                title: 'Wrong File!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        return true;
                }
                else if(fsize>2048){
                    Swal.fire({
                                position: 'top-end',
                                type: 'error',
                                title: 'Image are too large!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        return true;
                }
                else{   
                    $('#img_qrcode').attr('src',URL.createObjectURL(this.files[0]));
                }
            });

        });

        function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
        }
    </script>
@endsection
