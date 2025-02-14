@extends('layouts.guest')
@section('styles')
<link href="{{asset('daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@section('content')
  <!-- start page title -->
  <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">

        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-animate">
            <div class="card-body">
                <h4 class="mb-0 font-size-18">ข้อมูลส่วนตัว</h4>
                <br>
                <p>ระบบจะแสดงข้อมูลส่วนตัวของคุณ ทั้งชื่อ ที่อยู่ อีเมล เบอร์โทรศัพท์ ข้อมูลเหล่านี้จะถือเป็นผู้มีสิทธิในการใช้งานระบบทั้งหมด โปรดตรวจสอบข้อมูลเหล่านี้ให้ถูกต้อง</p>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-animate">
            <div class="card-body">
                <div class="body table-responsive" style="padding:15px">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="font-16">English Information</th>
                                <th class="font-16">ข้อมูลภาษาไทย</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>บริษัท :</th>
                                <td>xxxxxxxxxxxxxxxxxxxxxxx</td>
                                <td>xxxxxxxxxxxxxxxxxxxxxxx</td>
                            </tr>
                            <tr>
                                <th>ชื่อ - นามสกุล:</th>
                                <td>xxxxxxxxxxxxxxxxxxxxxxx</td>
                                <td>xxxxxxxxxxxxxxxxxxxxxxx</td>
                            </tr>
                            <tr>
                                <th>ที่อยู่:</th>
                                <td>xxxxxxxxxxxxxxxxxxxxxxx</td>
                                <td>xxxxxxxxxxxxxxxxxxxxxxx</td>
                            </tr>
                            <tr>
                                <th>เบอร์โทร:</th>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <th>แฟกซ์:</th>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <table class="table col-8">
                            <tbody>
                                <tr>
                                    <th style="border:none">อีเมล์:</th>
                                    <td style="border:none">jula@hotmail.com</td>

                                </tr>
                                <tr>
                                    <th style="border:none">อีเมลล์ออร์เดอร์:</th>
                                    <td style="border:none">jula@hotmail.com</td>

                                </tr>
                                <tr>
                                    <th style="border:none">มือถือ:</th>
                                    <td style="border:none"></td>
                                </tr>
                                <tr>
                                    <th style="border:none">เว็บไซต์:</th>
                                    <td style="border:none">https://taurus99.com/</td>
                                </tr>
                                <tr>
                                    <th style="border:none">ชื่อโดเมน:</th>
                                    <td style="border:none">https://taurus99.com/</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="col-md-4 col-lg-4 col-xs-12" style="padding:10px">
                            <div><a href="#"><b class="font-16">แก้ไขข้อมูลส่วนตัว คลิกที่นี่</b></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- end row-->
<div class="row">
    <div class="card col-12" style="padding:2rem">
        <div class="block-header" style="padding:15px">
            <h2>พารามิเตอร์คีย์</h2>
            <small>พารามิเตอร์หลักใช้ในการส่งข้อมูลหาสาขา</small>
        </div>
        <div class="row" style="padding: 15px;">
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Secret Key :</label>
                    <div class="input-group">
                        <input type="password" class="form-control" value="ewrdkldjssKFDHH" aria-describedby="basic-addon1" id="SecretKey" disabled="">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1" style="cursor: pointer;" onclick="showPassword('SecretKey')"><i class="mdi mdi-eye-outline"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label>API Key :</label>
                    <div class="input-group">
                        <input type="password" class="form-control" value="g78o08[KJL]" aria-describedby="basic-addon1" id="APIKeyAPIKey" disabled="">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1" style="cursor: pointer;" onclick="showPassword('APIKeyAPIKey')"><i class="mdi mdi-eye-outline"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row" style="padding: 15px;">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <label class="col-black" for="Main_Language"><b>Auth Key :</b> </label>
                <textarea style="-webkit-text-security: disc;resize:none;width: 100%;height: 210px;" class="form-control" name="AuthKey" id="AuthKey" disabled="">eyJhbGciOiJSUzI1NiIsInR5cCeroisiosfd9843tgusdfiI6IkpXVCJ9.eyJhdWQiOiIyIiwiZXhwIjozMzExOTE0OTU0LCJpYXQiOjE3MzUxMTQ5NTQsImp0aSI6IjE3MzUxMTQ5NTRlNmVhODVmN2UwZTY1M2MyMzg4MTlkZjEzOTExYmE1ODlmMzVjMWQxNjcwYTJjZWI2YjM2YjU4ZjkzNzc0YzdjZWIzNTA1IiwibmJmIjoxNzM1MTE0OTU0LCJzdWIiOiI0ODIyNCJ9.gAGXflSK4U_BrN-v8P-x3pPqy83qSZmIYAR5h9QgjiWDH4FlcHgZh84HZv3GKK5nJLyVZOn0RuQo2n9yERJPbToIXn9_AUULXOqyKxXwxjBzzSOqJp_mIeIbAm5e2Bu7FOklWbeqtq-rjn9ccupgL1fV0tYbVhDpP-tfJcK0TUsE9_gV97pDiWcPCWLzt6FBjX2SZ2rt3JByiI0pAjFSpEwCp5M7--dspw1WmbvJXsG6BGSoPsQEnrVJ23sHUtsgfY5DF0bow117NG4zxXUqUsulxScCrCE96ZXG_Uh1LsBJVkx-Dkf2xGM6ZcL4Rs2e2T3F_BB7oi8mMGlYk5yaxA</textarea>
                <div class="text-right">
                    <i class="mdi mdi-eye-outline" style="cursor: pointer;font-size:1.5em;" onclick="showPasswordAuth()"></i>
                </div>
            </div>
        </div>
        <div class="row" style="padding: 15px;">
            <div class="col-lg-6 col-sm-12">
                <div class="row">
                    <div class="col-12">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="card col-12" style="padding:2rem">
        <div class="block-header" style="padding:15px">
            <h2>ข้อมูลบริการ</h2><small>ระบบจะแสดงข้อมูลบริการที่คุณใช้งาน
                รายละเอียดของรูปแบบการทำงาน ค่าบริการ วันหมดอายุ
                เพื่อประโยชน์ในการพัฒนาระบบให้เหมาะสมกับการใช้งาน</small>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-12" style="padding:15px">
                <div class="row">
                    <div class="col-6">
                        <b class="col-black"><b>MerchantID :</b></b>
                    </div>

                    <div class="col-6">
                        <p class="col-black" for="MerchantID">56565655</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <b class="col-black"><b>ชนิดบริการ:</b></b>
                    </div>
                    <div class="col-6">
                        <p class="col-black" for="Service_Type">ePayment-L </p>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-6">
                    <b class="col-black"><b>สถานะ </b></b>
                </div>

                <div class="col-6">

                    <label class="col-green text-success" for="Status">
                        Enable
                    </label>

                </div>
            </div>
            <div class="col-lg-6 col-sm-12" style="padding:15px">

            </div>

            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="col-12">
                    <b class="col-black"><b>ช่องทางชำระเงิน :</b></b>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th >Card</th>
                            <th class="text-center" style="vertical-align:middle">ค่าธรรมเนียม (%)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            
                            <td>VISA</td>
                            <td class="text-center">2.4 </td>
                        </tr>
                        <tr>
                            
                            <td>MasterCard</td>
                            <td class="text-center">2.4</td>
                        </tr>

                        <tr>
                            
                            <td>Amex</td>
                            <td class="text-center">3.8</td>
                        </tr>

                        <tr>
                            
                            <td>jcb</td>
                            <td class="text-center">2.6</td>
                        </tr>
                        <tr>
                            
                            <td>cup</td>
                            <td class="text-center">2.6</td>
                        </tr>

                        <tr>
                            
                            <td>iBanking</td>
                            <td class="text-center">2.4</td>
                        </tr>


                        <tr>
                            
                            <td>Bill</td>
                            <td class="text-center">2.4</td>
                        </tr>

                        <tr>
                            
                            <td>Promptpay</td>
                            <td class="text-center">1 </td>
                        </tr>

                        <tr>
                            
                            <td>Alipay</td>
                            <td class="text-center">2</td>
                        </tr>

                        <tr>
                            
                            <td>WeChatPay</td>
                            <td class="text-center">2</td>
                        </tr>
                        <tr>
                            
                            <td>Installment</td>
                            <td class="text-center">2.4</td>
                        </tr>
                        <tr>
                            
                            <td> Other</td>
                            <td class="text-center">3.6</td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>

<!-- end row-->
@endsection
@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
function showPassword(x) {
  var x = document.getElementById(x);
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
var autn = 0;
function showPasswordAuth() {
  if (autn == 0) {
    const AuthKey = document.getElementById("AuthKey");
    AuthKey.style.cssText += "-webkit-text-security: inherit;";
    autn++;
  } else {
    const AuthKey = document.getElementById("AuthKey");
    AuthKey.style.cssText += "-webkit-text-security: disc;";
    autn--;
  }
}
function requestKey(merchantId) {
  console.log(merchantId)
  document.getElementById("request").style.display = "none";
  document.getElementById("loading").style.display = "initial";
  $.ajax({
    url: "/en/requestkey",
    type: "POST",
    data: { merchantId: merchantId },
    datatype: "json",
    success: function (json) {
      console.log(json);
      document.getElementById("loading").style.display = "none";
      document.getElementById("wait").style.display = "initial";
    },
  });
}
</script>
@endsection
