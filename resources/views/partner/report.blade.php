@extends('layouts.guest')
@section('styles')
    {{-- <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" /> --}}
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">{{ __('main.partners') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                        <li class="breadcrumb-item active"></li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card-body">
                <h4 class="card-title"></h4>
                <p class="card-subtitle mb-4"></p>
                <div class="card">
                    <div class="card-body p-0">
                        <div>
                            <table id="table" class="table m-10 table-bordered" data-filter-control="true"
                                data-toggle="table" data-search="true" data-show-export="false" data-click-to-select="false"
                                data-pagination="true" data-url="">
                                <thead>
                                    <tr class="text-center">
                                        <th>{{ __('dashboard.member') }}</th>
                                        <th data-sortable="true">{{ __('dashboard.total_bet') }}</th>
                                        <th data-sortable="true">{{ __('dashboard.winlose') }}</th>
                                        <th data-sortable="true">{{ __('dashboard.commission_rate') }}</th>
                                        <th data-sortable="true">{{ __('dashboard.commission') }}</th>
                                        <th data-sortable="true">{{ __('dashboard.date_range') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- โหลดข้อมูลด้วย JavaScript --}}
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            กำลังโหลดข้อมูล...
                                            {{-- เพิ่ม spinner (ถ้ามีใน theme ของคุณ) --}}
                                            {{-- <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div> --}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    <script
        src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            // โหลดข้อมูลด้วย AJAX
            $.ajax({
                url: "{{ route('partner.member.winloss.data') }}",
                method: 'GET',
                beforeSend: function() {
                    // แสดง Loading Spinner ก่อนเริ่มการโหลด
                    let tableBody = $('#table tbody');
                    tableBody.empty();
                    tableBody.append(`<tr>
                                        <td colspan="6" class="text-center">
                                            กำลังโหลดข้อมูล...
                                            <div class="spinner-border text-primary spinner-border-sm" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </td>
                                      </tr>`);
                },
                success: function(response) {
                    console.log(response);
                    let tableBody = $('#table tbody');
                    tableBody.empty(); // เคลียร์ loading spinner
                    if (response.length > 0) {
                        $.each(response, function(index, member) {
                            let dateRange =
                                `${moment(member.date1).format('DD/MM/YYYY')} - ${moment(member.date2).format('DD/MM/YYYY')}`;

                            let winloseColorClass = member.winlose < 0 ? 'text-danger' :
                                'text-success';

                            // Format the commission to 2 decimal places
                            let formattedCommission = parseFloat(member.total_commission)
                                .toFixed(2);

                            let row = `<tr>
                            <td>${member.member_username}</td>
                            <td class="text-center">${parseInt(member.total_bet)}</td>
                            <td class="text-center ${winloseColorClass}">${member.winlose}</td>
                            <td class="text-center">${member.rate}</td>
                            <td class="text-center">${formattedCommission}</td>
                            <td class="text-center">${dateRange}</td>
                        </tr>`;
                            tableBody.append(row);
                        });
                    } else {
                        tableBody.append(
                            '<tr><td colspan="6" class="text-center">ไม่พบข้อมูล</td></tr>');
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching data:', xhr.responseText);
                    let tableBody = $('#table tbody');
                    tableBody.empty();
                    tableBody.append(
                        '<tr><td colspan="6" class="text-center">เกิดข้อผิดพลาดในการดึงข้อมูล</td></tr>'
                        );
                }
            });
        });
    </script>
@endsection
