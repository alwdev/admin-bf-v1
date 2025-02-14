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

<div class="row">

    <div class="card col-12" style="padding:2rem">
        <div class="block-header"
            style="margin-bottom:0px;padding:15px">
            <!-- <button type="button" class="btn btn-light" style="padding: 10px;color: red;margin: 0px;margin-bottom: 30px;float: right;"><img style="width: 20px;margin-right: 5px;" src="/assets/images/image-news/icon-youtube.png"> ดูวิดิโอสาธิตการใช้งาน</button> -->
            <h2>ข่าวสารและกิจกรรมใหม่</h2>
            <small
                style="color: gray;">ติดตามข่าวสารและกิจกรรมของเรา
                รวมทั้งประกาศแจ้งพัฒนาปรับปรุงระบบการใช้งานต่างๆ
                ได้ที่นี่</small>
        </div>
        <div class="body">
            <div class="table-responsive" style="overflow: hidden;">
                <div id="DataTables_Table_0_wrapper"
                    class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"><div
                        class="row"><div
                            class="col-sm-12 col-md-6"></div><div
                            class="col-sm-12 col-md-6"></div></div><div
                        class="row"><div
                            class="col-sm-12"><table
                                class="table table-hover news-datatable dataTable no-footer"
                                id="DataTables_Table_0"
                                role="grid">
                                <thead>
                                    <tr role="row"><th
                                            class="sorting_disabled"
                                            rowspan="1"
                                            colspan="1"
                                            style="width: 1480px;">News</th></tr>
                                </thead>
                                <tbody>
                                    <tr role="row" class="odd">
                                        <td style="border-top: 0px solid #eee;border-bottom: 1px solid #eee;">
                                            <a href="#">
                                                <div class="col-lg-1" style="float: left;">
                                                    <img style="border-radius: 5px;padding-right: 5px;" class="media-object img-fluid" src="/theme/announcement.png" alt="เรื่อง: เเจ้งวันหยุดเเละกำหนดการโอนเงินคืนร้านค้า (12 ก.พ. 68)
    " width="64" height="64">
                                                </div>
                                                <div class="col-lg-11">
                                                    <span style="display: none;">2568/01/30 18:12</span>
                                                    <p class="col-gray" style="color: #000000c7 !important;">
                                                        <small style="font-size: 11px;color: #00000085 !important;padding-bottom: 5px;" class="col-gray-mini">30/01/2568 18:12</small>
                                                        <br>
                                                        เรื่อง: เเจ้งวันหยุดเเละกำหนดการโอนเงินคืนร้านค้า (12 ก.พ. 68)
    
                                                    </p>
                                                </div>
                                            </a>
                                        </td>
                                    </tr><tr role="row" class="even">
                                        <td style="border-top: 0px solid #eee;border-bottom: 1px solid #eee;">
                                            <a href="#">
                                                <div class="col-lg-1" style="float: left;">
                                                    <img style="border-radius: 5px;padding-right: 5px;" class="media-object img-fluid" src="/theme/announcement.png" alt="เรื่อง: ธนาคารกรุงเทพ จํากัด (มหาชน) (BBL) แจ้งปิดปรับปรุงระบบชั่วคราว (9 ก.พ. 67 เวลา 01:30 น. - 05:00 น.)
    " width="64" height="64">
                                                </div>
                                                <div class="col-lg-11">
                                                    <span style="display: none;">2568/01/30 18:11</span>
                                                    <p class="col-gray" style="color: #000000c7 !important;">
                                                        <small style="font-size: 11px;color: #00000085 !important;padding-bottom: 5px;" class="col-gray-mini">30/01/2568 18:11</small>
                                                        <br>
                                                        เรื่อง: ธนาคารกรุงเทพ จํากัด (มหาชน) (BBL) แจ้งปิดปรับปรุงระบบชั่วคราว (9 ก.พ. 67 เวลา 01:30 น. - 05:00 น.)
    
                                                    </p>
                                                </div>
                                            </a>
                                        </td>
                                    </tr><tr role="row" class="odd">
                                        <td style="border-top: 0px solid #eee;border-bottom: 1px solid #eee;">
                                            <a href="#">
                                                <div class="col-lg-1" style="float: left;">
                                                    <img style="border-radius: 5px;padding-right: 5px;" class="media-object img-fluid" src="/theme/announcement.png" alt="เรื่อง: แจ้งการพัฒนา Inquiry Order API เพื่อเพิ่มประสิทธิภาพการใช้งานระบบ Recurring Payment
     (31 ม.ค. 68 เวลา 01:00 น. - 03:00 น.)
    " width="64" height="64">
                                                </div>
                                                <div class="col-lg-11">
                                                    <span style="display: none;">2568/01/30 18:09</span>
                                                    <p class="col-gray" style="color: #000000c7 !important;">
                                                        <small style="font-size: 11px;color: #00000085 !important;padding-bottom: 5px;" class="col-gray-mini">30/01/2568 18:09</small>
                                                        <br>
                                                        เรื่อง: แจ้งการพัฒนา Inquiry Order API เพื่อเพิ่มประสิทธิภาพการใช้งานระบบ Recurring Payment
     (31 ม.ค. 68 เวลา 01:00 น. - 03:00 น.)
    
                                                    </p>
                                                </div>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table></div></div><div
                        class="row"><div
                            class="col-sm-12 col-md-5"></div><div
                            class="col-sm-12 col-md-7"></div></div></div>
            </div>
        </div>

    </div>
</div>
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
