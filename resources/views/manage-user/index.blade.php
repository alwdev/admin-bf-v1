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
            <h4 class="mb-0 font-size-18">จัดการพนักงาน</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">จัดการพนักงาน</li>
                </ol>
            </div>
            
        </div>
    </div>
</div>     
<!-- end page title -->

        <div class="row">
            <div class="card-header text-right" style="background: transparent;">
                @if( json_decode(auth()->user()->permissions)->manageuser > 2  )
                <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="{{ route('manageuser.addnewuser') }}">เพิ่มพนักงาน</a>
                @endif
            </div>
            <div class="col-12 card">
           
                    <div class="card-body">
                        <h4 class="card-title"></h4>
                        <p class="card-subtitle mb-4">
                        </p>
        
                        <table id="table" class="table m-10 table-bordered"
                        data-filter-control="true"
                        data-toggle="table"
                        data-search="true"
                        data-show-export="false"
                        data-click-to-select="false"
                        data-pagination="true"
                        data-url="">
                            <thead>
                                <tr>
                                    <th data-field="username"  data-filter-control="input" data-sortable="true">Username</th>
                                    <th data-field="email"  data-filter-control="select" data-sortable="true">Email</th>
                                    <th data-field="level"  data-filter-control="select" data-sortable="true">level</th>
                                    <th data-sortable="true">created_at</th>
                                </tr>
                            </thead>  
                            <tbody>
                                @foreach ($alluser as $user_)                            
                                <tr>
                                    <td>{{ $user_->name }}</td>
                                    <td>{{ $user_->email }}</td>
                                    <td>@if($user_->level== 1) Admin @elseif($user_->level == 2) Staff @endif </td>
                                    <td>
                                        {{ $user_->created_at->format('d/m/Y H:i:s') }}
                                        <a href="{{ route('manageuser.show',$user_->safeId) }}" type="button" class="btn btn-secondary btn-sm waves-effect waves-light"><i class="bx bx-slider"></i> ตั้งค่าสิทธิการใช้งาน</a>
                                        <button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="confirmPass('{{ $user_->id }}')"><i class="bx bx-lock-open-alt"></i> ดูรหัสผ่าน</button>
                                        <button type="button" class="btn btn-success btn-sm waves-effect waves-light" onclick="changePass('{{ $user_->id }}')"><i class="bx bx-edit-alt"></i> เปลียนรหัสผ่าน</button>
                                        @if( json_decode(auth()->user()->permissions)->manageuser > 3  )
                                        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="deluser('{{ $user_->id }}')"><i class="bx bx-trash"></i> ลบ</button>
                                        <form method="post" action="{{ route('manageuser.deluser') }}" id="del{{ $user_->id }}">
                                            @csrf
                                            <input type="hidden" name="userid" value="{{ $user_->id }}">
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
        
                    </div> <!-- end card body-->

            </div><!-- end col-->
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

    <script>
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