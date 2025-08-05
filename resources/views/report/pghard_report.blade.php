@extends('layouts.guest')
@section('styles')
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">รายงาน PG Hard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">รายงาน PG Hard</li>
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
                        <table id="table" class="table m-10 table-bordered"
                        data-filter-control="true"
                        data-toggle="table"
                        data-search="true"
                        data-show-export="false"
                        data-click-to-select="false"
                        data-pagination="true"
                        data-url="">
                            <thead>
                                <tr class="text-center">
                                    <th>ชื่อผู้ใช้</th>
                                    <th data-sortable="true">totalAmount</th>
                                    <th data-sortable="true">totalPayoff</th>
                                    <th data-sortable="true">totalSuccessSpin</th>
                                    <th data-sortable="true">totalSuccessMainSpin</th>
                                    {{-- <th data-sortable="true">วันที่</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report as $row)
                                <tr>
                                    <td>{{ $row['username'] }}</td>
                                    <td class="text-center">{{ intval($row['totalAmount']) }}</td>
                                    <td class="text-center">{{ intval($row['totalPayoff']) }}</td>
                                    <td class="text-center">{{ intval($row['totalSuccessSpin']) }}</td>
                                    <td class="text-center">{{ intval($row['totalSuccessMainSpin']) }}</td>
                                    {{-- <td class="text-center">{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s') }}</td> --}}
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
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
@endsection
