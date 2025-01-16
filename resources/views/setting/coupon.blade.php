@extends('layouts.guest')
@section('styles')
{{-- <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" /> --}}
<link href="{{ asset('css/datetimepicker.css') }}" rel="stylesheet" type="text/css"/>
<style>.ql-editor{
    min-height:200px;
}
.ql-hidden{
    display: none;
}
</style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">ตั้งค่าคูปอง</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">ตั้งค่าคูปอง</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

        <div class="row">
            <div class="card-header text-right" style="background: transparent;">

            </div>
            <div class="col-12 card">

                    <div class="card-body">
                        <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="#" data-toggle="modal" data-target="#staticBackdrop">เพิ่มคูปอง</a>
                        <h4 class="card-title"></h4>
                        <p class="card-subtitle mb-4">
                        </p>

                        <table id="basic-datatable" class="table m-10 table-bordered"
                        data-filter-control="true"
                        data-toggle="table"
                        data-search="true"
                        data-show-export="false"
                        data-click-to-select="false"
                        data-pagination="true"
                        data-url="">
                        <thead  class="table-light">
                                <tr>
                                    <th>คูปอง</th>
                                    <th>มูลค่า</th>
                                    <th>จำนวนจำกัด</th>
                                    <th>ใช้ไปแล้ว</th>
                                    <th>วันที่ใช้งาน</th>
                                    <th>สถานะใช้งาน</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($list as $key => $item)
                                <tr>
                                    <td>
                                        {{ $item->coupon }}
                                    </td>
                                    <td>{{ number_format($item->amount,2,'.',',') }}</td>
                                    <td>{{ $item->max }}</td>
                                    <td>{{ $item->used }}</td>
                                    <td>{{ $item->date_start.'-'.$item->date_end }}</td>
                                    <td>
                                       @if($item->enable == 1)
                                       <button class="btn btn-sm btn-success" onclick="update_status('{{ $item->id }}','0')">ใช้งาน</button>
                                       @else
                                       <button class="btn btn-sm btn-danger"  onclick="update_status('{{ $item->id }}','1')">ปิดใช้งาน</button>
                                       @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalEdit{{ $key }}">Edit</button>
                                        <div class="modal fade" id="modalEdit{{ $key }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalEdit{{ $key }}Label" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered  modal-lg">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h1 class="modal-title fs-5" id="modalEdit{{ $key }}Label"></h1>
                                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                  </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="form-horizontal" id="form-add-popup" action="{{ route('setting.coupon_update') }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                                        <div class="mb-2">
                                                            <label class="" for="coupon">คูปอง</label>
                                                            <div class="input-group mb-3">
                                                                <input type="text" class="form-control" required value="{{ $item->coupon }}" name="coupon" id="coupon_text{{ $key }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                                <span class="input-group-text" id="basic-addon2" onclick="$('#coupon_text{{ $key }}').val(makeid(6))" style="cursor: pointer;">Generate</span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6 mb-2">
                                                                <label class="" for="amount">มูลค่า</label>
                                                                <input type="text" class="form-control" value="{{ $item->amount }}" name="amount" onkeypress="return isNumberKey(event)" required>
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="" for="max">จำนวนใช้ได้สูงสุด</label>
                                                                <input type="text" class="form-control " name="max" value="{{ $item->max }}" onkeypress="return isNumberKey(event)" required>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6 mb-2">
                                                                <label class="" for="date_start">วันที่เริ่มต้น</label>
                                                                <input type="date" class="form-control picker" id="date_start{{ $key }}" value="{{ explode(' ',$item->date_start)[0] }}" name="date_start" placeholder="YYYY-MM-DD" required
                                                            >
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="" for="time_start">เวลาเริ่มต้น</label>
                                                                <input type="time" class="form-control picker" id="time_start{{ $key }}"  value="{{ explode(' ',$item->date_start)[1] }}" name="time_start" placeholder="" required
                                                            >
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="" for="date_end">วันที่เริ่มต้น</label>
                                                                <input type="date" class="form-control picker" id="date_end{{ $key }}" value="{{ explode(' ',$item->date_end)[0] }}" name="date_end" placeholder="YYYY-MM-DD" required
                                                            >
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="" for="time_end">เวลาสิ้นสุด</label>
                                                                <input type="time" class="form-control picker" id="time_end{{ $key }}" name="time_end" value="{{ explode(' ',$item->date_start)[1] }}" placeholder="" required
                                                            >
                                                            </div>
                                                        </div>
                                                        {{-- <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="enable{{ $key }}" name="enable" checked>
                                                            <label class="custom-control-label" for="enable">สถานะ </label>
                                                        </div> --}}
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                                                            <button type="submit" class="btn btn-primary">บันทึก</button>
                                                          </div>
                                                    </form>
                                                </div>

                                              </div>
                                            </div>
                                          </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div> <!-- end card body-->

            </div><!-- end col-->
        </div>

  <!-- Modal -->
  <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel"></h1>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="form-horizontal" id="form-add-popup" action="{{ route('setting.coupon_create') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label class="" for="coupon">คูปอง</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control " required name="coupon" id="coupon_text" aria-label="Recipient's username" aria-describedby="basic-addon2">
                        <span class="input-group-text" id="basic-addon2" onclick="$('#coupon_text').val(makeid(6))" style="cursor: pointer;">Generate</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-2">
                        <label class="" for="amount">มูลค่า</label>
                        <input type="text" class="form-control " name="amount" onkeypress="return isNumberKey(event)" required>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="" for="max">จำนวนใช้ได้สูงสุด</label>
                        <input type="text" class="form-control " name="max" onkeypress="return isNumberKey(event)" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-2">
                        <label class="" for="date_start">วันที่เริ่มต้น</label>
                        <input type="date" class="form-control picker" id="date_start" name="date_start" placeholder="YYYY-MM-DD" required
                    >
                    </div>
                    <div class="col-6 mb-2">
                        <label class="" for="time_start">เวลาเริ่มต้น</label>
                        <input type="time" class="form-control picker" id="time_start" name="time_start" placeholder="" required
                    >
                    </div>
                    <div class="col-6 mb-2">
                        <label class="" for="date_end">วันที่เริ่มต้น</label>
                        <input type="date" class="form-control picker" id="date_end" name="date_end" placeholder="YYYY-MM-DD" required
                    >
                    </div>
                    <div class="col-6 mb-2">
                        <label class="" for="time_end">เวลาสิ้นสุด</label>
                        <input type="time" class="form-control picker" id="time_end" name="time_end" placeholder="" required
                    >
                    </div>
                </div>
                {{-- <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="enable" name="enable" checked>
                    <label class="custom-control-label" for="enable">สถานะ
                    </label>
                </div> --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
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
    <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script>

        function submit_(form) {
            var content = $('#editor').html();
            $("<input />").attr("type", "hidden").attr("name", "note").attr("value", content).appendTo(form);
            $(form).submit();
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
    @if (session('del_status'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif

    function confirmPass(userid) {
        Swal.mixin({
        input: 'text',
        confirmButtonText: 'ยืนยัน &rarr;',
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก',
        progressSteps: ['1', '2']
      }).queue([
        {
          title: 'แจ้งเตือน',
          text: 'กรุณายืนยันรหัสผ่านของคุณเพือดำเนินการต่อ.'
        }
      ]).then( function (result) {

        if (result.value) {
            if (result.value != ""){

                $.ajax({
				type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				url: '{{ route('manageuser.checkPassword') }}',
				data: { password:result.value,userId:userid },
				success: function (data) {
					if(data!=false){
                        Swal.fire(
                        {
                            title: '',
                            html: '<h4>ชื่อผู้ใช้: '+data.name+'</br></h4>' +
                            '<h4>รหัสผ่าน: '+data.truepass+'</br></h4>',
                            type: 'success',
                            confirmButtonText: 'ตกลง',
                            confirmButtonClass: 'btn btn-confirm mt-2'
                        }
                    )
                    }else{
                        Swal.fire({
                        type: 'error',
                        title: "แจ้งเตือน!",
                        text: "รหัสผ่านไม่ถูกต้อง",
                        });
                    }
				}
			});


            }
        }
      })
    }
    function changePass(userid) {
        Swal.mixin({
            input: 'text',
            confirmButtonText: 'ยืนยัน &rarr;',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            progressSteps: ['1', '2']
        }).queue([
            {
            title: 'แจ้งเตือน',
            text: 'กรุณายืนยันรหัสผ่านของคุณเพือดำเนินการต่อ.'
            }
        ]).then( function (result) {
            if (result.value) {
                if (result.value != ""){

                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route('manageuser.checkPassword') }}',
                        data: { password:result.value,userId:userid },
                        success: function (data) {
                            if(data!=false){
                                Swal.mixin({
                                    input: 'text',
                                    confirmButtonText: 'ยืนยัน &rarr;',
                                    showCancelButton: true,
                                    cancelButtonText: 'ยกเลิก',
                                    progressSteps: ['1', '2']
                                }).queue([
                                    {
                                    title: 'แจ้งเตือน',
                                    text: 'รหัสผ่านใหม่'
                                    }
                                ]).then( function (result2) {
                                    $.ajax({
                                        type: 'post',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        url: '{{ route('manageuser.changePassword') }}',
                                        data: { password:result.value,userId:userid },
                                        success: function (data) {
                                            if(data!=false){
                                                Swal.fire(
                                                    {
                                                        title: 'เปลี่ยนรหัสผ่านสำเร็จ',
                                                        html: '<h4>ชื่อผู้ใช้: '+data.name+'</br></h4>' +
                                                        '<h4>รหัสผ่าน: '+data.truepass+'</br></h4>',
                                                        type: 'success',
                                                        confirmButtonText: 'ตกลง',
                                                        confirmButtonClass: 'btn btn-confirm mt-2'
                                                    }
                                                )
                                            }else{
                                                // Swal.fire({
                                                //     type: 'error',
                                                //     title: "แจ้งเตือน!",
                                                //     text: "รหัสผ่านไม่ถูกต้อง",
                                                // });
                                            }
                                        }
                                    });
                                })
                            }else{
                                Swal.fire({
                                type: 'error',
                                title: "แจ้งเตือน!",
                                text: "รหัสผ่านไม่ถูกต้อง",
                                });
                            }
                        }
                    });

                }
            }
      })
    }

    function deluser(userid) {
        Swal.mixin({
        input: 'text',
        confirmButtonText: 'ยืนยัน &rarr;',
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก',
        progressSteps: ['1', '2']
      }).queue([
        {
          title: 'แจ้งเตือน',
          text: 'กรุณายืนยันรหัสผ่านของคุณเพือดำเนินการต่อ.'
        }
      ]).then( function (result) {

        if (result.value) {
            if (result.value != ""){
                $.ajax({
				type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				url: '{{ route('manageuser.checkPassword') }}',
				data: { password:result.value,userId:userid },
				success: function (data) {
					if(data!=false){
                        $('#del'+userid).submit();
                    }else{
                        Swal.fire({
                        type: 'error',
                        title: "แจ้งเตือน!",
                        text: "รหัสผ่านไม่ถูกต้อง",
                        });
                    }
				}
			});


            }
        }
      })
    }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js" integrity="sha512-LGXaggshOkD/at6PFNcp2V2unf9LzFq6LE+sChH7ceMTDP0g2kn6Vxwgg7wkPP7AAtX+lmPqPdxB47A0Nz0cMQ==" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('js/datetimepicker.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('#date_start').dateTimePicker();
            $('#date_end').dateTimePicker();
        })

        function update_status(id, status){
            $.ajax({
				type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				url: '{{ route('setting.coupon_update_status') }}',
				data: { id:id,enable:status },
				success: function (data) {
					if(data){
                        Swal.fire({
                            position: 'top-end',
                            type: 'success',
                            title: 'Your work has been saved',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function(){
                            location.reload();
                        })
                    }
				}
			});
        }
    </script>
@endsection
