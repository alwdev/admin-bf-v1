@extends('layouts.guest')
@section('styles')
    <style>
        .swal-txn-popup {
            border-radius: 12px !important;
            padding: 1.5rem 1.25rem 1.25rem !important;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18) !important;
        }
        .swal-txn-actions {
            display: flex !important;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 0.75rem !important;
            width: 100%;
            margin-top: 0.5rem !important;
        }
        .swal-txn-actions .swal-btn-confirm,
        .swal-txn-actions .swal-btn-cancel {
            margin: 0 !important;
            flex: 0 0 auto;
            min-width: 120px;
            padding: 0.55rem 1.35rem !important;
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            line-height: 1.4 !important;
        }
        .swal-txn-actions .swal-btn-confirm {
            background: #E3A941 !important;
            border: 1px solid #E3A941 !important;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(227, 169, 65, 0.35);
        }
        .swal-txn-actions .swal-btn-confirm:hover {
            background: #cf9a38 !important;
            border-color: #cf9a38 !important;
        }
        .swal-txn-actions .swal-btn-cancel {
            background: #fff !important;
            border: 1px solid #ced4da !important;
            color: #495057 !important;
        }
        .swal-txn-actions .swal-btn-cancel:hover {
            background: #f8f9fa !important;
            border-color: #adb5bd !important;
        }
    </style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">รายการฝากถอน</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">รายการฝากถอน</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12 card">
        <div class="card-body">
            <h4 class="card-title"></h4>
            <p class="card-subtitle mb-3 p-2 rounded" style="background:rgba(227,169,65,.12);border:1px solid rgba(227,169,65,.35);color:#5d4a2e;font-size:.9rem;">
                <strong>โฟลว์ใหม่:</strong> สมาชิกแจ้งเฉพาะยอด — <strong>ไม่มีสลิป</strong> แอดมินตรวจยอดแล้วกดอนุมัติ/ปฏิเสธ
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
                        <th data-field="username" data-filter-control="input" data-sortable="true">สมาชิก</th>
                        <th data-field="type" data-filter-control="select" data-sortable="true">type</th>
                        <th data-field="amount" data-sortable="true">จำนวนเงิน</th>
                        <th data-sortable="true">วันที่ทำรายการ</th>
                        <th data-sortable="true">จาก <small class="text-muted">(ถ้ามี)</small></th>
                        <th data-sortable="true">ถึง <small class="text-muted">(ถ้ามี)</small></th>
                        <th data-sortable="true">โปรโมชั่น</th>
                        <th data-sortable="true">สถานะ</th>
                        @if( json_decode(auth()->user()->permissions)->transfer > 2  )
                        <th data-sortable="true"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfer as $item)
                        <tr>
                            <td>{{ $item->username }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ (float) $item->amount }}</td>
                            <td>{{ date('d/m/Y H:i:s',$item->transfer_date) }}</td>
                            @php
                                $depFromEmptyRt =
                                    $item->type === 'deposit' &&
                                    trim((string) ($item->deposit_from_bank_no ?? '')) === '' &&
                                    trim((string) ($item->deposit_from_bank_name ?? '')) === '';
                                $depToEmptyRt =
                                    $item->type === 'deposit' &&
                                    trim((string) ($item->deposit_to_bank_no ?? '')) === '' &&
                                    trim((string) ($item->deposit_to_bank_name ?? '')) === '';
                                $withdrawBankEmptyRt =
                                    $item->type === 'withdraw' &&
                                    trim((string) ($item->bank_number ?? '')) === '' &&
                                    trim((string) ($item->account_name ?? '')) === '' &&
                                    trim((string) ($item->bank_name ?? '')) === '';
                            @endphp
                            <td>
                                @if($item->type == 'deposit')
                                @if ($depFromEmptyRt)
                                    <span class="text-muted">—</span>
                                @else
                                @switch($item->deposit_from_bank_type)
                                    @case("ธนาคารกรุงเทพ")
                                            <img src="{{ asset('images/bank/bbl.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารกสิกรไทย")
                                            <img src="{{ asset('images/bank/kbank.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("KBANK")
                                            <img src="{{ asset('images/bank/kbank.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารกรุงไทย")
                                            <img src="{{ asset('images/bank/ktb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("KTB")
                                            <img src="{{ asset('images/bank/ktb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารทหารไทยธนชาต")
                                            <img src="{{ asset('images/bank/ttb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารไทยพาณิชย์")
                                            <img src="{{ asset('images/bank/scb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("SCB")
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
                                @endif
                                @else
                                {{ $item->order_id }}
                                @endif
                            </td>
                            <td>
                                @if($item->type == 'deposit')
                                @if ($depToEmptyRt)
                                    <span class="text-muted">—</span>
                                @else
                                @switch($item->deposit_to_bank_type)
                                    @case("ธนาคารกรุงเทพ")
                                            <img src="{{ asset('images/bank/bbl.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารกสิกรไทย")
                                            <img src="{{ asset('images/bank/kbank.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("KBANK")
                                            <img src="{{ asset('images/bank/kbank.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารกรุงไทย")
                                            <img src="{{ asset('images/bank/ktb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("KTB")
                                            <img src="{{ asset('images/bank/ktb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารทหารไทยธนชาต")
                                            <img src="{{ asset('images/bank/ttb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("ธนาคารไทยพาณิชย์")
                                            <img src="{{ asset('images/bank/scb.png') }}" width="25" class="bank-logo">
                                            @break
                                        @case("SCB")
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
                                @endif
                                @elseif($item->type == 'withdraw')
                                @if ($withdrawBankEmptyRt)
                                    <span class="text-muted">—</span>
                                @else
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
                                    @case("TrueMoney Wallet")
                                        <img src="{{ asset('images/bank/truemoney.png') }}" width="25" class="bank-logo">
                                        @break
                                    @default
                                        <div style="width:40px;height:40px;background:#E3A941;"></div>
                                @endswitch
                            {{ $item->bank_number }} <br>
                            {{ $item->account_name }}
                                @endif
                            @else
                            -
                                @endif
                            </td>
                            <td>
                                @php
                                    $turnover = 'ผ่าน';
                                @endphp
                                @if($item->type != 'cashback')
                                    @if($item->promotion =='')
                                        ไม่รับโปรโมชั่น
                                    @else
                                        {!! $item->promotion !!}
                                        @if($item->type == 'withdraw')
                                        @php

                                            $turnover = app('App\Http\Controllers\TransactionController')->checkTurnOver($item->member_id);
                                        @endphp
                                        <br>
                                        ยอดเทิร์นที่ทำได้ : {{ $turnover }}
                                        @endif
                                    @endif
                                @else
                                    cashback
                                @endif
                            </td>
                            <td>
                               @if ($item->status == 1)
                                    <h5><span class="badge badge-pill badge-warning text-bg-warning">{{ $item->status_code }}</span></h5>
                                @elseif ($item->status == 2)
                                    <h5><span class="badge badge-pill badge-success text-bg-success">{{ $item->status_code }}</span></h5>
                                @else
                                    <h5><span class="badge badge-pill badge-danger text-bg-danger">{{ $item->status_code }}</span></h5>
                                @endif
                            </td>
                            @if( json_decode(auth()->user()->permissions)->transfer > 2  )


                            <td>
                                @if($item->status == 1)
                                <button type="button" class="btn btn-outline-success btn-sm  waves-effect waves-light" @if(isset($turnover)) @if($turnover != 'ผ่าน') disabled @endif @endif onclick="approveDeposit('#frmdeposit{{ $item->id }}')"><i class="bx bx-check"></i>Approve</button>
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
                                {{-- <a href="#" type="button" class="btn btn-outline-primary    btn-sm  waves-effect waves-light"><i class="bx bx-revision"></i></a>
                                <a href="#" type="button" class="btn btn-outline-primary    btn-sm  waves-effect waves-light"><i class="bx bxs-credit-card"></i></a>
                                <a href="#" type="button" class="btn btn-outline-primary    btn-sm  waves-effect waves-light"><i class="bx bx-undo"></i></a> --}}
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
        // $('#basic-datatable').DataTable({
        //     "order": [[ 2, "desc" ]]
        // });

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

        function approveDeposit(form) {
            Swal.fire({
                title: 'ยืนยันการดำเนินการ',
                text: 'รายการนี้ไม่มีสลิปจากสมาชิก — ตรวจสอบยอดและข้อมูลบัญชีก่อนอนุมัติ',
                icon: 'warning',
                showCancelButton: true,
                focusCancel: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-txn-popup',
                    confirmButton: 'swal-btn-confirm',
                    cancelButton: 'swal-btn-cancel',
                    actions: 'swal-txn-actions'
                }
            }).then(function(result) {
                if (result.isConfirmed || result.value) {
                    $(form).submit();
                }
            });
        }
    </script>
@endsection
