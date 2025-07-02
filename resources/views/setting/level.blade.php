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
            <h4 class="mb-0 font-size-18">{{__('main.member_level_setting')}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">{{__('main.member_level_setting')}}</li>
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
                        <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="#" data-toggle="modal" data-target="#staticBackdrop">{{__('main.Add')}}
                        </a>
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
                                    <th>#</th>
                                    <th>{{__('dashboard.name')}} Level</th>
                                    <th>{{__('dashboard.deposit_amount')}}</th>
                                    <th>{{__('managemember.to')}}</th>
                                    <th>{{__('main.picture')}}</th>
                                    <th>{{__('managemember.manage')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($list as $key => $item)
                                <tr>
                                    <td>{{ $item->rank }}</td>
                                    <td>{{ $item->level_name }}</td>
                                    <td>{{ number_format($item->level_min_point) }}</td>
                                    <td>{{ number_format($item->level_max_point) }}</td>
                                    <td>
                                        @if($item->image)
                                        <img src="{{ asset($item->image) }}" class="img-responsive img-thumbnail" width="64" alt="User Image">
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary display-inline" data-toggle="modal" data-target="#modalEdit{{ $key }}">{{__('main.edit')}}</button>
                                        <!-- Modal -->
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
                                                  <form class="form-horizontal" id="form-add-level{{ $key }}" action="{{ route('setting.level_update') }}" method="post" enctype="multipart/form-data">
                                                      @csrf
                                                      <input type="hidden" name="id" value="{{ $item->id }}">
                                                      <div class="mb-2">
                                                          <label class="" for="image">{{__('main.picture')}}</label>
                                                          @if($item->image)
                                                            <br>
                                                            <img src="{{ asset($item->image) }}" class="img-responsive img-thumbnail" width="200" alt="User Image">
                                                          @endif
                                                          <input type="file" class="form-control" name="image" accept="image/png, image/gif, image/jpeg">
                                                      </div>
                                                      <div class="mb-2">
                                                          <label class="" for="rank">{{__('main.order')}}</label>
                                                          <input type="number" class="form-control" name="rank" value="{{ $item->rank }}" @required(true)>
                                                      </div>
                                                      <div class="mb-2">
                                                          <label class="" for="level_name">{{__('dashboard.name')}} Level</label>
                                                          <input type="text" class="form-control" name="level_name" value="{{ $item->level_name }}" @required(true)>
                                                      </div>
                                                      <div class="mb-2">
                                                          <label class="" for="level_min_point">{{__('dashboard.deposit_amount')}}</label>
                                                          <input type="number" class="form-control" name="level_min_point"  value="{{ $item->level_min_point }}" @required(true)>
                                                      </div>
                                                      <div class="mb-2">
                                                          <label class="" for="level_max_point">{{__('managemember.to')}}</label>
                                                          <input type="number" class="form-control" name="level_max_point"  value="{{ $item->level_max_point }}"  @required(true)>
                                                      </div>
                                                  </form>
                                              </div>
                                              <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('main.close')}}</button>
                                                <button type="button" onclick="submit_('#form-add-level{{ $key }}')" class="btn btn-primary">{{__('main.save')}}</button>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                        <button type="button" class="btn btn-danger display-inline" onclick="confirm_destroy('{{ $item->id }}')">{{__('managemember.delete')}}</button>
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
            <form class="form-horizontal" id="form-add-level" action="{{ route('setting.level_create') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label class="" for="image">{{__('main.picture')}}</label>
                    <input type="file" class="form-control" name="image" accept="image/png, image/gif, image/jpeg">
                </div>
                <div class="mb-2">
                    <label class="" for="rank">{{__('main.order')}}</label>
                    <input type="number" class="form-control" name="rank" @required(true)>
                </div>
                <div class="mb-2">
                    <label class="" for="level_name">{{__('dashboard.name')}} Level</label>
                    <input type="text" class="form-control" name="level_name" @required(true)>
                </div>
                <div class="mb-2">
                    <label class="" for="level_min_point">{{__('dashboard.deposit_amount')}}</label>
                    <input type="number" class="form-control" name="level_min_point" @required(true)>
                </div>
                <div class="mb-2">
                    <label class="" for="level_max_point">{{__('managemember.to')}}</label>
                    <input type="number" class="form-control" name="level_max_point" @required(true)>
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('main.close')}}</button>
          <button type="button" onclick="submit_('#form-add-level')" class="btn btn-primary">{{__('main.save')}}</button>
        </div>
      </div>
    </div>
  </div>
<!-- end row-->

<form method="post" id="level_destroy_form" action="{{ route('setting.level_destroy') }}">
    @csrf
    <input type="hidden" id="level_destroy_id" name="id">
</form>
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

        function confirm_destroy(id) {

            Swal.fire({
                title: "{{__('setting.Do you want to delete this information?')}}",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "{{__('managemember.delete')}}",
                denyButtonText: '{{__('main.cancel')}}'
                }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $('#level_destroy_id').val(id);
                    $('#level_destroy_form').submit();
                }else{
                }
            });
            //
        }

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
@endsection
