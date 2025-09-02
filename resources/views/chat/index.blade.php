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
                <h4 class="mb-0 font-size-18">Chat</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Chat</li>
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
                            <th data-field="id" data-sortable="true">chat id</th>
                            <th data-field="member" data-sortable="true" data-filter-control="input">member</th>
                            <th data-field="chat" data-sortable="false">Open chat</th>
                            <th data-field="updated_at" data-sortable="true">Last</th>
                            <th data-field="lastAt" data-sortable="true">Time</th>
                            <th>Chat count</th>
                            <th data-field="action" data-sortable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($convs as $item)
                            @if ($item->messages->count() > 0)
                                @php
                                    $lastAt =
                                        $item->latestMessage?->created_at ??
                                        ($item->last_message_at ? Carbon::parse($item->last_message_at) : null);

                                    $ageMin = $lastAt ? now()->diffInSeconds($lastAt) / 60 : null;

                                    $bucket = $ageMin !== null ? max(1, min(10, (int) ceil($ageMin))) : null;

                                    $alpha = $bucket !== null ? round(0.55 - ($bucket - 1) * (0.43 / 9), 2) : 0;
                                    $rowStyle =
                                        $lastAt && $ageMin <= 10 ? "background-color: rgba(220,53,69, {$alpha});" : '';
                                @endphp
                                <tr style="{{ $rowStyle }}">
                                    <td>หมายเลขห้อง #{{ $item->id }}</td>
                                    <td>
                                        @php

                                            $member = App\Models\Members::where('id', $item->members[0]->id)->first();
                                        @endphp
                                        <button type="button" class="btn btn-info btn-sm btn-info-modal"
                                            data-toggle="modal" data-target="#memberInfoModal"
                                            data-member-data="{{ json_encode($member) }}">
                                            <i class="bx bx-info-circle"></i> Info
                                        </button>
                                        @if ($item->members->count() > 0)
                                            {{ $item->members[0]->username }}
                                        @else
                                            No members
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('chat.show', $item->id) }}" class="btn btn-primary btn-sm">
                                            Open chat
                                        </a>
                                    <td>
                                        @if ($item->messages->count() > 0)
                                            {{ $item->messages->last()->updated_at->diffForHumans() }}
                                        @else
                                            No messages
                                        @endif
                                    </td>
                                    <td>{{ $lastAt }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $item->messages->count() }}</span>
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm btn-end-chat"
                                            data-toggle="modal" data-target="#confirmCloseModal"
                                            data-action="{{ route('chat.conversations.close', $item->id) }}"
                                            data-thread="{{ $item->id }}" data-title="{{ $item->title ?? '' }}">
                                            จบการสนทนา
                                        </button>
                                    </td>

                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
    </div>
    <!-- end row-->
    <div class="modal fade" id="confirmCloseModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="closeConvForm" method="POST" action="">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            ยืนยันปิดการสนทนา
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        ต้องการจบการสนทนา <strong>#<span id="modal-thread-id"></span></strong>
                        <span id="modal-thread-title" class="text-muted"></span> ใช่?
                        <div class="small text-muted mt-2">
                            * เมื่อยืนยัน ห้องจะถูกปิดและไม่สามารถส่งข้อความต่อได้
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-danger" id="btnConfirmClose">
                            จบการสนทนา
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="memberInfoModal" tabindex="-1" aria-labelledby="memberInfoModalLabel" aria-hidden="true">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
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
        document.addEventListener('DOMContentLoaded', function() {

            // ตอนเปิด modal: ดึงค่า data-* จากปุ่มที่กดมาใส่ในฟอร์ม
            $('#confirmCloseModal').on('show.bs.modal', function(e) {
                const btn = $(e.relatedTarget);
                const act = btn.data('action');
                const thId = btn.data('thread');
                const title = btn.data('title') || '';

                $('#closeConvForm').attr('action', act);
                $('#modal-thread-id').text(thId);
                $('#modal-thread-title').text(title ? ` (${title})` : '');
            });

            // กันกดซ้ำระหว่าง submit
            $('#closeConvForm').on('submit', function() {
                const $btn = $('#btnConfirmClose');
                $btn.prop('disabled', true).text('กำลังดำเนินการ...');
            });

        });
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
