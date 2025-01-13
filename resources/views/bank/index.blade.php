@extends('layouts.guest')
@section('styles')
{{-- <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" /> --}}
<style>
    .badge {

        padding: 10px;
        font-weight: 400;
    }
</style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">สมุดบัญชีธนาคาร</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">สมุดบัญชีธนาคาร</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
<div class="card-header" style="background: transparent;">

</div>
<div class="row card">
    <div class="col-12">

        <div class="card-body">    
            @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="{{ route('bankaccount.create') }}">เพิ่มสมุดบัญชี</a>
                <a type="button" class="btn btn-success waves-effect waves-light" href="javascript:void(0);"  data-toggle="modal" data-target="#staticBackdrop">จัดการยอดเงิน</a>
            @endif

            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">จัดการยอดคงเหลือ</h1>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <form id="form-add-transfer" action="{{ route('bank.bank_forward_balance_create') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="amount">จำนวนเงิน</label>
                                        <input type="number" class="form-control" id="amount" name="amount" autocomplete="off" required value="{{ old('amount') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="amount">ประเภท</label>
                                        <select name="type" class="form-control" id="type">
                                             <option value="เงินเข้า">เงินเข้า</option>
                                             <option value="เงินออก">เงินออก</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="bank_to">บัญชีธนาคารผู้รับ</label>
                                        <select name="bank_to" class="form-control" id="bank_to">
                                            <option></option>
                                            @foreach ($banks as $item)
                                            <option value="{{ $item->id }}">{{ $item->bank_name.' ( '.$item->account_name.' - '.$item->account_name.' )' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="note">หมายเหตุ</label>
                                        <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>
                       
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary" >บันทึก</button>
                    </div>
                </form>
                </div>
                </div>
            </div>

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
                <thead  class="table-light">
                        <tr>
                            <th></th>
                            <th data-field="bank_name"  data-sortable="true">ธนาคาร</th>
                            <th data-field="account_name" data-sortable="true">ชื่อบัญชี</th>
                            <th data-field="account_no" data-sortable="true">หมายเลขบัญชี</th>
                            <th class="text-center">ยอดคงเหลือ</th>
                            <th class="text-center">สถานะ</th>
                            <th data-sortable="true">อัพเดทล่าสุด</th>
                            @if( json_decode(auth()->user()->permissions)->member > 2  )
                            <th>จัดการ</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($banks as $item)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ asset('images/bank/'.$item->logo) }}" class="avatar-xs">
                                </td>
                                <td>{{ $item->bank_name }}</td>
                                <td>{{ $item->account_name }}</td>
                                <td>{{ $item->account_no }}</td>
                                <td>{{ number_format($item->balance,2,'.',',') }}</td>
                                <td class="text-center" style="font-size: 16px;">
                                    @if ( $item->enable == 1)
                                    <span class="badge badge-pill badge-success">แสดง</span>
                                    @else
                                    <span class="badge badge-pill badge-danger">ซ่อน</span>
                                    @endif    
                                </td>
                                <td>{{ $item->created_at }}</td>
                                @if( json_decode(auth()->user()->permissions)->member > 2  )
                                <td>
                                    <a href="{{ route('bankaccount.show',$item->id) }}" type="button" class="btn btn-primary btn-sm waves-effect waves-light"><i class="bx bx-edit"></i>แก้ไข</a>
                                    @if( json_decode(auth()->user()->permissions)->member > 3  )
                                        <a href="javascript:void(0)" onclick="approvedel('#delbank{{ $item->id }}');" type="button" class="btn btn-danger btn-sm waves-effect waves-light"><i class="bx bxs-trash"></i>ลบ</a>
                                        <form action="{{ route('bankaccount.destroy') }}" method="post" id="delbank{{ $item->id }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                        </form>
                                    @endif
                                </td>
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
 @if (session('banksave'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif

    function approvedel(form){
            Swal.fire({
                    title: 'แจ้งเตือน',
                    text: "ต้องการลบข้อมูลสมุดบัญชีธนาคารนี้หรือไม่?",
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
