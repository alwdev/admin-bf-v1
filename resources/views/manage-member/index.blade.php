@extends('layouts.guest')
@section('styles')
{{-- <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" /> --}}
<style type="text/css">
    .bank-logo{
        margin-bottom: 1rem;
    }
</style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">รายชื่อสมาชิก</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">รายชื่อสมาชิก</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row card">
    <div class="col-12">
        <div class="card-body">
            <h4 class="card-title"></h4>
            <p class="card-subtitle mb-4">
            </p>

                <table id="basic-datatable" class="table nowrap table-striped"
                data-filter-control="true"
                data-toggle="table"
                data-search="true"
                data-show-export="false"
                data-click-to-select="false"
                data-pagination="true"
                data-url="">
                    <thead>
                        <tr>
                            <th data-field="member_id" data-filter-control="input" data-sortable="true">รหัสผู้ใช้</th>
                            <th data-field="username" data-filter-control="input" data-sortable="true">ชื่อผู้ใช้</th>
                            @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                            <th>รหัสผ่าน</th>
                            @endif
                            <th data-field="fullname" data-filter-control="input" data-sortable="true">ชื่อ - นามสกุล</th>
                            <th data-sortable="true">ยอดเงิน</th>
                            <th>บัญชี</th>
                            <th data-sortable="true">วันที่สมัคร</th>
                            <th></th>
                            @if( json_decode(auth()->user()->permissions)->member > 2  )
                            <th>จัดการ</th>
                            @endif
                            <th>แก้ไขโดย</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($memberlist as $key => $member)
                        @if ($member->enable == 0)
                            <tr style="color: rgb(211, 88, 5);">
                        @else
                            <tr style="color: rgb(5, 5, 5);">
                        @endif
                            <td>{{ $member->member_id }}</td>
                            <td>{{ $member->username }}</td>
                            @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                            <td><button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="changePass('{{ $member->id }}')"><i class="bx bx-edit-alt"></i>เปลียน</button></td>
                            @endif
                            <td>{{ $member->fullname }}</td>
                            @php
                                $member_balance = app(\App\Http\Controllers\BetflixController::class)->Balance($member->username);
                            @endphp
                            <td class="text-right">{{ $member_balance }} ฿
                                @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="editBalance('{{ $member->id }}','{{ $member->username }}','{{ Auth::user()->id }}')"><i class="bx bx-edit-alt"></i></button>
                                @endif
                            </td>
                            <td>
                                <button href="่javascript:void(0);" data-toggle="modal" data-target="#exampleModal{{ $key }}" type="button" class="btn btn-secondary  btn-sm waves-effect waves-light">บัญชี</button>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal{{ $key }}" tabindex="-1" aria-labelledby="exampleModal{{ $key }}Label" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="exampleModal{{ $key }}Label">รายละเอียดบัญชี</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                          </button>
                                        </div>
                                        <div class="modal-body text-center">
                                            @switch($member->bank_name)
                                            @case("ธนาคารกรุงเทพ")
                                                    <img src="{{ asset('images/bank/bbl.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารกสิกรไทย")
                                                    <img src="{{ asset('images/bank/kbank.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารกรุงไทย")
                                                    <img src="{{ asset('images/bank/ktb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารทหารไทยธนชาต")
                                                    <img src="{{ asset('images/bank/ttb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารไทยพาณิชย์")
                                                    <img src="{{ asset('images/bank/scb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารกรุงศรีอยุธยา")
                                                    <img src="{{ asset('images/bank/bay.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารเกียรตินาคินภัทร")
                                                    <img src="{{ asset('images/bank/kk.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารซีไอเอ็มบีไทย")
                                                    <img src="{{ asset('images/bank/cimb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารทิสโก้")
                                                    <img src="{{ asset('images/bank/tisco.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารยูโอบี")
                                                    <img src="{{ asset('images/bank/uob.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารไทยเครดิต")
                                                    <img src="{{ asset('images/bank/tcrb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารออมสิน")
                                                    <img src="{{ asset('images/bank/gsb.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร")
                                                    <img src="{{ asset('images/bank/baac.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("askmepay")
                                                    <img src="{{ asset('images/bank/askmepay.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @case("TrueMoney Wallet")
                                                    <img src="{{ asset('images/bank/truemoney.png') }}" width="80" class="bank-logo">
                                                    @break
                                                @default
                                                    <div style="width:40px;height:40px;background:#E3A941;"></div>
                                            @endswitch
                                            <br/>
                                            <h3>{{ $member->bank_name }}</h3>
                                            <h3>{{ $member->account_name }}</h3>
                                            <h3>{{ $member->bank_number }}</h3>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                            </td>
                            <td>{{ $member->created_at->format('d/m/Y H:i:s') }}</td>
                            <td><a href="{{ route('managemember.historyTransfer',$member->id) }}" type="button" class="btn btn-success  btn-sm waves-effect waves-light"><i class="bx bx-bitcoin"></i> การเงิน</a></td>
                            @if( json_decode(auth()->user()->permissions)->member > 2  )
                            <td>
                                <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" onclick="lock('{{ $member->id }}','{{ Auth::user()->id }}','{{ $member->username }}','{{ $member->enable }}')" style="width: 80px;"><i class="bx bx-edit-alt" ></i>{{ $member->enable == 1 ? 'ล็อค' : 'ปลดล็อค'  }}</button>
                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="delete_member('{{ $member->id }}','{{ Auth::user()->id }}','{{ $member->username }}')" style="width: 80px;"><i class="bx bx-edit-alt"></i>ลบ</button>
                            </td>
                            @endif
                            <td>{{ App\Http\Controllers\ManageMemberController::staff_detail($member->update_by) }}</td>
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

    <!-- third party js -->
    {{-- <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
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
    <script src="{{ asset('plugins/datatables/vfs_fonts.js')}}"></script> --}}
    <!-- third party js ends -->

    <!-- Datatables init -->
    {{-- <script src="{{ asset('pages/datatables-demo.js')}}"></script> --}}
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

    function editBalance(member_id,member_name,user_id) {
        Swal.mixin({
                input: 'text',
                confirmButtonText: 'ยืนยัน &rarr;',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                progressSteps: ['1', '2']
            }).queue([
                {
                title: 'ต้องแก้ไขยอดเงินหรือไม่',
                text: 'ยอดเงิน'
                }
            ]).then( function (result) {
                if (result.value) {
                        $.ajax({
                            type: 'post',
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route('managemember.memberEditBalance') }}',
                            data: { member_id:member_id,user_id:user_id,balance:Number(result.value) },
                            success: function (data) {
                                if(data!=false){
                                    Swal.fire(
                                            {
                                                title: 'แก้ไขยอดเงิน',
                                                html: '<h4>ชื่อผู้ใช้: '+member_name+' </br></h4>' +
                                                '<h4>แก้ไขยอดเงินแล้ว</br></h4>',
                                                type: 'success',
                                                confirmButtonText: 'ตกลง',
                                                confirmButtonClass: 'btn btn-confirm mt-2'

                                            }
                                        ).then(function() {
                                            location.reload();
                                        });

                                    }
                                }
                        });
                }else if(result.dismiss == 'cancel'){
                    console.log('cancel');
                    }
            })
    }
    function changePass(member_id) {
        Swal.mixin({
            // input: 'text',
            confirmButtonText: 'ยืนยัน &rarr;',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            progressSteps: ['1', '2']
        }).queue([
            {
            title: 'ต้องการเปลี่ยนรหัสผ่าน หรือไม่',
            }
        ]).then( function (result) {
            if (result.value) {
                    $.ajax({
                        type: 'post',
                         headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          },
                          url: '{{ route('managemember.changePassword') }}',
                          data: { id:member_id },
                          success: function (data) {
                            if(data!=false){
                                Swal.fire(
                                        {
                                            title: 'เปลี่ยนรหัสผ่านสำเร็จ',
                                            html: '<h4>ชื่อผู้ใช้: '+data[0].username+'</br></h4>' +
                                            '<h4>รหัสผ่าน: '+data[1]+'</br></h4>',
                                            type: 'success',
                                            confirmButtonText: 'ตกลง',
                                            confirmButtonClass: 'btn btn-confirm mt-2'
                                        }
                                    ).then(function() {
                                        location.reload();
                                    });
                                }
                            }
                    });
            }
      })
    }

    function lock(member_id,user_id,member_name,status) {
        if(status == 1){
            Swal.mixin({
                confirmButtonText: 'ยืนยัน &rarr;',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                progressSteps: ['1', '2']
            }).queue([
                {
                title: 'ต้องการล็อค Member หรือไม่',
                }
            ]).then( function (result) {
                if (result.value) {
                        $.ajax({
                            type: 'post',
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route('managemember.memberlock') }}',
                            data: { member_id:member_id,user_id:user_id,status:status },
                            success: function (data) {
                                if(data!=false){
                                    Swal.fire(
                                            {
                                                title: 'ล็อคการใช้งาน',
                                                html: '<h4>ชื่อผู้ใช้: '+member_name+'</br></h4>' +
                                                '<h4>ถูกล็อคการใช้งานแล้ว</br></h4>',
                                                type: 'success',
                                                confirmButtonText: 'ตกลง',
                                                confirmButtonClass: 'btn btn-confirm mt-2'

                                            }
                                        ).then(function() {
                                            location.reload();
                                        });

                                    }
                                }
                        });
                }
            })
        }else{
            Swal.mixin({
                confirmButtonText: 'ยืนยัน &rarr;',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                progressSteps: ['1', '2']
            }).queue([
                {
                title: 'ต้องการปลดล็อค Member หรือไม่',
                }
            ]).then( function (result) {
                if (result.value) {
                        $.ajax({
                            type: 'post',
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route('managemember.memberlock') }}',
                            data: { member_id:member_id,user_id:user_id,status:status },
                            success: function (data) {
                                if(data!=false){
                                    Swal.fire(
                                            {
                                                title: 'ปลดล็อคการใช้งาน',
                                                html: '<h4>ชื่อผู้ใช้: '+member_name+'</br></h4>' +
                                                '<h4>ถูกปลดล็อคการใช้งานแล้ว</br></h4>',
                                                type: 'success',
                                                confirmButtonText: 'ตกลง',
                                                confirmButtonClass: 'btn btn-confirm mt-2'

                                            }
                                        ).then(function() {
                                            location.reload();
                                        });
                                    }
                                }
                        });
                }
            })
        }
    }

    function delete_member(member_id,user_id,member_name) {
        Swal.mixin({
            // input: 'text',
            confirmButtonText: 'ยืนยัน &rarr;',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            progressSteps: ['1', '2']
        }).queue([
            {
            title: 'ต้องการลบสมาชิก หรือไม่',
            }
        ]).then( function (result) {
            if (result.value) {
                    $.ajax({
                        type: 'post',
                         headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          },
                          url: '{{ route('managemember.memberdelete') }}',
                          data: { member_id:member_id,user_id:user_id },
                          success: function (data) {
                            if(data!=false){
                                Swal.fire(
                                    {
                                        title: 'ลบสมาชิกใช้งาน',
                                        html: '<h4>ชื่อผู้ใช้: '+member_name+'</br></h4>' +
                                        '<h4>สมาชิกถูกลบแล้ว</br></h4>',
                                        type: 'success',
                                        confirmButtonText: 'ตกลง',
                                        confirmButtonClass: 'btn btn-confirm mt-2'
                                    }
                                    ).then(function() {
                                        location.reload();
                                    });
                                }
                            }
                    });
            }
      })
    }
    </script>
@endsection
