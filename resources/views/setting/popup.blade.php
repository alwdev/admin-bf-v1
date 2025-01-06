@extends('layouts.guest')
@section('styles')
{{-- <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" /> --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
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
            <h4 class="mb-0 font-size-18">ตั้งค่าป๊อบอัพ</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">ตั้งค่าป๊อบอัพ</li>
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
                        <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="#" data-toggle="modal" data-target="#staticBackdrop">เพิ่มป๊อบอัพ</a>
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
                                    <th>รูป</th>
                                    <th>เพจ</th>
                                    <th>ข้อความ</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>  
                            <tbody>
                                @foreach ($list as $item)                            
                                <tr>
                                    <td>
                                        @if($item->image)
                                        <img src="{{ asset($item->image) }}" class="img-responsive img-thumbnail" alt="User Image">
                                        @endif
                                    </td>
                                    <td>{{ $item->show_page }}</td>
                                    <td>{!! $item->note !!}</td>
                                    <td>
                                       @if($item->active == 1)
                                       <span class="badge badge-pill badge-success">ใช้งาน</span>
                                       @else
                                       <span class="badge badge-pill badge-danger">ปิดใช้งาน</span>
                                       @endif 
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
            <form class="form-horizontal" id="form-add-popup" action="{{ route('setting.popup_create') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label class="" for="image">รูปภาพ</label>
                    <input type="file" class="form-control" name="image" accept="image/png, image/gif, image/jpeg">
                </div>
                <div class="mb-2">
                    <label class="">หน้าที่ต้องการแสดง</label>
                    <br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="allpage" name="allpage" value="ทุกหน้า" checked>
                        <label class="form-check-label" for="allpage">ทุกหน้า</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="homepage" name="homepage" value="หน้าแรก">
                        <label class="form-check-label" for="homepage">หน้าแรก</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="promotionpage" name="promotionpage" value="หน้าโปรโมชั่น">
                        <label class="form-check-label" for="promotionpage">หน้าโปรโมชั่น</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="gamepage" name="gamepage" value="หน้าเกม">
                        <label class="form-check-label" for="gamepage">หน้าเกม</label>
                    </div>
                </div>
                <div  class="mb-2">
                    <div id="editor">
                    </div>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="active" name="active" checked>
                    <label class="custom-control-label" for="active">สถานะ 
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
          <button type="button" onclick="submit_('#form-add-popup')" class="btn btn-primary">บันทึก</button>
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
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <!-- Initialize Quill editor -->
    <script>
      const quill = new Quill('#editor', {
        theme: 'snow'
      });
    </script>
@endsection