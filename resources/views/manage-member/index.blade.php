@extends('layouts.guest')
@section('styles')
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .bank-logo {
            margin-bottom: 1rem;
        }
    </style>
    <style type="text/css">
        /* ลดขนาดฟอนต์ในตาราง */
        #basic-datatable {
            font-size: 0.9rem;
            /* ปรับขนาดฟอนต์หลักของตาราง */
        }

        /* ปรับปรุงส่วนหัวของตาราง */
        #basic-datatable thead th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            padding: 8px 12px;
            /* ลด padding ในส่วนหัว */
        }

        /* ปรับปรุงข้อมูลในแต่ละแถว */
        #basic-datatable tbody td {
            padding: 8px 12px;
            /* ลด padding ในส่วนเนื้อหา */
        }

        /* ปรับสไตล์แถวให้ดูทันสมัยขึ้น */
        #basic-datatable tbody tr {
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            margin-bottom: 4px;
            transition: all 0.2s;
        }

        /* เอฟเฟกต์เมื่อวางเมาส์บนแถว */
        #basic-datatable tbody tr:hover {
            background-color: #f1f1f1;
            transform: translateY(-1px);
            /* ขยับขึ้นเล็กน้อยเมื่อวางเมาส์ */
        }

        /* จัดการปุ่มให้ดูเรียบร้อย */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            /* ลดขนาดปุ่มเล็ก */
            font-size: 0.8rem;
        }

        /* สไตล์สำหรับปุ่มที่มีไอคอน */
        .btn-icon {
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #basic-datatable th {
            text-align: center;
            /* จัดให้ข้อความอยู่ตรงกลาง */
            vertical-align: middle;
            /* จัดให้ข้อความอยู่กึ่งกลางในแนวตั้ง */
        }

        .btn-wallet {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    </style>
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">{{ __('managemember.Member_list') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">/</a></li>
                        <li class="breadcrumb-item active">{{ __('managemember.Member_list') }}</li>
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

                <table id="basic-datatable" class="table nowrap table-striped" data-filter-control="true"
                    data-toggle="table" data-search="true" data-show-export="false" data-click-to-select="false"
                    data-pagination="true" data-url="">
                    <thead>
                        <tr>
                            <th data-field="username" data-filter-control="input" data-sortable="true">
                                {{ __('managemember.user_name') }}</th>
                            <th data-field="email" data-filter-control="input" data-sortable="true">email</th>
                            <th data-sortable="true">{{ __('managemember.amount') }}</th>
                            <th></th>
                            <th>{{ __('Affiliate') }}</th>
                            <th>{{ __('managemember.manage') }}</th>
                            <th>{{ __('managemember.Edited_by') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($memberlist as $key => $member)
                            <tr class="{{ $member->enable == 0 ? 'table-secondary' : '' }}">
                                <td>{{ $member->username }}</td>
                                <td>{{ $member->email }}</td>
                                <td class="text-right">{{ $member->wallet_balance }}
                                    @if (json_decode(auth()->user()->permissions)->manageuser > 2)
                                        <button type="button"
                                            class="btn btn-primary btn-sm waves-effect waves-light btn-edit-balance"
                                            data-toggle="modal" data-target="#editBalanceModal"
                                            data-member-id="{{ $member->id }}"
                                            data-member-username="{{ $member->username }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('managemember.historyTransfer', $member->id) }}" type="button"
                                        class="btn btn-success btn-sm waves-effect waves-light">
                                        {{ __('managemember.finance') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('managemember.affiliates', ['id' => $member->id]) }}"
                                        class="btn btn-primary btn-sm waves-effect waves-light">
                                        <i class="bx bx-group"></i>
                                        {{ is_null(json_decode($member->ref_user, true)) ? 0 : count(json_decode($member->ref_user, true)) }}
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm btn-info-modal" data-toggle="modal"
                                        data-target="#memberInfoModal" data-member-data="{{ json_encode($member) }}">
                                        <i class="bx bx-info-circle"></i> Info
                                    </button>
                                </td>

                                <td>{{ App\Http\Controllers\ManageMemberController::staff_detail($member->update_by) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
    </div>
    <!-- end row-->

    <div class="modal fade" id="editPassModal" tabindex="-1" aria-labelledby="editPassModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPassModalLabel">{{ __('managemember.change_password') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('managemember.changePassword') }}" method="POST" id="form_editPass">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="modal-pass-member-id" required>
                        <div class="form-group text-left">
                            <label for="new_password">{{ __('managemember.Enter_a_new_password') }}</label>
                            <input type="text" name="password" class="form-control" id="new_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateBankAccountModal" tabindex="-1" aria-labelledby="updateBankAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateBankAccountModalLabel">Edit wallet address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('managemember.memberupdateBankAccount') }}" method="POST"
                    id="form_updateBankAccount">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="member_id" id="modal-wallet-member-id" required>
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" required>
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="modal-wallet-address">wallet address</label>
                                <input type="text" class="form-control w-100" id="modal-wallet-address"
                                    name="wallet_address" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBalanceModal" tabindex="-1" aria-labelledby="editBalanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBalanceModalLabel">{{ __('managemember.Edit_balance') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('managemember.memberEditBalance') }}" method="POST" id="form_editBalance">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="member_id" id="modal-member-id" required>
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" required>

                        <div class="form-group text-left">
                            <label for="modal-member-username">{{ __('managemember.user_name') }}</label>
                            <input type="text" class="form-control" id="modal-member-username" readonly>
                        </div>

                        <div class="form-group text-left">
                            <label for="modal-balance-amount">{{ __('managemember.amount') }}</label>
                            <input type="text" name="balance" class="form-control" id="modal-balance-amount"
                                onkeypress="return isNumberKey(event)" required>
                        </div>

                        <div class="form-group text-left">
                            <label for="modal-balance-type">{{ __('dashboard.type') }}</label>
                            <select name="type" class="form-control" id="modal-balance-type" required>
                                <option value=""></option>
                                <option value="เติมมือ">{{ __('dashboard.Add_normal') }}</option>
                                <option value="คืนลูกค้า">{{ __('dashboard.Customer_Refund') }}</option>
                                <option value="แก้เครดิต">{{ __('managemember.Edit_balance') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="memberInfoModal" tabindex="-1" aria-labelledby="memberInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="memberInfoModalLabel">รายละเอียดสมาชิก</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="member-info-content">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="d-flex">
                        @if (json_decode(auth()->user()->permissions)->member > 2)
                            <button type="button"
                                class="btn btn-warning btn-sm waves-effect waves-light d-flex align-items-center"
                                id="modal-lock-button" style="width: 80px; margin-right: 10px;">
                                <i class="bx bx-edit-alt mr-1"></i><span id="lock-unlock-text"></span>
                            </button>
                            <button type="button"
                                class="btn btn-danger btn-sm waves-effect waves-light d-flex align-items-center"
                                id="modal-delete-button" style="width: 80px;">
                                <i class="bx bx-edit-alt mr-1"></i><span>{{ __('managemember.delete') }}</span>
                            </button>
                        @endif
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="affiliateModal" tabindex="-1" aria-labelledby="affiliateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="affiliateModalLabel">Affiliate Tree</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="affiliate-tree-container">
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script
        src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        function editBalance(member_id, member_name, user_id) {
            Swal.mixin({
                input: 'text',
                confirmButtonText: '{{ __('main.yes') }} &rarr;',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                progressSteps: ['1', '2']
            }).queue([{
                title: '{{ __('managemember.Edit_balance') }}',
                text: '{{ __('managemember.Enter_the_amount') }}',
            }]).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route('managemember.memberEditBalance') }}',
                        data: {
                            member_id: member_id,
                            user_id: user_id,
                            balance: Number(result.value)
                        },
                        success: function(data) {
                            if (data != false) {
                                Swal.fire({
                                    title: '{{ __('managemember.Edit_balance') }}',
                                    html: '<h4>{{ __('managemember.user_name') }}: ' +
                                        member_name + ' </br></h4>' +
                                        '<h4>success</br></h4>',
                                    type: 'success',
                                    confirmButtonText: '{{ __('main.yes') }}',
                                    confirmButtonClass: 'btn btn-confirm mt-2'

                                }).then(function() {
                                    location.reload();
                                });

                            }
                        }
                    });
                } else if (result.dismiss == 'cancel') {
                    console.log('cancel');
                }
            })
        }

        function changePass(member_id) {
            Swal.mixin({
                // input: 'text',
                confirmButtonText: '{{ __('main.yes') }} &rarr;',
                showCancelButton: true,
                cancelButtonText: '{{ __('main.no') }}',
                progressSteps: ['1', '2']
            }).queue([{
                title: '{{ __('managemember.Change_password') }}',
                text: '',
            }]).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route('managemember.changePassword') }}',
                        data: {
                            id: member_id
                        },
                        success: function(data) {
                            if (data != false) {
                                Swal.fire({
                                    title: '{{ __('managemember.Change_password') }}',
                                    html: '<h4>{{ __('managemember.user_name') }}: ' + data[0]
                                        .username + '</br></h4>' +
                                        '<h4>{{ __('managemember.password') }}: ' + data[1] +
                                        '</br></h4>',
                                    type: 'success',
                                    confirmButtonText: '{{ __('main.yes') }}',
                                    confirmButtonClass: 'btn btn-confirm mt-2'
                                }).then(function() {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            })
        }

        function lock(member_id, user_id, member_name, status) {
            if (status == 1) {
                Swal.mixin({
                    confirmButtonText: '{{ __('main.yes') }} &rarr;',
                    showCancelButton: true,
                    cancelButtonText: '{{ __('main.cancel') }}',
                    progressSteps: ['1', '2']
                }).queue([{
                    title: 'ต้องการล็อค Member หรือไม่',
                }]).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            type: 'post',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route('managemember.memberlock') }}',
                            data: {
                                member_id: member_id,
                                user_id: user_id,
                                status: status
                            },
                            success: function(data) {
                                if (data != false) {
                                    Swal.fire({
                                        title: 'ล็อคการใช้งาน',
                                        html: '<h4>ชื่อผู้ใช้: ' + member_name + '</br></h4>' +
                                            '<h4>ถูกล็อคการใช้งานแล้ว</br></h4>',
                                        type: 'success',
                                        confirmButtonText: 'ตกลง',
                                        confirmButtonClass: 'btn btn-confirm mt-2'

                                    }).then(function() {
                                        location.reload();
                                    });

                                }
                            }
                        });
                    }
                })
            } else {
                Swal.mixin({
                    confirmButtonText: 'ยืนยัน &rarr;',
                    showCancelButton: true,
                    cancelButtonText: 'ยกเลิก',
                    progressSteps: ['1', '2']
                }).queue([{
                    title: 'ต้องการปลดล็อค Member หรือไม่',
                }]).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            type: 'post',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route('managemember.memberlock') }}',
                            data: {
                                member_id: member_id,
                                user_id: user_id,
                                status: status
                            },
                            success: function(data) {
                                if (data != false) {
                                    Swal.fire({
                                        title: 'ปลดล็อคการใช้งาน',
                                        html: '<h4>ชื่อผู้ใช้: ' + member_name + '</br></h4>' +
                                            '<h4>ถูกปลดล็อคการใช้งานแล้ว</br></h4>',
                                        type: 'success',
                                        confirmButtonText: 'ตกลง',
                                        confirmButtonClass: 'btn btn-confirm mt-2'

                                    }).then(function() {
                                        location.reload();
                                    });
                                }
                            }
                        });
                    }
                })
            }
        }

        function delete_member(member_id, user_id, member_name) {
            Swal.mixin({
                // input: 'text',
                confirmButtonText: 'ยืนยัน &rarr;',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                progressSteps: ['1', '2']
            }).queue([{
                title: 'ต้องการลบสมาชิก หรือไม่',
            }]).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route('managemember.memberdelete') }}',
                        data: {
                            member_id: member_id,
                            user_id: user_id
                        },
                        success: function(data) {
                            if (data != false) {
                                Swal.fire({
                                    title: 'ลบสมาชิกใช้งาน',
                                    html: '<h4>ชื่อผู้ใช้: ' + member_name + '</br></h4>' +
                                        '<h4>สมาชิกถูกลบแล้ว</br></h4>',
                                    type: 'success',
                                    confirmButtonText: 'ตกลง',
                                    confirmButtonClass: 'btn btn-confirm mt-2'
                                }).then(function() {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            })
        }

        @if (session('success'))
            Swal.fire({
                position: 'top-end',
                type: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            })
        @endif
    </script>

    <script>
        // สำหรับ Modal แสดง Wallet Address
        document.addEventListener('DOMContentLoaded', function() {
            $('#basic-datatable').on('click', '.btn-view-wallet', function() {
                let walletAddress = $(this).data('wallet-address');
                $('#wallet-address-display').text(walletAddress);
            });

            // สำหรับ Modal แก้ไข Bank Account
            $('#basic-datatable').on('click', '.btn-edit-bank', function() {
                let memberId = $(this).data('member-id');
                let walletAddress = $(this).data('wallet-address');

                $('#modal-member-id').val(memberId);
                $('#modal-wallet-address').val(walletAddress);
            });

            $('#basic-datatable').on('click', '.btn-edit-balance', function() {
                let memberId = $(this).data('member-id');
                let username = $(this).data('member-username');

                $('#modal-member-id').val(memberId);
                $('#modal-member-username').val(username);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // เมื่อคลิกปุ่ม Info ในตาราง
            $('#basic-datatable').on('click', '.btn-info-modal', function() {
                let memberData = $(this).data('member-data');

                // อัปเดตเนื้อหาใน Modal Body
                let htmlContent = `
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted">ข้อมูลทั่วไป</h6>
                            <hr class="mt-1 mb-2">
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="bx bx-user-circle"></i> <strong>User Code:</strong>
                            <p class="text-dark">${memberData.member_id}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="bx bx-user"></i> <strong>Full Name:</strong>
                            <p class="text-dark">${memberData.fullname}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted">ข้อมูลการติดต่อ</h6>
                            <hr class="mt-1 mb-2">
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="bx bx-envelope"></i> <strong>Email:</strong>
                            <p class="text-dark">${memberData.email}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="bx bx-phone"></i> <strong>เบอร์โทรศัพท์:</strong>
                            <p class="text-dark">${memberData.phone || 'N/A'}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted">รายละเอียดบัญชี</h6>
                            <hr class="mt-1 mb-2">
                        </div>
                        <div class="col-md-6 mb-2 d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-wallet"></i> <strong>Wallet Address:</strong>
                            </div>
                            <span>${memberData.wallet_address || 'N/A'}</span>
                            <button type="button" class="btn btn-primary btn-sm" style="width: 100px;"
                                data-toggle="modal"
                                data-target="#updateBankAccountModal"
                                data-member-id="${memberData.id}"
                                data-wallet-address="${memberData.wallet_address}"
                                onclick="$('#memberInfoModal').modal('hide');">
                                <i class="bx bx-edit-alt"></i> แก้ไข
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="bx bx-key"></i> <strong>Password:</strong>
                            <br>
                            <button type="button" class="btn btn-warning btn-sm mt-2"
                                data-toggle="modal"
                                data-target="#editPassModal"
                                data-member-id="${memberData.id}"
                                onclick="$('#memberInfoModal').modal('hide');">
                                <i class="bx bx-edit-alt"></i> แก้ไข Password
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <i class="bx bx-calendar"></i> <strong>Registration Date:</strong>
                            <p class="text-dark">${new Date(memberData.created_at).toLocaleString('th-TH')}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <i class="bx bx-check-circle"></i> <strong>Status:</strong>
                            <p class="${memberData.enable == 1 ? 'text-success' : 'text-danger'} font-weight-bold">
                                ${memberData.enable == 1 ? 'Active' : 'Locked'}
                            </p>
                        </div>
                    </div>
                </div>
            `;
                $('#member-info-content').html(htmlContent);

                // กำหนดข้อมูลและ Event Listener สำหรับปุ่มใน Footer
                let lockButton = $('#modal-lock-button');
                let lockUnlockText = $('#lock-unlock-text');
                let deleteButton = $('#modal-delete-button');

                // เปลี่ยนข้อความและสีปุ่ม Lock/Unlock ตามสถานะปัจจุบันของสมาชิก
                if (memberData.enable == 1) {
                    lockUnlockText.text('{{ __('managemember.lock') }}');
                    lockButton.removeClass('btn-secondary').addClass('btn-warning');
                } else {
                    lockUnlockText.text('{{ __('managemember.unlock') }}');
                    lockButton.removeClass('btn-warning').addClass('btn-secondary');
                }

                // กำหนดการทำงานของปุ่ม Lock/Unlock
                lockButton.off('click').on('click', function() {
                    lock(memberData.id, '{{ Auth::user()->id }}', memberData.username, memberData
                        .enable);
                });

                // กำหนดการทำงานของปุ่ม Delete
                deleteButton.off('click').on('click', function() {
                    delete_member(memberData.id, '{{ Auth::user()->id }}', memberData.username);
                });
            });

            // สำหรับ Modal แก้ไข Wallet Address
            $('#basic-datatable, #memberInfoModal').on('click', '[data-target="#updateBankAccountModal"]',
                function() {
                    let memberId = $(this).data('member-id');
                    let walletAddress = $(this).data('wallet-address');

                    $('#modal-wallet-member-id').val(memberId);
                    $('#modal-wallet-address').val(walletAddress);
                });

            // สำหรับ Modal แก้ไข Password
            $('#basic-datatable, #memberInfoModal').on('click', '[data-target="#editPassModal"]', function() {
                let memberId = $(this).data('member-id');
                $('#modal-pass-member-id').val(memberId);
            });

            // JavaScript สำหรับ Affiliate Modal
            // JavaScript สำหรับ Affiliate Modal
            // JavaScript สำหรับ Affiliate Modal
            $('#basic-datatable').on('click', '.btn-affiliate-modal', function() {
                const memberId = $(this).data('member-id');
                const container = $('#affiliate-tree-container');

                // แสดงสถานะ Loading
                container.html(
                    '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
                    );

                $.ajax({
                    url: `/api/members/${memberId}/affiliates`,
                    method: 'GET',
                    success: function(response) {
                        let htmlTree = `<ul class="tree-view">`;

                        // สมาชิกหลัก (Root Member)
                        htmlTree += `<li class="tree-node tree-root">
                            <div class="node-content">
                                <span class="node-icon bg-info text-white">M</span>
                                <span class="node-label"><strong>${response.member_username}</strong></span>
                            </div>`;

                        // ผู้ถูกแนะนำ (Children)
                        if (response.referred_users.length > 0) {
                            htmlTree += `<ul class="tree-children">`;
                            response.referred_users.forEach(user => {
                                htmlTree += `<li class="tree-node">
                                    <div class="node-content">
                                        <span class="node-icon bg-success text-white">L1</span>
                                        <span class="node-label">${user.username}</span>
                                    </div>
                                </li>`;
                            });
                            htmlTree += `</ul>`;
                        } else {
                            htmlTree +=
                                `<ul class="tree-children"><li class="no-affiliate">ไม่มีผู้ถูกแนะนำ</li></ul>`;
                        }

                        htmlTree += `</li></ul>`;
                        container.html(htmlTree);
                    },
                    error: function(xhr, status, error) {
                        container.html(
                            '<div class="alert alert-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล.</div>'
                            );
                        console.error(error);
                    }
                });
            });

        });
    </script>
@endsection
