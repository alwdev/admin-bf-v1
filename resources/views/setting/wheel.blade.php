@extends('layouts.guest')
@section('styles')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">ตั้งค่า</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">ตั้งค่า</li>
                </ol>
            </div>
            
        </div>
    </div>
</div>     
<!-- end page title -->
<!-- end row-->
 <div class="row">
    <div class="col-xl-6 d-flex">
        <div class="card flex-fill">
            <form id="form-maintenance" action="{{ route('setting.wheel_update') }}" method="post" enctype="multipart/form-data">
                @csrf
           <div class="card-body">
               <h4 class="card-title">ตั้งค่าเกมกงล้อ</h4>
 
                   <br>
                   <div class="mb-2">
                       <label class="" for="ticket_condition">จะได้ตั๋วทุกยอดเติมสะสม :</label>
                       <input required type="text" class="form-control" name="ticket_condition" value="{{ $setting->ticket_condition }}">
                   </div>
                   {{-- <div class="mb-2">
                       <label class="" for="limit_per_day">จำกัดจำนวนต่อวัน</label>
                       <input required type="text" class="form-control" name="limit_per_day" value="{{ $setting->limit_per_day }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="limit_person">จำกัดจำนวนต่อคนต่อวัน</label>
                       <input required type="text" class="form-control" name="limit_person" value="{{ $setting->limit_person }}">
                   </div>
                   <div class="mb-2">
                       <label class="" for="trunover">จำนวนเท่าของเทิร์น (หน่วย):</label>
                       <input required type="text" class="form-control" name="trunover" value="{{ $setting->trunover }}">
                   </div> --}}
                   <div class="mb-2">
                       <label class="" for="limit_withdraw">ถอนได้สูงสุด (หน่วย) :</label>
                       <input required type="text" class="form-control" name="limit_withdraw" value="{{ $setting->limit_withdraw }}">
                   </div>
                    <div class="custom-control custom-checkbox">
                        <input required type="checkbox" class="custom-control-input"@if($setting->enable == 1) checked @endif id="enable" name="enable">
                        <label class="custom-control-label" for="enable">สถานะ 
                            @if($setting->enable == 1) 
                            <span class="badge text-bg-success text-success">ใช้งาน</span>
                            @else
                            <span class="badge text-bg-danger text-danger">ปิดใช้งาน</span>
                            @endif
                        </label>
                    </div>
                    <div class="mb-2 mt-2">
                        <label class="" for="image">ภาพแสดงกงล้อหน้าลูกค้า</label>
                        <br>
                        @if($setting->image)
                        <img src="{{ $setting->image }}"  style="width:200px;">
                        @endif
                        <input required type="file" class="form-control" name="image">
                        {{-- <span class="text-danger">** ข้อควรระวัง และ พบเจอบ่อยเป็นประจำ.... **</span>
                        - ขนาดภาพ <span class="text-danger">จำเป็นต้อง 500x500 เท่านั้น</span> - สังเกตตัวเลขในช่อง ที่แสดงนี้เพื่อเทียบกับการตั้งค่า - ระยะความกว้างของช่องนั้นต้องเท่ากับต้นแบบเท่านั้นมิฉะนั้น "ผลลัพธ์จะไม่ตรงตามการตั้งค่า" --}}
                    </div>
                   {{-- <button type="button" onclick="formsubmit()" class="btn btn-primary waves-effect waves-light mt-4">บันทึก</button> --}}
               
           </div>
           <!-- end card-body-->
       </div>
       <!-- end card -->
   </div>
    <div class="col-xl-6 d-flex">
        <div class="card flex-fill">
           <div class="card-body">
               <h4 class="card-title">แนะนำเพื่อน | ยอดเดิมพัน/ยอดเสีย</h4>

                   <br>
                   <div class="row">
                        <div class="col-4">
                            <h4>รางวัลที่ 1</h4>
                            <div class="mb-2">
                                <label class="" for="win_1_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_1_reward" value="{{ $setting->win_1_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_1">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_1" value="{{ $setting->win_1 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_1_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_1_rate" value="{{ $setting->win_1_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 2</h4>
                            <div class="mb-2">
                                <label class="" for="win_2_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_2_reward" value="{{ $setting->win_2_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_2">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_2" value="{{ $setting->win_2 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_2_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_2_rate" value="{{ $setting->win_2_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 3</h4>
                            <div class="mb-2">
                                <label class="" for="win_3_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_3_reward" value="{{ $setting->win_3_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_3">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_3" value="{{ $setting->win_3 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_3_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_3_rate" value="{{ $setting->win_3_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 4</h4>
                            <div class="mb-2">
                                <label class="" for="win_4_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_4_reward" value="{{ $setting->win_4_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_4">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_4" value="{{ $setting->win_4 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_4_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_4_rate" value="{{ $setting->win_4_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 5</h4>
                            <div class="mb-2">
                                <label class="" for="win_5_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_5_reward" value="{{ $setting->win_5_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_5">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_5" value="{{ $setting->win_5 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_5_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_5_rate" value="{{ $setting->win_5_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 6</h4>
                            <div class="mb-2">
                                <label class="" for="win_6_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_6_reward" value="{{ $setting->win_6_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_6">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_6" value="{{ $setting->win_6 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_6_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_6_rate" value="{{ $setting->win_6_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 7</h4>
                            <div class="mb-2">
                                <label class="" for="win_7_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_7_reward" value="{{ $setting->win_7_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_7">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_7" value="{{ $setting->win_7 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_7_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_7_rate" value="{{ $setting->win_7_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 8</h4>
                            <div class="mb-2">
                                <label class="" for="win_8_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_8_reward" value="{{ $setting->win_8_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_8">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_8" value="{{ $setting->win_8 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_8_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_8_rate" value="{{ $setting->win_8_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 9</h4>
                            <div class="mb-2">
                                <label class="" for="win_9_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_9_reward" value="{{ $setting->win_9_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_9">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_9" value="{{ $setting->win_9 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_9_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_9_rate" value="{{ $setting->win_9_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 10</h4>
                            <div class="mb-2">
                                <label class="" for="win_10_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_10_reward" value="{{ $setting->win_10_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_10">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_10" value="{{ $setting->win_10 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_10_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_10_rate" value="{{ $setting->win_10_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 11</h4>
                            <div class="mb-2">
                                <label class="" for="win_11_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_11_reward" value="{{ $setting->win_11_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_11">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_11" value="{{ $setting->win_11 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_11_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_11_rate" value="{{ $setting->win_11_rate }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4>รางวัลที่ 12</h4>
                            <div class="mb-2">
                                <label class="" for="win_12_reward">ชื่อรางวัล :</label>
                                <input required type="text" class="form-control" name="win_12_reward" value="{{ $setting->win_12_reward }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_12">ได้รับเครดิต :</label>
                                <input required type="text" class="form-control" name="win_12" value="{{ $setting->win_12 }}">
                            </div>
                            <div class="mb-2">
                                <label class="" for="win_12_rate">เปอร์เซ็น :</label>
                                <input required type="text" class="form-control" name="win_12_rate" value="{{ $setting->win_12_rate }}">
                            </div>
                        </div>

                   </div>

                  
               
           </div> 
           <button type="button" onclick="formsubmit()" class="btn btn-primary waves-effect waves-light mt-4">บันทึก</button>
           <!-- end card-body-->
        </form>
       </div>
       <!-- end card -->
   </div>
 </div>
@endsection
@section('scripts')
    <script>
        function formsubmit() {
            Swal.fire({
            title: "ต้องการบันทึกข้อมูลหรือไม่?",
            // text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "บันทึก",
            cancelButtonText: "ยกเลิก",
            }).then((result) => {
                if (result.value) {
                    $('#form-maintenance').submit();
                }
            });
        }

        @if (session('status'))

            Swal.fire({
                position: 'top-end',
                type: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            })

            @endif
    </script>
@endsection