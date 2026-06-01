@extends('layouts.guest')
@section('styles')
    <style>
        .badge {
            padding: 10px;
            font-weight: 400;
        }

        .txn-list-page .txn-flow-hint {
            font-size: 0.9rem;
            line-height: 1.45;
            padding: 0.65rem 0.85rem;
            border-radius: 6px;
            background: rgba(227, 169, 65, 0.12);
            border: 1px solid rgba(227, 169, 65, 0.35);
            color: #5d4a2e;
        }

        .txn-list-page th small {
            font-weight: 400;
            white-space: nowrap;
        }

        /* SweetAlert2 ยืนยันรายการฝากถอน — ปุ่มให้สอดคล้องธีม */
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
            transition: background 0.15s, border-color 0.15s, box-shadow 0.15s;
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .swal-txn-actions .swal-btn-cancel:hover {
            background: #f8f9fa !important;
            border-color: #adb5bd !important;
        }

        /* Copy button for withdrawal rows */
        .btn-copy-withdraw {
            background: none;
            border: none;
            padding: 10px;
            cursor: pointer;
            color: #adb5bd;
            transition: color 0.2s;
            margin-left: 8px;
            line-height: 1;
            min-width: 40px;
            min-height: 40px;
        }

        .btn-copy-withdraw:hover {
            color: #E3A941;
        }

        .btn-copy-withdraw:focus {
            outline: none;
        }

        .btn-copy-withdraw i {
            font-size: 24px;
        }
    </style>
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">{{ __('dashboard.Deposit_Withdraw') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">{{ __('dashboard.Deposit_Withdraw') }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row txn-list-page">
        <div class="col-12 card">
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <h4 class="card-title"></h4>
                <div class="txn-flow-hint mb-3">
                    <strong>โฟลว์ใหม่:</strong> สมาชิกแจ้งเฉพาะ<strong>ยอดฝากหรือถอน</strong> — <strong>ไม่มีสลิปโอน</strong><br>
                    แอดมินตรวจ<strong>ยอดเงิน</strong> (และบัญชีในคอลัมต้นทาง/ปลายทาง ถ้าระบบส่งมา) แล้วกด <strong>อนุมัติ</strong> หรือ <strong>ปฏิเสธ</strong>
                </div>

                <table id="basic-datatable" class="table nowrap table-striped" data-filter-control="true"
                    data-toggle="table" data-search="true" data-show-export="false" data-click-to-select="false"
                    data-pagination="true" data-url="">
                    <thead class="table-light">
                        <tr>
                            <th data-field="username" data-sortable="true">{{ __('member') }}</th>
                            <th data-field="type" data-sortable="true">{{ __('dashboard.type') }}</th>
                            <th data-field="amount" data-sortable="true">{{ __('managemember.amount') }}</th>
                            <th data-sortable="true">{{ __('dashboard.Date_of_transaction') }}</th>
                            <th data-sortable="true">{{ __('managemember.from') }}
                                <small class="text-muted d-block">(ถ้ามี)</small></th>
                            <th data-sortable="true">{{ __('managemember.to') }}
                                <small class="text-muted d-block">(ถ้ามี)</small></th>
                            <th data-sortable="true">{{ __('main.promotion') }}</th>
                            <th data-sortable="true">{{ __('dashboard.status') }}</th>
                            @if (json_decode(auth()->user()->permissions)->transfer > 2)
                                <th data-sortable="true"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transfer as $key_ => $item)
                            <tr>
                                <td>{{ $item->username }}</td>
                                <td>
                                    @if ($item->type == 'deposit')
                                        <span
                                            class="badge badge-pill badge-success text-bg-success">{{ __('dashboard.deposit') }}</span>
                                    @elseif($item->type == 'withdraw')
                                        <span
                                            class="badge badge-pill badge-warning text-bg-warning">{{ __('dashboard.withdraw') }}</span>
                                    @endif
                                </td>
                                <td class="text-nowrap font-weight-bold">{{ number_format((float) $item->amount, 2) }}</td>
                                <td>{{ date('d/m/Y H:i:s', strtotime($item->transfer_date)) }}</td>
                                @php
                                    /* โฟลว์ใหม่: ส่งแค่ยอดได้ — ไม่ต้องมี type ธนาคาร; ถ้าไม่มีเลขบัญชี+ชื่อ ให้ถือว่าว่าง (ไม่โชว์กล่องส้ม) */
                                    $depFromEmpty =
                                        $item->type === 'deposit' &&
                                        trim((string) ($item->deposit_from_bank_no ?? '')) === '' &&
                                        trim((string) ($item->deposit_from_bank_name ?? '')) === '';
                                    $depToEmpty =
                                        $item->type === 'deposit' &&
                                        trim((string) ($item->deposit_to_bank_no ?? '')) === '' &&
                                        trim((string) ($item->deposit_to_bank_name ?? '')) === '';
                                    $withdrawBankEmpty =
                                        $item->type === 'withdraw' &&
                                        trim((string) ($item->bank_number ?? '')) === '' &&
                                        trim((string) ($item->account_name ?? '')) === '' &&
                                        trim((string) ($item->bank_name ?? '')) === '';
                                @endphp
                                <td>
                                    @if ($item->type == 'deposit')
                                        @if ($depFromEmpty)
                                            <span class="text-muted">—</span>
                                        @else
                                        <x-bank-icon :bank-name="$item->deposit_from_bank_type" />
                                        {{ $item->deposit_from_bank_no }} <br>
                                        {{ $item->deposit_from_bank_name }}
                                        @endif
                                    @else
                                        {{ $item->order_id }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->type == 'deposit')
                                        @if ($depToEmpty)
                                            <span class="text-muted">—</span>
                                        @else
                                        <x-bank-icon :bank-name="$item->deposit_to_bank_type" />
                                        {{ $item->deposit_to_bank_no }} <br>
                                        {{ $item->deposit_to_bank_name }}
                                        @endif
                                    @elseif($item->type == 'withdraw')
                                        @if ($withdrawBankEmpty)
                                            <span class="text-muted">—</span>
                                        @else
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <x-bank-icon :bank-name="$item->bank_name" />
                                                {{ $item->bank_number }} <br>
                                                {{ $item->account_name }}
                                            </div>
                                            <button type="button"
                                                    class="btn-copy-withdraw"
                                                    onclick="copyWithdrawInfo(this)"
                                                    data-account-name="{{ $item->account_name }}"
                                                    data-bank-number="{{ $item->bank_number }}"
                                                    data-bank-name="{{ $item->bank_name }}"
                                                    data-amount="{{ number_format((float) $item->amount, 2) }}">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $turnover = 'ผ่าน';
                                    @endphp
                                    @if ($item->type != 'cashback')
                                        @if ($item->promotion == '' || $item->promotion == 'ไม่รับโปรโมชั่น')
                                            {{ __('managemember.Not_accepting_promotions') }}
                                        @else
                                            {!! $item->promotion !!}
                                            @if ($item->type == 'withdraw')
                                                @if ($item->turnover_on == 1)
                                                    @php

                                                        $turnover = app(
                                                            'App\Http\Controllers\TransactionController',
                                                        )->checkTurnOver($item->member_id);
                                                    @endphp
                                                    <br>
                                                    ยอดเทิร์นที่ทำได้ : {{ $turnover }}
                                                @endif
                                            @endif

                                            @if ($item->turnover_on == 1 && $item->type == 'deposit')
                                                <button type="button"
                                                    onclick="confirm_turonver_on('#form_turnover_on{{ $key_ }}')"
                                                    class="btn btn-sm btn-info">รีเซ็ตเทิร์นโอเวอร์</button>
                                                <form id="form_turnover_on{{ $key_ }}"
                                                    action="{{ route('managemember.turnover_on') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                                </form>
                                            @endif

                                        @endif
                                    @else
                                        cashback
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $status = '';
                                        if ($item->status_code == 'อนุมัติ') {
                                            $status = __('dashboard.approval');
                                        } elseif ($item->status_code == 'ปฏิเสธ') {
                                            $status = __('dashboard.refuse');
                                        } elseif ($item->status_code == 'BOT.อนุมัติ') {
                                            $status = __('dashboard.bot_approval');
                                        } elseif ($item->status_code == 'กำลังดำเนินการ') {
                                            $status = __('dashboard.In_progress');
                                        }else {
                                            $status = $item->status_code;
                                        }
                                    @endphp
                                    @if ($item->status == 1)
                                        <h5><span
                                                class="badge badge-pill badge-warning text-bg-warning">{{ $status }}</span>
                                        </h5>
                                    @elseif ($item->status == 2)
                                        <h5><span
                                                class="badge badge-pill badge-success text-bg-success">{{ $status }}</span>
                                        </h5>
                                    @elseif ($item->status == 4)
                                        <h5><span
                                                class="badge badge-pill badge-danger text-bg-danger">{{ $status }}</span>
                                        </h5>
                                    @else
                                        <h5><span
                                                class="badge badge-pill badge-danger text-bg-danger">{{ $status }}</span>
                                        </h5>
                                    @endif
                                </td>
                                @if (json_decode(auth()->user()->permissions)->transfer > 2)


                                    <td>
                                        @if ($item->status == 1)
                                            <button type="button"
                                                class="btn btn-outline-success btn-sm  waves-effect waves-light"
                                                @if (isset($turnover)) @if ($turnover != 'ผ่าน') disabled @endif
                                                @endif
                                                onclick="approveDeposit('#frmdeposit{{ $item->id }}')"><i
                                                    class="bx bx-check"></i>Approve</button>
                                            <form action="{{ route('managemember.approveDeposit') }}" method="post"
                                                id="frmdeposit{{ $item->id }}" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="transfer_id" value="{{ $item->id }}" />
                                                <input type="hidden" name="member_id" value="{{ $item->member_id }}" />
                                                <input type="hidden" name="status" value="approve" />
                                                <input type="hidden" name="type" value="{{ $item->type }}" />
                                            </form>

                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm  waves-effect waves-light"
                                                onclick="approveDeposit('#frmrejectdeposit{{ $item->id }}')"><i
                                                    class="bx bx-check"></i>Reject</button>
                                            <form action="{{ route('managemember.approveDeposit') }}" method="post"
                                                id="frmrejectdeposit{{ $item->id }}" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="transfer_id" value="{{ $item->id }}" />
                                                <input type="hidden" name="member_id"
                                                    value="{{ $item->member_id }}" />
                                                <input type="hidden" name="status" value="reject" />
                                                <input type="hidden" name="type" value="{{ $item->type }}" />
                                            </form>
                                        @elseif($item->status == 4)
                                            <button type="button"
                                                class="btn btn-outline-success btn-sm  waves-effect waves-light"
                                                @if (isset($turnover)) @if ($turnover != 'ผ่าน') disabled @endif
                                                @endif
                                                onclick="approveDeposit('#frmdeposit{{ $item->id }}')"><i
                                                    class="bx bx-check"></i>Approve</button>
                                            <form action="{{ route('managemember.approveDeposit') }}" method="post"
                                                id="frmdeposit{{ $item->id }}" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="transfer_id" value="{{ $item->id }}" />
                                                <input type="hidden" name="member_id"
                                                    value="{{ $item->member_id }}" />
                                                <input type="hidden" name="status" value="approve" />
                                                <input type="hidden" name="type" value="{{ $item->type }}" />
                                            </form>
                                            <button type="button"
                                                class="btn btn-outline-warning btn-sm  waves-effect waves-light"
                                                onclick="approveDeposit('#frmrependingdeposit{{ $item->id }}')"><i
                                                    class="bx bx-check"></i>Pending</button>
                                            <form action="{{ route('managemember.approveDeposit') }}" method="post"
                                                id="frmrependingdeposit{{ $item->id }}" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="transfer_id" value="{{ $item->id }}" />
                                                <input type="hidden" name="member_id"
                                                    value="{{ $item->member_id }}" />
                                                <input type="hidden" name="status" value="pending" />
                                                <input type="hidden" name="type" value="{{ $item->type }}" />
                                            </form>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm  waves-effect waves-light"
                                                onclick="approveDeposit('#frmrejectdeposit{{ $item->id }}')"><i
                                                    class="bx bx-check"></i>Reject</button>
                                            <form action="{{ route('managemember.approveDeposit') }}" method="post"
                                                id="frmrejectdeposit{{ $item->id }}" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="transfer_id" value="{{ $item->id }}" />
                                                <input type="hidden" name="member_id"
                                                    value="{{ $item->member_id }}" />
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
    <script
        src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
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

        function confirm_turonver_on(form) {
            Swal.fire({
                title: '{{ __('main.Do you want to change your status?') }}',
                text: '',
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

        function copyWithdrawInfo(button) {
            const accountName = button.dataset.accountName;
            const bankNumber = button.dataset.bankNumber;
            const bankName = button.dataset.bankName;
            const amount = button.dataset.amount;

            const text = `ชื่อ: ${accountName}\nเลขบัญชี: ${bankNumber}\nธนาคาร: ${bankName}\nยอดเงิน: ${amount}`;

            navigator.clipboard.writeText(text)
                .then(() => {
                    showCopyToast();
                })
                .catch(err => {
                    console.error('Copy failed:', err);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'ไม่สามารถคัดลอกได้',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
        }

        function showCopyToast() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'คัดลอกแล้ว!',
                showConfirmButton: false,
                timer: 1500
            });
        }
    </script>
@endsection
