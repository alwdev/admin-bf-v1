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
            <h4 class="mb-0 font-size-18">รายงานการฝาก-ถอน</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">รายงานการฝาก-ถอน</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card-body">
            <div class="row">
                <div class="col-2 text-right">
                <input type="hidden" id="txt_date1" value="{{ $date_id }}">
                <select id="date_picker" name="date_picker" class="form-control">
                    <option value="0">Today</option>
                    <option value="1">Yesterday</option>
                    <option value="2">Last 7 days</option>
                    <option value="3">Last 30 days</option>
                </select>
                </div>
            </div>
            <h4 class="card-title"></h4>
            <p class="card-subtitle mb-4">
            </p>
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
                                    <th data-field="username" data-filter-control="select" data-sortable="true">ชื่อผู้ใช้</th>
                                    <th data-sortable="true">ยอดเงิน</th>
                                    <th data-field="" data-filter-control="select" data-sortable="true">ประเภท</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfers as $item)
                                <tr>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ number_format($item->amount, 2) }}</td>
                                    <td>
                                        @if($item->type == 'deposit')
                                        ฝาก
                                        @elseif ($item->type == 'withdraw')
                                            ถอน
                                        @else
                                            {{$item->type}}
                                        @endif
                                    </td>
                                    <td>
                                    </td>
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
    let date1 = document.getElementById('txt_date1').value ;
    $('#date_picker').on('change', function(){
        var idDate = this.value;
        console.log(idDate);
        window.location.href ="/member_transfer/"+idDate;
        this.value = idDate;
    })
    $(document).ready(function() {
        document.getElementById('date_picker').value = date1;
    });
    </script>
@endsection
