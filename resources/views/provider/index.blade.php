@extends('layouts.guest')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">จัดการค่ายเกม</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                        <li class="breadcrumb-item active">จัดการค่ายเกม</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12 card">
            <div class="card-body">
                <h4 class="card-title"></h4>
                <p class="card-subtitle mb-4">
                </p>

                <table id="table" class="table m-10 table-bordered" data-filter-control="true" data-toggle="table"
                    data-search="true" data-show-export="false" data-click-to-select="false" data-pagination="true"
                    data-url="">
                    <thead>
                        <tr>
                            <th></th>
                            <th data-field="product_id" data-filter-control="input" data-sortable="true">รหัส</th>
                            <th data-field="product_name" data-filter-control="input" data-sortable="true">ชื่อ</th>
                            <th data-field="category" data-filter-control="select" data-sortable="true">หมวดหมู่</th>
                            <th data-field="active" data-filter-control="select" data-sortable="true">สถานะ</th>
                            <th>GameList</th>
                            {{-- <th data-sortable="true">created_at</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $item)
                            <tr>
                                <td>
                                    <img src="{{ $item->img }}" id="gmaeImage{{ $item->id }}" onclick="chooseImage(this,{{ $item->id }},'{{ $item->product_id }}')"   class="image-upload" style="max-height: 250px;cursor: pointer;">
                                </td>
                                <td>{{ $item->product_id }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>
                                    @switch($item->category)
                                        @case("1")
                                        <span class="badge badge-pill badge-soft-success font-size-12">Sportsbook</span>
                                            @break
                                            @case("2")
                                            <span class="badge badge-pill badge-soft-success font-size-12">Live Casino</span>
                                            @break
                                            @case("3")
                                            <span class="badge badge-pill badge-soft-success font-size-12">Slot Game</span>
                                            @break
                                            @case("4")
                                            <span class="badge badge-pill badge-soft-success font-size-12">Fishing Hunter</span>
                                            @break
                                            @case("5")
                                            <span class="badge badge-pill badge-soft-success font-size-12">Game Card</span>
                                            @break
                                            @case("6")
                                            <span class="badge badge-pill badge-soft-success font-size-12">Lotto</span>
                                            @break
                                            @case("7")
                                            <span class="badge badge-pill badge-soft-success font-size-12">E-Sport</span>
                                            @break
                                            @case("8")
                                            <span class="badge badge-pill badge-soft-success font-size-12">Poker Game</span>
                                            @break
                                            @case("9")
                                            <span class="badge badge-pill badge-soft-success font-size-12">Keno</span>
                                            @break
                                            @case("10")
                                            <span class="badge badge-pill badge-soft-success font-size-12">Crypto Tradding
                                            </span>
                                            @break

                                        @default

                                    @endswitch
                                </td>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            onchange="updateStatus(this,{{ $item->id }})"
                                            @if ($item->active == 1) checked value="1" @else value="0" @endif
                                            id="customCheck{{ $item->id }}">
                                        @if ($item->active == 1)
                                            <label class="custom-control-label" for="customCheck{{ $item->id }}">Enable</label>
                                        @else
                                            <label class="custom-control-label" for="customCheck{{ $item->id }}">Disable</label>
                                        @endif

                                    </div>
                                </td>
                                <td><a href="{{ route('gamelist.index',$item->product_id) }}" class="btn btn-primary">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div> <!-- end card body-->

        </div><!-- end col-->
    </div>
    <form action="#" method="POST" id="img-upload" enctype="multipart/form-data" >
        @csrf
            <input type="file" id="imgupload" name="imgupload" class="imgupload" style="display:none" />
            <input type="hidden" id="provider_id" name="provider_id" value="" style="display:none"/>
            <input type="hidden" id="provider" name="provider" value="" style="display:none"/>
    </form>

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
    <script
        src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
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
        function updateStatus(e, id) {
            let c = e.checked;
            let active = 0;
            if(c==true){
                active = 1;
            }
            Swal.fire({
                title: "Are you sure?",
                text: "ต้องการเปลี่ยนแปลงสถานะหรือไม่?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ตกลง"
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: 'get',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route('provider.updateprovider') }}',
                        data: {
                            id: id,
                            checked: active
                        },
                        success: function(data) {
                            if (data) {
                                Swal.fire({
                                    position: 'top-end',
                                    type: 'success',
                                    title: 'Your work has been saved',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            }
                        }
                    });
                } else {
                    alert(1);
                    if (c == false) {
                        e.checked = true;
                    } else {
                        e.checked = false;
                    }
                }
            });

        }


        function chooseImage(e,id,provider){
            $('#provider_id').val(id);
            $('#provider').val(provider);
            $('#imgupload').trigger('click');
        }
        $('#imgupload').change(function(e){
            e.preventDefault();
            const fsize = (this.files[0].size)/1024;
            const fname = (this.files[0].name).split('.').pop();

            if( fname!="png" && fname!="jpg" && fname!="jpeg"){
                Swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: 'Wrong File!',
                            showConfirmButton: false,
                            timer: 1500
                        })
                      return true;
            }
            else if(fsize>2048){
                Swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: 'Image are too large!',
                            showConfirmButton: false,
                            timer: 1500
                        })
                      return true;
            }
            else{
            $('#img-upload').submit();
            }
        });
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#img-upload').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#file-input-error').text('');

        $.ajax({
            type:'POST',
            url: "/updateproviderimage",
            data: formData,
            contentType: false,
            processData: false,
            success: (response) => {
                if (response) {
                    this.reset();
                    //console.log(response);

                    var  imgname= response+'?'+Math.random();
                    $('#gmaeImage'+ $('#provider_id').val()).attr('src',imgname);
                    //ONLY UPDATE YOURSELF
                    // $('#sideuserimage').attr('src',imgname);
                    // $('.save-wrapper').removeClass('show');
                    $(function() {
                        Swal.fire({
                            position: 'top-end',
                            type: 'success',
                            title: 'Your work has been saved',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    });
                }
            },
            error: function(response){
                $('#file-input-error').text(response.responseJSON.message);
                $('.save-wrapper').removeClass('show');
            }
       });
    });
    </script>
@endsection
