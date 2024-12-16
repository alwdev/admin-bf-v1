@extends('layouts.guest')
@section('styles')
{{-- <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" /> --}}

<link href="{{asset('daterangepicker/daterangepicker.css')}}" rel="stylesheet">

@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">รายงาน</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">รายงานการเล่น สมาชิก</li>
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
                {{-- <select id="date_picker" name="date_picker" class="form-control">
                    <option value="0">Today</option>
                    <option value="1">Yesterday</option>
                    <option value="2">Last 7 days</option>
                    <option value="3">Last 30 days</option>
                </select> --}}
                <input type="text" name="date_picker" class="form-control form-select" id="date_picker" style="font-size:15px;">

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
                                    <th data-field="username" data-filter-control="select">ชื่อสมาชิก=>กดดูรายละเอียด</th>
                                    <th>ยอดเดิมพัน</th>
                                    <th>ยอดได้เสีย</th>
                                    <th>วันที่/เวลา</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($member_play as $member)
                                <tr>
                                    <td>
                                        <a href="{{ route('report.memberplay_name', ['username' => $member->playerUsername,'date_id' => $date_id]) }}" class="" target="_blank">
                                        {{ $member->playerUsername }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ number_format((float) $member->amount,2) }}</td>
                                    <td class="text-center">{{ number_format((float) ($member->winlose),2) }}</td>
                                    <td class="text-center">{{ $date1 }} - {{ $date2 }}</td>
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

    <script src="{{asset('moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
{{-- <script type="text/javascript">
let date1 = document.getElementById('txt_date1').value ;
    $('#date_picker').on('change', function(){
        var idDate = this.value;
        console.log(idDate);
        window.location.href ="/list_memberplay/"+idDate;
        this.value = idDate;
    })
    $(document).ready(function() {
        document.getElementById('date_picker').value = date1;
    });
</script> --}}
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
    <script>

        function copy(e) {
            const input = e.parentElement.parentElement.querySelector('input')
            navigator.clipboard.writeText(input.value);
            e.innerHTML = 'คัดลอกแล้ว';
            setTimeout(() => {
                e.innerHTML = 'คัดลอก';
            }, 1000)
        }

        let sdate = moment();
        let edate = moment();
        @if(!is_null($date))
        // alert();
            sdate = moment('{{ $date }}');
            edate = moment('{{ $date_end }}');
        @endif
        $('#date_picker').daterangepicker(
            {
                ranges   : {
                "วันนี้"      : [moment(), moment()],
                "เมื่อวาน"  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                "7 วันที่แล้ว": [moment().subtract(6, 'days'), moment()],
                "30 วันที่แล้ว" : [moment().subtract(29, 'days'), moment()],
                "เดือนนี้" : [moment().startOf('month'), moment().endOf('month')],
                "เดือนที่แล้ว" : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                "ปีนี้" : [moment().startOf('year'), moment().endOf('year')],
                },
                startDate: sdate,
                endDate  : edate
            },
            function (start, end) {

                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
            }
        );
        $('.ranges > ul > li:last')[0].textContent = "กำหนดเอง";
        $('#date_picker').change(function(){
            var date__ = $('#date_picker').val();
            console.log(date__);
            window.location.href = '/list_memberplay/'+moment(date__.split('-')[0]).format('YYYY-MM-DD')+'/'+moment(date__.split('-')[1]).format('YYYY-MM-DD');
        })
    </script>
@endsection
