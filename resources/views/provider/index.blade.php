@extends('layouts.guest')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">{{__('main.game_cate')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">{{__('main.game_cate')}}</li>
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
                <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#addProductModal">
                    เพิ่มข้อมูล
                </button>

                <table id="basic-datatable" class="table m-10 table-bordered" data-filter-control="true" data-toggle="table"
                    data-search="true" data-show-export="false" data-click-to-select="false" data-pagination="true"
                    data-url="">
                    <thead  class="table-light">
                        <tr>
                            <th></th>
                            <th>{{__('main.Small_picture')}}</th>
                            <th data-field="product_id" data-filter-control="input" data-sortable="true">{{__('main.code')}}</th>
                            <th data-field="product_name" data-filter-control="input" data-sortable="true">{{__('dashboard.name')}}</th>
                            <th data-field="category" data-filter-control="select" data-sortable="true">{{__('main.Category')}}</th>
                            <th data-field="active" data-filter-control="select" data-sortable="true">{{__('dashboard.status')}}</th>
                            <th data-field="order_top"  data-sortable="true">{{__('main.order')}}</th>
                            <th >{{__('main.Edit sequence')}}</th>
                            {{-- <th>GameList</th> --}}
                            {{-- <th data-sortable="true">created_at</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $item)
                            <tr>
                                <td>
                                    <img src="{{ $item->img }}" id="gmaeImage{{ $item->id }}" onclick="chooseImage(this,{{ $item->id }},'{{ $item->product_id }}','L')"   class="image-upload" style="max-height: 250px;cursor: pointer;">
                                </td>
                                <td>
                                    <img src="{{ $item->img_mini }}" id="gmaeImage2{{ $item->id }}" onclick="chooseImage(this,{{ $item->id }},'{{ $item->product_id }}','S')"   class="image-upload" style="max-height: 80px;cursor: pointer;">
                                </td>
                                <td>{{ $item->product_id }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>
                                    @switch($item->category)
                                        @case("1")
                                        Sportsbook
                                            @break
                                            @case("2")
                                            Live Casino
                                            @break
                                            @case("3")
                                            Slot Game
                                            @break
                                            @case("4")
                                            Fishing Hunter
                                            @break
                                            @case("5")
                                            Game Card
                                            @break
                                            @case("6")
                                            Lotto
                                            @break
                                            @case("7")
                                            E-Sport
                                            @break
                                            @case("8")
                                            Poker Game
                                            @break
                                            @case("9")
                                            Keno
                                            @break
                                            @case("10")
                                            Crypto Tradding
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
                                {{-- <td><a href="{{ route('gamelist.index',$item->product_id) }}" class="btn btn-primary">View</a></td> --}}
                                <td>
                                    <div class="d-flex">

                                        {{ $item->order_top }}
                                        <button type="button" class="btn btn-primary btn-sm waves-effect waves-light ml-2" onclick="editOrderTop('{{ $item->id }}')"><i class="bx bx-edit-alt"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm"
    onclick='openEditModal(@json($item))'
    data-toggle="modal" data-target="#editProductModal">
    <i class="bx bx-edit-alt"></i>
</button>

                                </td>
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
            <input type="hidden" id="size" name="size" value="" style="display:none"/>
    </form>


    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มข้อมูล</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- ข้อมูลพื้นฐาน --}}
                    <div class="form-group">
                        <label for="product_id">รหัส</label>
                        <input type="text" class="form-control" name="product_id" required>
                    </div>

                    <div class="form-group">
                        <label for="product_name">ชื่อ</label>
                        <input type="text" class="form-control" name="product_name" required>
                    </div>

                    <div class="form-group">
                        <label for="category">หมวดหมู่</label>
                        <select class="form-control" name="category" required>
                            @foreach ($categorys as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="active">สถานะ</label>
                        <select class="form-control" name="active" required>
                            <option value="1">Enable</option>
                            <option value="0">Disable</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="order_top">ลำดับการแสดงผล</label>
                        <input type="number" class="form-control" name="order_top" required>
                    </div>

                    {{-- รูปภาพแบบแบ่งซ้ายขวา --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="img">รูปหลัก (ขนาดใหญ่)</label>
                            <input type="file" class="form-control-file" name="img" id="img" accept="image/*" onchange="previewImage(this, 'previewImg')">
                            <img id="previewImg" src="#" alt="Preview" style="width: 100%; max-height: 250px; margin-top: 10px; display: none; object-fit: contain;">
                        </div>

                        <div class="col-md-6">
                            <label for="img_mini">รูปขนาดย่อ</label>
                            <input type="file" class="form-control-file" name="img_mini" id="img_mini" accept="image/*" onchange="previewImage(this, 'previewMini')">
                            <img id="previewMini" src="#" alt="Preview" style="width: 100%; max-height: 150px; margin-top: 10px; display: none; object-fit: contain;">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="editProductForm" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูล</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- hidden id --}}
                    {{-- <input type="hidden" name="product_id_hidden" id="edit_product_id_hidden"> --}}

                    <div class="form-group">
                        <label>รหัส</label>
                        <input type="text" class="form-control" name="product_id" id="edit_product_id" required>
                    </div>

                    <div class="form-group">
                        <label>ชื่อ</label>
                        <input type="text" class="form-control" name="product_name" id="edit_product_name" required>
                    </div>

                    <div class="form-group">
                        <label>หมวดหมู่</label>
                        <select class="form-control" name="category" id="edit_category" required>
                            @foreach ($categorys as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>สถานะ</label>
                        <select class="form-control" name="active" id="edit_active" required>
                            <option value="1">Enable</option>
                            <option value="0">Disable</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>ลำดับการแสดงผล</label>
                        <input type="number" class="form-control"   name="order_top" id="edit_order_top" required>
                    </div>

                    {{-- รูปแบบซ้าย-ขวาเหมือนเดิม --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="edit_img">รูปหลัก (ขนาดใหญ่)</label>
                            <input type="file" class="form-control-file" name="img" id="edit_img" accept="image/*" onchange="previewImage(this, 'previewEditImg')">
                            <img id="previewEditImg" src="#" style="width: 100%; max-height: 250px; margin-top: 10px; display: none; object-fit: contain;">
                        </div>

                        <div class="col-md-6">
                            <label for="edit_img_mini">รูปขนาดย่อ</label>
                            <input type="file" class="form-control-file" name="img_mini" id="edit_img_mini" accept="image/*" onchange="previewImage(this, 'previewEditMini')">
                            <img id="previewEditMini" src="#" style="width: 100%; max-height: 150px; margin-top: 10px; display: none; object-fit: contain;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </form>
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
    <script
        src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
    <script>
        function editOrderTop(id) {
        Swal.mixin({
                input: 'text',
                confirmButtonText: 'Ok &rarr;',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                progressSteps: ['1', '2']
            }).queue([
                {
                title: '{{__('main.Edit sequence')}}',
                text: '{{__('main.order')}}'
                }
            ]).then( function (result) {
                if (result.value) {
                        $.ajax({
                            type: 'post',
                            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route('provider.order_top') }}',
                            data: { id:id,order_top:Number(result.value) },
                            success: function (data) {
                                if(data!=false){
                                    Swal.fire(
                                            {
                                                title: 'success',
                                                type: 'success',
                                                confirmButtonText: 'OK',
                                                confirmButtonClass: 'btn btn-confirm mt-2'

                                            }
                                        ).then(function() {
                                            location.reload();
                                        });

                                    }
                                }
                        });
                }else if(result.dismiss == 'cancel'){
                    console.log('cancel');
                    }
            })
    }
        @if (session('status') || session('success'))

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
                text: "{{__('main.Do you want to change your status?')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok"
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
                    // alert(1);
                    if (c == false) {
                        e.checked = true;
                    } else {
                        e.checked = false;
                    }
                }
            });

        }


        function chooseImage(e,id,provider,size){
            $('#provider_id').val(id);
            $('#provider').val(provider);
            $('#size').val(size);
            $('#imgupload').trigger('click');
        }
        $('#imgupload').change(function(e){
            e.preventDefault();
            const fsize = (this.files[0].size)/1024;
            const fname = (this.files[0].name).split('.').pop();

            if(fname !== "png" && fname !== "jpg" && fname !== "jpeg" && fname !== "webp" && fname !== "gif" && fname !== "svg") {
                Swal.fire({
                    position: 'top-end',
                    type: 'error',
                    title: 'Wrong File!',
                    showConfirmButton: false,
                    timer: 1500
                });
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
                    if($('#size').val() == "L"){
                        $('#gmaeImage'+ $('#provider_id').val()).attr('src',imgname);
                    }else{
                        $('#gmaeImage2'+ $('#provider_id').val()).attr('src',imgname);
                    }
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

    <script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            }

            reader.readAsDataURL(file);
        } else {
            preview.src = "#";
            preview.style.display = "none";
        }
    }
</script>
<script>
    function openEditModal(data) {
    $('#edit_product_id').val(data.product_id);
    $('#edit_product_id_hidden').val(data.id);
    $('#edit_product_name').val(data.product_name);
    $('#edit_category').val(data.category);
    $('#edit_active').val(data.active);
    $('#edit_order_top').val(data.order_top);

    $('#previewEditImg').attr('src', data.img).show();
    $('#previewEditMini').attr('src', data.img_mini).show();

    $('#editProductForm').attr('action', `/provider/${data.id}`);
}

</script>

@endsection
