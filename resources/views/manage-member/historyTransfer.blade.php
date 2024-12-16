@extends('layouts.guest')
@section('styles')
<style>
    .bank-logo{
        background: rgb(88, 88, 88);
    }
</style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">ประวัติการเงิน</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item">จัดการพนักงาน</li>
                    <li class="breadcrumb-item active">ประวัติการเงิน</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
         <div class="card">
            {{-- <div class="card-header text-right"> --}}
                {{-- <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="{{ route('manageuser.addnewuser') }}">เพิ่มพนักงาน</a> --}}
            {{-- </div> --}}
            <div class="card-body">
                <h4 class="card-title"></h4>
                <p class="card-subtitle mb-4">
                </p>

                <table id="basic-datatable" class="table nowrap"
                data-filter-control="true"
                data-toggle="table"
                data-search="true"
                data-show-export="false"
                data-click-to-select="false"
                data-pagination="true"
                data-url="">
                    <thead>
                        <tr>
                            <th>สมาชิก</th>
                            <th data-field="type" data-filter-control="select" data-sortable="true">type</th>
                            <th>จำนวนเงิน</th>
                            <th>วันที่ทำรายการ</th>
                            <th>จาก</th>
                            <th>ถึง</th>
                            <th>หลักฐาน</th>
                            <th>สถานะ</th>
                            {{-- @if( json_decode(auth()->user()->permissions)->member > 2  )
                            <th></th>
                            @endif --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transfer as $item)
                            <tr>
                                <td>{{ $item->username }}</td>
                                <td>{{ $item->type }}</td>
                                <td>{{ number_format((float) $item->amount,2) }} <i class="bx bx-bitcoin"></i></td>
                                <td>{{ date('d/m/Y H:i:s',$item->transfer_date) }}</td>
                                <td>
                                    @if($item->type == 'deposit')
                                    @switch($item->deposit_from_bank_type)
                                        @case("ธนาคารกรุงเทพ")
                                                <img src="{{ asset('images/bank/bbl.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารกสิกรไทย")
                                                <img src="{{ asset('images/bank/kbank.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารกรุงไทย")
                                                <img src="{{ asset('images/bank/ktb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารทหารไทยธนชาต")
                                                <img src="{{ asset('images/bank/ttb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารไทยพาณิชย์")
                                                <img src="{{ asset('images/bank/scb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารกรุงศรีอยุธยา")
                                                <img src="{{ asset('images/bank/bay.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารเกียรตินาคินภัทร")
                                                <img src="{{ asset('images/bank/kk.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารซีไอเอ็มบีไทย")
                                                <img src="{{ asset('images/bank/cimb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารทิสโก้")
                                                <img src="{{ asset('images/bank/tisco.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารยูโอบี")
                                                <img src="{{ asset('images/bank/uob.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารไทยเครดิต")
                                                <img src="{{ asset('images/bank/tcrb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารออมสิน")
                                                <img src="{{ asset('images/bank/gsb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร")
                                                <img src="{{ asset('images/bank/baac.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("askmepay")
                                                <img src="{{ asset('images/bank/askmepay.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("TrueMoney Wallet")
                                                <img src="{{ asset('images/bank/truemoney.png') }}" width="25" class="bank-logo">
                                                @break
                                            @default
                                                <div style="width:40px;height:40px;background:#E3A941;"></div>
                                        @endswitch
                                    {{ $item->deposit_from_bank_no }} <br>
                                    {{ $item->deposit_from_bank_name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($item->type == 'deposit')
                                    @switch($item->deposit_to_bank_type)
                                        @case("ธนาคารกรุงเทพ")
                                                <img src="{{ asset('images/bank/bbl.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารกสิกรไทย")
                                                <img src="{{ asset('images/bank/kbank.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารกรุงไทย")
                                                <img src="{{ asset('images/bank/ktb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารทหารไทยธนชาต")
                                                <img src="{{ asset('images/bank/ttb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารไทยพาณิชย์")
                                                <img src="{{ asset('images/bank/scb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารกรุงศรีอยุธยา")
                                                <img src="{{ asset('images/bank/bay.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารเกียรตินาคินภัทร")
                                                <img src="{{ asset('images/bank/kk.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารซีไอเอ็มบีไทย")
                                                <img src="{{ asset('images/bank/cimb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารทิสโก้")
                                                <img src="{{ asset('images/bank/tisco.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารยูโอบี")
                                                <img src="{{ asset('images/bank/uob.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารไทยเครดิต")
                                                <img src="{{ asset('images/bank/tcrb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารออมสิน")
                                                <img src="{{ asset('images/bank/gsb.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร")
                                                <img src="{{ asset('images/bank/baac.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("askmepay")
                                                <img src="{{ asset('images/bank/askmepay.png') }}" width="25" class="bank-logo">
                                                @break
                                            @case("TrueMoney Wallet")
                                                <img src="{{ asset('images/bank/truemoney.png') }}" width="25" class="bank-logo">
                                                @break
                                            @default
                                                <div style="width:40px;height:40px;background:#E3A941;"></div>
                                        @endswitch
                                    {{ $item->deposit_to_bank_no }} <br>
                                    {{ $item->deposit_to_bank_name }}
                                    @elseif($item->type == 'withdraw')
                                    @switch($item->bank_name)
                                    @case("ธนาคารกรุงเทพ")
                                            <img src="{{ asset('images/bank/bbl.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารกสิกรไทย")
                                            <img src="{{ asset('images/bank/kbank.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารกรุงไทย")
                                            <img src="{{ asset('images/bank/ktb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารทหารไทยธนชาต")
                                            <img src="{{ asset('images/bank/ttb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารไทยพาณิชย์")
                                            <img src="{{ asset('images/bank/scb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารกรุงศรีอยุธยา")
                                            <img src="{{ asset('images/bank/bay.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารเกียรตินาคินภัทร")
                                            <img src="{{ asset('images/bank/kk.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารซีไอเอ็มบีไทย")
                                            <img src="{{ asset('images/bank/cimb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารทิสโก้")
                                            <img src="{{ asset('images/bank/tisco.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารยูโอบี")
                                            <img src="{{ asset('images/bank/uob.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารไทยเครดิต")
                                            <img src="{{ asset('images/bank/tcrb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารออมสิน")
                                            <img src="{{ asset('images/bank/gsb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร")
                                            <img src="{{ asset('images/bank/baac.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("askmepay")
                                            <img src="{{ asset('images/bank/askmepay.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("TrueMoney Wallet")
                                            <img src="{{ asset('images/bank/truemoney.png') }}" width="25" class="bank-logo">
                                            @break
                                        @default
                                            <div style="width:40px;height:40px;background:#E3A941;"></div>
                                    @endswitch
                                {{ $item->bank_number }} <br>
                                {{ $item->account_name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($item->type == 'deposit')
                                    <button type="button" class="btn btn-primary" onclick="showEvidence('{{ env('APP_URL_IMAGE_EVIDENCE').$item->deposit_slip }}')">หลักฐาน</button>
                                    @endif
                                </td>
                                <td>
                                   @if ($item->status == 1)
                                        <h5><span class="badge badge-pill badge-warning text-bg-warning">{{ $item->status_code }}</span></h5>
                                    @elseif ($item->status == 2)
                                        <h5><span class="badge badge-pill badge-success text-bg-success">{{ $item->status_code }}</span></h5>
                                    @elseif($item->status == 3)
                                        <h5><span class="badge badge-pill badge-danger text-bg-danger">{{ $item->status_code }}</span></h5>
                                    @endif
                                </td>
                                @if( json_decode(auth()->user()->permissions)->member > 2  )
                                {{-- <td>
                                    @if($item->status == 1)
                                    <button type="button" class="btn btn-outline-success btn-sm  waves-effect waves-light" onclick="approveDeposit('#frmdeposit{{ $item->id }}')"><i class="bx bx-check"></i>Approve</button>
                                    <form action="{{ route('managemember.approveDeposit') }}" method="post" id="frmdeposit{{ $item->id }}" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="transfer_id" value="{{ $item->id }}" />
                                        <input type="hidden" name="member_id" value="{{ $item->member_id }}" />
                                        <input type="hidden" name="status" value="approve" />
                                        <input type="hidden" name="type" value="{{ $item->type }}" />
                                    </form>
                                    <button type="button" class="btn btn-outline-danger btn-sm  waves-effect waves-light" onclick="approveDeposit('#frmrejectdeposit{{ $item->id }}')"><i class="bx bx-check"></i>Reject</button>
                                    <form action="{{ route('managemember.approveDeposit') }}" method="post" id="frmrejectdeposit{{ $item->id }}" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="transfer_id" value="{{ $item->id }}" />
                                        <input type="hidden" name="member_id" value="{{ $item->member_id }}" />
                                        <input type="hidden" name="status" value="reject" />
                                        <input type="hidden" name="type" value="{{ $item->type }}" />
                                    </form>
                                    @elseif($item->status == 2)

                                    @endif

                                </td> --}}
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->
@endsection
@section('scripts')
<link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables/buttons.bootstrap4.css') }}">
<link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet">

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
    <script>
    @if (session('status'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif
    @if (session('del_status'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif

    function confirmPass(userid) {
        Swal.mixin({
        input: 'text',
        confirmButtonText: 'ยืนยัน &rarr;',
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก',
        progressSteps: ['1', '2']
      }).queue([
        {
          title: 'แจ้งเตือน',
          text: 'กรุณายืนยันรหัสผ่านของคุณเพือดำเนินการต่อ.'
        }
      ]).then( function (result) {

        if (result.value) {
            if (result.value != ""){

                $.ajax({
				type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				url: '{{ route('manageuser.checkPassword') }}',
				data: { password:result.value,userId:userid },
				success: function (data) {
					if(data!=false){
                        Swal.fire(
                        {
                            title: '',
                            html: '<h4>ชื่อผู้ใช้: '+data.name+'</br></h4>' +
                            '<h4>รหัสผ่าน: '+data.truepass+'</br></h4>',
                            type: 'success',
                            confirmButtonText: 'ตกลง',
                            confirmButtonClass: 'btn btn-confirm mt-2'
                        }
                    )
                    }else{
                        Swal.fire({
                        type: 'error',
                        title: "แจ้งเตือน!",
                        text: "รหัสผ่านไม่ถูกต้อง",
                        });
                    }
				}
			});


            }
        }
      })
    }
    function changePass(userid) {
        Swal.mixin({
            input: 'text',
            confirmButtonText: 'ยืนยัน &rarr;',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            progressSteps: ['1', '2']
        }).queue([
            {
            title: 'แจ้งเตือน',
            text: 'กรุณายืนยันรหัสผ่านของคุณเพือดำเนินการต่อ.'
            }
        ]).then( function (result) {
            if (result.value) {
                if (result.value != ""){

                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route('manageuser.checkPassword') }}',
                        data: { password:result.value,userId:userid },
                        success: function (data) {
                            if(data!=false){
                                Swal.mixin({
                                    input: 'text',
                                    confirmButtonText: 'ยืนยัน &rarr;',
                                    showCancelButton: true,
                                    cancelButtonText: 'ยกเลิก',
                                    progressSteps: ['1', '2']
                                }).queue([
                                    {
                                    title: 'แจ้งเตือน',
                                    text: 'รหัสผ่านใหม่'
                                    }
                                ]).then( function (result2) {
                                    $.ajax({
                                        type: 'post',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        url: '{{ route('manageuser.changePassword') }}',
                                        data: { password:result.value,userId:userid },
                                        success: function (data) {
                                            if(data!=false){
                                                Swal.fire(
                                                    {
                                                        title: 'เปลี่ยนรหัสผ่านสำเร็จ',
                                                        html: '<h4>ชื่อผู้ใช้: '+data.name+'</br></h4>' +
                                                        '<h4>รหัสผ่าน: '+data.truepass+'</br></h4>',
                                                        type: 'success',
                                                        confirmButtonText: 'ตกลง',
                                                        confirmButtonClass: 'btn btn-confirm mt-2'
                                                    }
                                                )
                                            }else{
                                                // Swal.fire({
                                                //     type: 'error',
                                                //     title: "แจ้งเตือน!",
                                                //     text: "รหัสผ่านไม่ถูกต้อง",
                                                // });
                                            }
                                        }
                                    });
                                })
                            }else{
                                Swal.fire({
                                type: 'error',
                                title: "แจ้งเตือน!",
                                text: "รหัสผ่านไม่ถูกต้อง",
                                });
                            }
                        }
                    });

                }
            }
      })
    }

    function deluser(userid) {
        Swal.mixin({
        input: 'text',
        confirmButtonText: 'ยืนยัน &rarr;',
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก',
        progressSteps: ['1', '2']
      }).queue([
        {
          title: 'แจ้งเตือน',
          text: 'กรุณายืนยันรหัสผ่านของคุณเพือดำเนินการต่อ.'
        }
      ]).then( function (result) {

        if (result.value) {
            if (result.value != ""){
                $.ajax({
				type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				url: '{{ route('manageuser.checkPassword') }}',
				data: { password:result.value,userId:userid },
				success: function (data) {
					if(data!=false){
                        $('#del'+userid).submit();
                    }else{
                        Swal.fire({
                        type: 'error',
                        title: "แจ้งเตือน!",
                        text: "รหัสผ่านไม่ถูกต้อง",
                        });
                    }
				}
			});


            }
        }
      })
    }
    </script>
    <script>
        function showEvidence($img){
            Swal.fire({
                imageUrl: $img,
                imageHeight: 600,
                imageAlt: "A tall image"
            });
        }

        function approveDeposit(form){
            Swal.fire({
                    title: 'แจ้งเตือน',
                    text: "ต้องการอัพเดตสถานะหรือไม่?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่',
                    cancelButtonText: 'ไม่, ยกเลิก!',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ml-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                       $(form).submit();
                    }
                });
        }
    </script>
@endsection
