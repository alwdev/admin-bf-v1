@extends('layouts.guest')
@section('styles')
<link href="{{asset('daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@section('content')
  <!-- start page title -->
  <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            {{-- <h4 class="mb-0 font-size-18">Dashboard</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div> --}}

        </div>
    </div>
</div>
<!-- end page title -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-animate">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-0 font-size-18">ประวัติการโอนเงิน</h4>
                        <br>
                        <p>คุณสามารถตรวจสอบประวัติการโอนเงินคืนของคุณ ที่ผ่านมาได้ โดยสามารถเลือกแสดงรายการได้ตามช่วงวันเวลาที่ต้องการตรวจสอบข้อมูล</p>
                        <p style="text-decoration: underline;color:red;font-size:16px;">หากวันโอนเงินตรงกับวันหยุดทำการของบริษัทหรือธนาคาร บริษัทจะทำการโอนเงินให้ร้านค้าในสองวันทำการถัดไป</p>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="date_range">MerchaneID</label>
                                    <input type="text" class="form-control" id="date_range" name="date_range" value="{{ old('date_range')?? '' }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="date_range">เลือกช่วงวันเวลา</label>
                                    <input type="text" name="date_picker" class="form-control form-select" id="date_picker" style="font-size:15px;">
                                </div>
                            </div>
                            <div class="col-5">
                                <button type="submit" class="btn btn-primary">ตรวจสอบ</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
         <div class="card card-animate" >
            <div class="card-body">

                <div class="header">
                    <h4> สรุปยอด ยอดรวมรายได้ <br>
                         <small style="font-size:12px;color:gray; ">
                            2025-01-31 21:00:00 - 2025-02-07
                            22:56:02 </small></h4>
                </div>

                <div class="table-responsive">
                    <table class="js-exportable-moneytransfer table table-bordered table-striped dataTable no-footer" id="tableMoneyTransfer" role="grid">
                        <thead>
                            <tr role="row"><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 446.094px;">รายได้ระหว่างวันที่</th><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 219.047px;">วันที่โอนเงิน</th><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 268.75px;">ยอดโอนเงิน</th><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 184.141px;">สถานะ</th><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 252.969px;">รายละเอียด</th></tr>
                        </thead>
                        <tbody>

                            <tr role="row" class="odd">
                                <td>04/01/2025 -
                                    11/01/2025</td>
                                <td>15/01/2025</td>
                                <td>12.86 บาท</td>
                                <td>Pending</td>
                                <td class="text-center">
                                    <form method="get" action="#">
                                        <input type="hidden" name="startDate" value="2025-01-04">
                                        <input type="hidden" name="startTime" value="21:00:00">
                                        <input type="hidden" name="endDate" value="2025-01-11">
                                        <input type="hidden" name="endTime" value="20:59:59">
                                        <input type="hidden" name="childMerchant" value="48224">
                                        <input class="btn btn-raised btn-xs btn-primary waves-effect" type="submit" value="view" fdprocessedid="158l7c">
                                        <input name="AntiforgeryFieldname" type="hidden" value="-gH8vwKPSAFK0bMqsvY"></form>
          
                                </td>
                            </tr><tr role="row" class="even">
                                <td>28/12/2024 -
                                    04/01/2025</td>
                                <td>08/01/2025</td>
                                <td>3,021.38
                                    บาท</td>
                                <td>Pending</td>
                                <td class="text-center">
                                    <form method="post" action="#">
                                        <input type="hidden" name="startDate" value="2024-12-28">
                                        <input type="hidden" name="startTime" value="21:00:00">
                                        <input type="hidden" name="endDate" value="2025-01-04">
                                        <input type="hidden" name="endTime" value="20:59:59">
                                        <input type="hidden" name="childMerchant" value="48224">
                                        <input class="btn btn-raised btn-xs btn-primary waves-effect" type="submit" value="view" fdprocessedid="j0f7x">
                                        <input name="AntiforgeryFieldname" type="hidden" ></form>
                                        <button data-startdate="2024-11-02 21:00:00" data-enddate="2024-11-09 20:59:59" data-transferdate="2024-11-13 00:00:00" data-merchantid="48224" class="btn btn-raised btn-xs btn-info waves-effect sendMailBTN mt-2" fdprocessedid="f1bwxk">
                                            Send Mail
                                        </button>
                                </td>
                            </tr><tr role="row" class="odd">
                                <td>16/11/2024 -
                                    23/11/2024</td>
                                <td>27/11/2024</td>
                                <td>19.44 บาท</td>
                                <td>Pending</td>
                                <td class="text-center">
                                    <form method="post" action="#">
                                        <input type="hidden" name="startDate" value="2024-11-16">
                                        <input type="hidden" name="startTime" value="21:00:00">
                                        <input type="hidden" name="endDate" value="2024-11-23">
                                        <input type="hidden" name="endTime" value="20:59:59">
                                        <input type="hidden" name="childMerchant" value="48224">
                                        <input class="btn btn-raised btn-xs btn-primary waves-effect" type="submit" value="view" fdprocessedid="121fp">
                                        <input name="AntiforgeryFieldname" type="hidden" ></form>
                                        <button data-startdate="2024-11-02 21:00:00" data-enddate="2024-11-09 20:59:59" data-transferdate="2024-11-13 00:00:00" data-merchantid="48224" class="btn btn-raised btn-xs btn-info waves-effect sendMailBTN mt-2" fdprocessedid="f1bwxk">
                                            Send Mail
                                        </button>
                                    </button>
                                </td>
                            </tr><tr role="row" class="even">
                                <td>02/11/2024 -
                                    09/11/2024</td>
                                <td>13/11/2024</td>
                                <td>38,887.20
                                    บาท</td>
                                <td>Complete</td>
                                <td class="text-center">
                                    <form method="post" action="#">
                                        <input type="hidden" name="startDate" value="2024-11-02">
                                        <input type="hidden" name="startTime" value="21:00:00">
                                        <input type="hidden" name="endDate" value="2024-11-09">
                                        <input type="hidden" name="endTime" value="20:59:59">
                                        <input type="hidden" name="childMerchant" value="48224">
                                        <input class="btn btn-raised btn-xs btn-primary waves-effect" type="submit" value="view" fdprocessedid="4muu8m">
                                        <input name="AntiforgeryFieldname" type="hidden" ></form>
                                        <button data-startdate="2024-11-02 21:00:00" data-enddate="2024-11-09 20:59:59" data-transferdate="2024-11-13 00:00:00" data-merchantid="48224" class="btn btn-raised btn-xs btn-info waves-effect sendMailBTN mt-2" fdprocessedid="f1bwxk">
                                            Send Mail
                                        </button>
                                </td>
                            </tr></tbody>
                    </table>
                </div>

                <div class="col-lg-4">
                    <h4>Status Meaning  </h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><h4 class="col-orange">Status</h4></th>
                                <th><h4 class="col-orange">Meaning</h4></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b class="col-black">Pending</b></td>
                                <td><p class="col-black">รอโอนเงิน</p></td>
                            </tr>
                            <tr>
                                <td><b class="col-black">No
                                        Order</b></td>
                                <td><p class="col-black">ไม่มีรายการโอนเงิน</p></td>
                            </tr>
                            <tr>
                                <td><b class="col-black">Complete</b></td>
                                <td><p class="col-black">โอนเงินเรียบร้อย</p></td>
                            </tr>
                        </tbody>
                    </table>
                </div> <!-- end col -->
            </div>
        </div>
    </div> <!-- end col -->




</div>
<form action="{{ route('dashboard_date') }}" id="form_dashboard" method="get">
    @csrf
    <input type="hidden" name="date_" id="date_">
</form>
<!-- end row-->
@endsection
@section('scripts')
<link rel="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
{{-- <script>
        const backgroundColor = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
      ];

    const time_ = [];
    const count_ = [];
    @foreach ($players as $key => $player)
    time_.push("{{ $player[0]->hour.':00' }}");
        count_.push("{{ Count($player) }}");
    @endforeach
    let ctx = document.getElementById("line-chart").getContext("2d");
    let myChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: time_,
            datasets: [
                {
                    label: "Online players",
                    data: count_,
                    backgroundColor: "rgba(153,205,1,0.6)",
                },

            ],
        },
    });



    const game_ = [];
    const gamecount_ = [];
    @foreach ($topgame as $key => $game)
        game_.push("{!! $game->gameName !!}");
        gamecount_.push("{{ $game->count }}");
    @endforeach
    $(document).ready(function() {
        var ctxD = $("#donut-chart");
        var myLineChart = new Chart(ctxD, {
            type: 'doughnut',
            data: {
                labels: game_,
                datasets: [{
                    data: gamecount_,
                    backgroundColor: backgroundColor
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Games'
                },
                legend: {
                    display: false
                }

            }
        });
    });
</script> --}}
<script src="{{asset('moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    function formatDate(date = new Date()) {
      const year = date.toLocaleString('default', {year: 'numeric'});
      const month = date.toLocaleString('default', {
        month: 'short',
      });
      const day = date.toLocaleString('default', {day: '2-digit'});

      return [day, month, year].join(' ');
    }
    let sdate = moment()
    let edate = moment();
    @if(!is_null(request()->get('date_')))
        let pdate =  "{{request()->get('date_')}}";
        sdate = moment(pdate.split('-')[0]);
        edate = moment(pdate.split('-')[1]);
    @endif
    $('#date_picker').daterangepicker(
        {
        ranges   : {
          "Today"      : [moment(), moment()],
          "Yesterday"  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          "Last 7 Days": [moment().subtract(6, 'days'), moment()],
          "Last 30 Days" : [moment().subtract(29, 'days'), moment()],
          "This Month" : [moment().startOf('month'), moment().endOf('month')],
          "Last Month" : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          "This Year" : [moment().startOf('year'), moment().endOf('year')],
        },
        startDate: sdate,
        endDate  : edate
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    );
    $('.ranges > ul > li:last')[0].textContent = "Custom Range";

    $('#date_picker').change(function(){
        $('#date_').val($('#date_picker').val());
        $('#form_dashboard').submit();
    })
</script>
@endsection
