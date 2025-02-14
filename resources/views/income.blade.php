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
<div class="content-header mb-3">
    <div class="row">
        <div class="col-10"></div>
        <div class="col-2 text-right">
        <input type="text" name="date_picker" class="form-control form-select" id="date_picker" style="font-size:15px;">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-animate">
            <div class="card-body">
                <h4 class="mb-0 font-size-18">รายได้ระหว่างวันที่</h4>
                <br>
                <p>ระบบจะแสดงยอดรวมรายได้ร้านค้าของคุณ รวมทั้งคำนวณยอดเงินค่าธรรมเนียม และยอดเงินสุทธิที่จะได้รับโอนเข้าบัญชี อีกทั้งยังสามารถแสดงข้อมูลย้อนหลังตามช่วงเวลาได้อีกด้วย</p>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="avatar-sm float-right">
                </div>
                <h6 class="text-muted text-uppercase mt-0">อนุมัติ</h6>
                <h3 class="my-3">0 ฿</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="avatar-sm float-right">
                </div>
                <h6 class="text-muted text-uppercase mt-0">ถูกปฎิเสธ</h6>
                <h3 class="my-3">0 ฿</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="avatar-sm float-right">
                </div>
                <h6 class="text-muted text-uppercase mt-0">ถูกยึดหน่วง</h6>
                <h3 class="my-3">0 ฿</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="avatar-sm float-right">
                </div>
                <h6 class="text-muted text-uppercase mt-0">รวมทั้งสิ้น</h6>
                <h3 class="my-3">0 ฿</h3>
            </div>
        </div>
    </div>
</div>

<!-- end row-->

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
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th colspan="2">บัตร</th>
                                <th class="text-center" style="vertical-align:middle">หักค่าธรรมเนียม
                                    (% or THB)</th>
                                <th class="text-right" style="vertical-align:middle">รวมทั้งสิ้น</th>
                                <th class="text-right" style="vertical-align:middle">หักค่าธรรมเนียม
                                    (THB) <br>
                                    <small>รวมภาษีมูลค่าเพิ่มI</small></th>
                                <th class="text-right" style="vertical-align:middle">รวมสุทธิ</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td colspan="3"></td>
                                <td class="text-right  font-bold">
                                    0.00
                                </td>
                                <td class="text-right font-bold">
                                    0.00
                                </td>
                                <td class="text-right font-bold" style="border-bottom:solid 2px #000000">
                                    0.00
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-12">
         <div class="card card-animate" >
            <div class="card-body">

                <div class="col-md-12 text-right">
                    <hr>
                    <h3 class="text-right font-bold">
                        THB 0.00
                    </h3>
                    <hr>
                </div>
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
