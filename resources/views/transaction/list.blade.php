@extends('layouts.guest')
@section('styles')
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

    <div class="row">
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
                <p class="card-subtitle mb-4">
                </p>

                <table id="basic-datatable" class="table nowrap table-striped" data-filter-control="true"
                    data-toggle="table" data-search="true" data-show-export="false" data-click-to-select="false"
                    data-pagination="true" data-url="">
                    <thead class="table-light">
                        <tr>
                            <th data-field="username" data-sortable="true">{{ __('member') }}</th>
                            <th data-field="type" data-sortable="true">{{ __('dashboard.type') }}</th>
                            <th data-field="amount" data-sortable="true">{{ __('managemember.amount') }}</th>
                            <th data-sortable="true">{{ __('dashboard.Date_of_transaction') }}</th>
                            <th data-sortable="true">{{ __('managemember.from') }}</th>
                            <th data-sortable="true">{{ __('managemember.to') }}</th>
                            <th data-sortable="true">{{ __('main.promotion') }}</th>
                            <th data-sortable="true">{{ __('dashboard.status') }}</th>
                            @if (json_decode(auth()->user()->permissions)->transfer > 2)
                                <th data-sortable="true"></th>
                            @endif
                            <th>Transaction Hash</th>
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
                                <td>{{ (float) $item->amount }}</td>
                                <td>{{ date('d/m/Y H:i:s', $item->transfer_date) }}</td>
                                <td>
                                    @if ($item->type == 'deposit')
                                        <img src="{{ env('APP_LOGO') }}" width="25" class="bank-logo">
                                        <a class="hash-pill"
                                            href="https://bscscan.com/address/{{ $item->deposit_from_bank_no }}"
                                            target="_blank" rel="noopener">
                                            {{ short_middle($item->deposit_from_bank_no, 5, 5) }}
                                        </a> <br>{{ $item->deposit_from_bank_type }}
                                    @elseif($item->type == 'withdraw')
                                        <img src="{{ env('APP_LOGO') }}" width="25" class="bank-logo">
                                        <a class="hash-pill"
                                            href="https://bscscan.com/address/{{ $item->deposit_from_bank_no }}"
                                            target="_blank" rel="noopener">
                                            {{ short_middle($item->deposit_from_bank_no, 5, 5) }}
                                        </a> <br>
                                    @else
                                        {{ $item->order_id }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->type == 'deposit')
                                        <img src="{{ env('APP_LOGO') }}" width="25" class="bank-logo">
                                        <a class="hash-pill"
                                            href="https://bscscan.com/address/{{ $item->deposit_to_bank_no }}"
                                            target="_blank" rel="noopener">
                                            {{ short_middle($item->deposit_to_bank_no, 5, 5) }}
                                        </a>
                                        <br>
                                        {{ $item->deposit_to_bank_name }} {{ $item->deposit_to_bank_type }}
                                    @elseif($item->type == 'withdraw')
                                        <img src="{{ env('APP_LOGO') }}" width="25" class="bank-logo">
                                        <a class="hash-pill"
                                            href="https://bscscan.com/address/{{ $item->withdraw_bank_no }}"
                                            target="_blank" rel="noopener">
                                            {{ short_middle($item->withdraw_bank_no, 5, 5) }}
                                        </a> <br>
                                        {{ $item->withdraw_bank_name }}
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
                                        } else {
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
                                                <input type="hidden" name="member_id" value="{{ $item->member_id }}" />
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
                                                <input type="hidden" name="member_id" value="{{ $item->member_id }}" />
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
                                                <input type="hidden" name="member_id" value="{{ $item->member_id }}" />
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
                                <td><a class="hash-pill" href="https://bscscan.com/tx/{{ $item->ref_id }}"
                                        target="_blank" rel="noopener">
                                        {{ short_middle($item->ref_id, 10, 8) }}
                                    </a></td>
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
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            })
        @endif
        @if (session('del_status'))

            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        function showEvidence($img) {
            Swal.fire({
                imageUrl: $img,
                imageHeight: 600,
                imageAlt: "A tall image"
            });
        }

        function approveDeposit(form) {
            Swal.fire({
                title: '{{ __('main.Do you want to change your status?') }}',
                text: "**{{ __('main.Warningthe admin must transfer') }}**",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No, Cancel!',
                confirmButtonClass: 'btn btn-success mt-2',
                cancelButtonClass: 'btn btn-danger ml-2 mt-2',
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $(form).submit();
                }
            });
        }

        function confirm_turonver_on(form) {
            Swal.fire({
                title: '{{ __('main.Do you want to change your status?') }}',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No, Cancel!',
                confirmButtonClass: 'btn btn-success mt-2',
                cancelButtonClass: 'btn btn-danger ml-2 mt-2',
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $(form).submit();
                }
            });
        }
    </script>
@endsection
