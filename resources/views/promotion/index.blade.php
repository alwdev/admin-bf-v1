@extends('layouts.guest')

@section('styles')
    <style>
        .badge {
            padding: 10px;
            font-weight: 400;
        }
    </style>
    {{-- ตรวจสอบให้แน่ใจว่าได้รวมลิงก์ CSS ของ DataTables และ Bootstrap Table แล้ว --}}
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">{{ __('main.promotion') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">{{ __('main.promotion') }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="card-header" style="background: transparent;">
        {{-- หากคุณต้องการเปิดใช้งานปุ่ม "Add Promotion" ตามสิทธิ์การใช้งาน
             ให้ยกเลิกคอมเมนต์ส่วนนี้ และตรวจสอบว่า auth()->user()->permissions->manageuser ทำงานได้
             โดยการ cast 'permissions' ใน User Model เป็น array หรือ object
             ตัวอย่างใน User Model: protected $casts = ['permissions' => 'array']; --}}
        {{-- @if (auth()->user() && isset(auth()->user()->permissions['manageuser']) && auth()->user()->permissions['manageuser'] > 2) --}}
        <a type="button" class="btn btn-primary btn-gold waves-effect waves-light"
            href="{{ route('promotion.create') }}">{{ __('main.Add Promotion') }}</a>
        {{-- @endif --}}
    </div>
    <div class="row">
        <div class="col-12 card">
            <div class="card-body">
                <h4 class="card-title"></h4>
                <p class="card-subtitle mb-4">
                </p>

                {{-- ปรับ table id จาก basic-datatable เป็น promotions-table เพื่อความเฉพาะเจาะจง --}}
                <table id="promotions-table" class="table nowrap" data-filter-control="true" data-toggle="table"
                    data-search="true" data-show-export="false" data-click-to-select="false" data-pagination="true"
                    data-url=""> {{-- data-url="" จะถูกละเว้นเนื่องจากข้อมูลถูกส่งมาจาก Blade โดยตรง --}}
                    <thead class="table-light">
                        <tr>
                            <th data-field="name" data-sortable="true">{{ __('main.promotion') }}</th>
                            <th data-field="deposit" data-sortable="true">{{ __('dashboard.deposit_amount') }}</th>
                            <th data-field="bonus" data-sortable="true">โบนัส</th>
                            <th data-field="turnover" data-sortable="true">เทิร์นโอเวอร์</th>
                            <th data-field="withdraw_limit" data-sortable="true">ถอนได้สูงสุด</th>
                            <th data-field="is_newuser" data-sortable="true">{{ __('main.For new players only') }}</th>
                            <th data-field="first_deposit_bonus" data-sortable="true">ยอดฝากครั้งแรก</th>
                            <th data-field="recurring_promotion" data-sortable="true">โปรต่อเนื่อง</th>
                            <th data-field="applicable_games" data-sortable="true">ใช้กับเกม</th>
                            <th data-field="action" data-sortable="true">{{ __('main.Publish') }}</th>
                            <th data-sortable="true">{{ __('main.Creation Date') }}</th>
                            {{-- หากคุณต้องการเปิดใช้งานปุ่ม แก้ไข/ลบ ตามสิทธิ์การใช้งาน --}}
                            {{-- @if (auth()->user() && isset(auth()->user()->permissions['transfer']) && auth()->user()->permissions['transfer'] > 2) --}}
                            <th data-sortable="true"></th>
                            {{-- @endif --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promotions as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->deposit }}</td>
                                {{-- แสดง Bonus ตาม is_percentage_based --}}
                                <td>
                                    @if ($item->is_percentage_based)
                                        {{ $item->bonus_percentage }}
                                    @else
                                        {{ $item->bonus }}
                                    @endif
                                </td>
                                {{-- แสดง Turnover ตาม is_percentage_based --}}
                                <td>
                                    @if ($item->is_percentage_based)
                                        {{ $item->turnover_percentage }}
                                    @else
                                        {{ $item->turnover }}
                                    @endif
                                </td>
                                {{-- แสดง Withdraw Limit ตาม is_percentage_based --}}
                                <td>
                                    @if ($item->is_percentage_based)
                                        {{ $item->withdraw_limit_percentage }}
                                    @else
                                        {{ $item->withdraw_limit }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_newuser)
                                        {{-- ตรวจสอบเป็น boolean โดยตรง --}}
                                        <span class="badge badge-success">{{ __('main.yes') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('main.no') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_first_deposit_bonus)
                                        {{-- ตรวจสอบเป็น boolean โดยตรง --}}
                                        <span class="badge badge-info">ใช่</span>
                                    @else
                                        <span class="badge badge-secondary">ไม่ใช่</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_recurring_promotion)
                                        {{-- ตรวจสอบเป็น boolean โดยตรง --}}
                                        <span class="badge badge-primary">
                                            {{ __('main.yes') }}
                                            @if ($item->recurring_promotion_days > 0)
                                                ({{ $item->recurring_promotion_days }} วัน
                                                @if ($item->recurring_bonus_percentage)
                                                    , โบนัส {{ $item->recurring_bonus_percentage }}
                                                @endif)
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('main.no') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($item->applicable_games))
                                        @foreach ($item->applicable_games as $game)
                                            <span class="badge bg-light">{{ $game }}</span>
                                        @endforeach
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->enable)
                                        {{-- ตรวจสอบเป็น boolean โดยตรง --}}
                                        <span class="badge badge-success">{{ __('main.Publish') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('main.Not published') }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                {{-- หากคุณต้องการเปิดใช้งานปุ่ม แก้ไข/ลบ ตามสิทธิ์การใช้งาน --}}
                                {{-- @if (auth()->user() && isset(auth()->user()->permissions['transfer']) && auth()->user()->permissions['transfer'] > 2) --}}
                                <td class="text-right">
                                    <a href="{{ route('promotion.edit', $item->id) }}" type="button"
                                        class="btn btn-warning btn-sm waves-effect waves-light" style="width: 80px;"><i
                                            class="bx bx-edit-alt"></i> {{ __('main.edit') }}</a>
                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light"
                                        onclick="confirmDelete('#formdel{{ $item->id }}')" style="width: 80px;"><i
                                            class="bx bx-trash"></i> {{ __('managemember.delete') }}</button>
                                    <form method="post" action="{{ route('promotion.destroy') }}"
                                        id="formdel{{ $item->id }}" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                    </form>
                                </td>
                                {{-- @endif --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    {{-- ตรวจสอบให้แน่ใจว่าได้รวมไฟล์ JavaScript ที่จำเป็นทั้งหมดแล้ว --}}
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    {{-- หากคุณไม่ใช้ Ionicons สามารถคอมเมนต์หรือลบออกได้ --}}
    {{-- <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script> --}}
    {{-- <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- เพิ่ม SweetAlert2 CDN --}}

    <script>
        $(document).ready(function() {
            // Initialise DataTables
            $('#promotions-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Thai.json" // ภาษาไทย
                },
                "responsive": true, // ทำให้ตาราง responsive
                "autoWidth": false, // ปิด autoWidth เพื่อให้ responsive ทำงานได้ดีขึ้น
                // เพิ่มเติม options ที่คุณต้องการ:
                // "order": [[ 0, "asc" ]], // จัดเรียงคอลัมน์แรกตามลำดับขึ้น
                // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] // กำหนดจำนวนรายการต่อหน้า
            });
        });

        // ส่วนของ SweetAlert2 และ Function อื่นๆ ยังคงเดิม
        @if (session('status'))
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '{{ session('status') }}',
                showConfirmButton: false,
                timer: 1500
            })
        @endif
        @if (session('del_status'))
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '{{ session('del_status') }}',
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

        function confirmDelete(formId) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: 'คุณต้องการลบโปรโมชั่นนี้ใช่หรือไม่? การดำเนินการนี้ไม่สามารถย้อนกลับได้!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                confirmButtonClass: 'btn btn-danger mt-2',
                cancelButtonClass: 'btn btn-secondary ml-2 mt-2',
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    $(formId).submit();
                }
            });
        }
    </script>
@endsection
