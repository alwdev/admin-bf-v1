@extends('layouts.guest') @section('content')

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <style>
        .treeview ul {
            list-style: none;
            padding-left: 32px;
        }

        .treeview ul li {
            padding: 50px 0px 0px 35px;
            position: relative;
        }

        .treeview ul li:before {
            content: "";
            position: absolute;
            top: -26px;
            left: -31px;
            border-left: 2px dashed #a2a5b5;
            width: 1px;
            height: 100%;
        }

        .treeview ul li:after {
            content: "";
            position: absolute;
            border-top: 2px dashed #a2a5b5;
            top: 70px;
            left: -30px;
            width: 65px;
        }

        .treeview>ul>li:after,
        .treeview>ul>li:last-child:before {
            content: unset;
        }

        .treeview>ul>li:before {
            top: 90px;
            left: 36px;
        }

        .treeview>ul>li>.treeview__level:before {
            height: 60px;
            width: 60px;
            top: -9.5px;
            background-color: #54a6d9;
            border: 7.5px solid #d5e9f6;
            font-size: 22px;
        }

        .treeview__level {
            padding: 7px;
            padding-left: 42.5px;
            display: inline-block;
            border-radius: 5px;
            font-weight: 700;
            border: 1px solid #e3e5ef;
            position: relative;
            z-index: 1;
        }

        .level-title {
            overflow-x: auto;
            white-space: nowrap;
        }

        .treeview__level:before {
            content: attr(data-level);
            position: absolute;
            left: -27.5px;
            top: -6.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 55px;
            width: 55px;
            border-radius: 50%;
            border: 7.5px solid #eef6d5;
            background-color: #bada55;
            color: #fff;
            font-size: 12px;
        }

        .treeview__level1:before {
            background-color: #3f0abb;
            border: 7.5px solid #8d54eb;
        }

        .treeview__level2:before {
            background-color: #991807;
            border: 7.5px solid #eb5454;
        }

        .treeview__level3:before {
            background-color: #126452;
            border: 7.5px solid #54eb98;
        }

        .treeview__level .level-title {
            cursor: pointer;
            user-select: none;
        }

        .treeview .level-title:hover {
            text-decoration: underline;
        }
    </style>
    <style>
        /* ... โค้ด CSS เดิมของคุณ ... */

        /* Master Level text color */
        .treeview__level[data-level="M"] .level-title {
            color: #54a6d9;
            /* สีเดียวกับวงกลม M */
        }

        /* Level 1 text color */
        .treeview__level1 .level-title {
            color: #3f0abb;
            /* สีเดียวกับวงกลม Level 1 */
        }

        /* Level 2 text color */
        .treeview__level2 .level-title {
            color: #991807;
            /* สีเดียวกับวงกลม Level 2 */
        }

        /* Level 3 text color */
        .treeview__level3 .level-title {
            color: #126452;
            /* สีเดียวกับวงกลม Level 3 */
        }

        /* ปรับปรุง hover effect ให้ดูดีขึ้น */
        .treeview__level .level-title:hover {
            text-decoration: underline;
            /* คุณอาจต้องการทำให้สีเข้มขึ้นเล็กน้อยเมื่อ hover */
            /* color: darken(original_color, 10%); */
            /* ถ้าใช้ SASS/LESS */
            /* ตัวอย่างถ้าเป็น Level 1 */
            /* .treeview__level1 .level-title:hover { color: #2a0785; } */
        }

        /* ... โค้ด CSS อื่นๆ ที่คุณมีอยู่แล้ว ... */
    </style>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">โครงสร้าง Affiliate ของ {{ $affiliateData['master']['username'] }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('managemember.index') }}">Member List</a></li>
                        <li class="breadcrumb-item active">Affiliate Tree</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="nftmax-table mg-top-40">
                        <div class="treeview">
                            <ul>
                                <li>
                                    <div class="treeview__level" data-level="M">
                                        <a href="{{ route('managemember.affiliates', ['id' => $affiliateData['master']['id']]) }}"
                                            class="level-title">
                                            Master ({{ $affiliateData['master']['username'] }})
                                        </a>
                                    </div>
                                    @if (count($affiliateData['levels']) > 0)
                                        <ul>
                                            @foreach ($affiliateData['levels'] as $level1)
                                                <li>
                                                    <div class="treeview__level treeview__level1" data-level="Level 1">
                                                        <a href="{{ route('managemember.affiliates', ['id' => $level1['id']]) }}"
                                                            class="level-title">
                                                            {{ $level1['username'] }}
                                                        </a>
                                                    </div>
                                                    @if (count($level1['children']) > 0)
                                                        <ul>
                                                            @foreach ($level1['children'] as $level2)
                                                                <li>
                                                                    <div class="treeview__level treeview__level2"
                                                                        data-level="Level 2">
                                                                        <a href="{{ route('managemember.affiliates', ['id' => $level2['id']]) }}"
                                                                            class="level-title">
                                                                            {{ $level2['username'] }}
                                                                        </a>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="p-3 text-center text-muted">ไม่มีผู้ถูกแนะนำ</div>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h5 class="text-truncate font-size-14 mb-2">ยอดรวมค่าคอมมิชชั่นทั้งหมด</h5>
                            <h4 class="mb-2">{{ number_format($totalCommission, 2) }} ฿</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="bx bx-dollar-circle font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h5 class="text-truncate font-size-14 mb-2">ค่าคอมมิชชั่นที่รับแล้ว</h5>
                            <h4 class="mb-2">{{ number_format($paidCommission, 2) }} ฿</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="bx bx-check-circle font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="nftmax-table mg-top-40">
                        <div class="treeview">
                        </div>

                        <h5 class="mt-5">ประวัติค่าคอมมิชชั่น</h5>
                        <table id="commissionTable" class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>วันที่</th>
                                    <th class="text-right">จำนวนเงิน</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($commissions->count() > 0)
                                    @foreach ($commissions as $index => $commission)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($commission->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="text-right">{{ number_format($commission->amount, 2) }}</td>
                                            <td>
                                                @if ($commission->status == 1)
                                                    <span class="text-warning">รอดำเนินการ</span>
                                                @elseif($commission->status == 2)
                                                    <span class="text-success">สำเร็จ</span>
                                                @elseif($commission->status == 3)
                                                    <span class="text-danger">ถูกปฏิเสธ</span>
                                                @else
                                                    <span>ไม่ทราบสถานะ</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">ไม่พบข้อมูลค่าคอมมิชชั่น</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@section('scripts')
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
$(document).ready(function() {
    $('#commissionTable').DataTable({
        "language": {
            "sProcessing": "กำลังดำเนินการ...",
            "sLengthMenu": "แสดง _MENU_ รายการ",
            "sZeroRecords": "ไม่พบข้อมูล",
            "sEmptyTable": "ไม่พบข้อมูลในตาราง",
            "sInfo": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "sInfoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "sInfoFiltered": "(กรองข้อมูล _MAX_ ทุกรายการ)",
            "sInfoPostFix": "",
            "sSearch": "ค้นหา:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "กำลังโหลดข้อมูล...",
            "oPaginate": {
                "sFirst": "หน้าแรก",
                "sLast": "หน้าสุดท้าย",
                "sNext": "ถัดไป",
                "sPrevious": "ก่อนหน้า"
            },
            "oAria": {
                "sSortAscending": ": เรียงจากน้อยไปมาก",
                "sSortDescending": ": เรียงจากมากไปน้อย"
            }
        }
    });
});
</script>
@endsection
