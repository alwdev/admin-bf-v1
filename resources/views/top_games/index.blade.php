@extends('layouts.guest')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                {{-- เปลี่ยน Title --}}
                <h4 class="mb-0 font-size-18">{{ __('Top Games Management') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">{{ __('Top Games Management') }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 card">

            <div class="card-body">
                {{-- ปุ่มเพิ่มข้อมูล (เปิด Modal) --}}
                <button type="button" class="btn btn-primary btn-gold waves-effect waves-light mb-3" data-toggle="modal"
                    data-target="#addModal">
                    {{ __('Add New Game') }}
                </button>
                <h4 class="card-title"></h4>
                <p class="card-subtitle mb-4">
                </p>

                {{-- ตารางแสดงรายการ Top Games --}}
                <table id="basic-datatable" class="table m-10 table-bordered" data-filter-control="true" data-toggle="table"
                    data-search="true" data-show-export="false" data-click-to-select="false" data-pagination="true"
                    data-url="">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Provider') }}</th>
                            <th>{{ __('Order') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Active') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- ใช้ $topGames เพื่อวนลูปแสดงข้อมูล --}}
                        @foreach ($topGames as $key => $game)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $game->name }}</td>
                                <td>{{ $game->provider }}</td>
                                <td>{{ $game->order_by }}</td>
                                <td>
                                    @if ($game->image)
                                        {{-- ใช้ asset() เพื่อลิงก์ไปยัง public folder โดยตรง --}}
                                        <img src="{{ asset($game->image) }}" alt="Game Image" style="max-height: 50px;">
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    {{-- ปุ่มสลับสถานะ (Enable/Disable) --}}
                                    @if ($game->active)
                                        <button class="btn btn-sm btn-success"
                                            onclick="changeStatus('{{ $game->id }}')">{{ __('Enable') }}</button>
                                    @else
                                        <button class="btn btn-sm btn-danger"
                                            onclick="changeStatus('{{ $game->id }}')">{{ __('Disable') }}</button>
                                    @endif
                                </td>
                                <td>
                                    {{-- ปุ่มแก้ไข (เปิด Modal พร้อมส่งข้อมูล) --}}
                                    <button class="btn btn-sm btn-warning" onclick="editItem(this)"
                                        data-id="{{ $game->id }}" data-name="{{ $game->name }}"
                                        data-provider="{{ $game->provider }}" data-link="{{ $game->link }}"
                                        data-image="{{ $game->image }}" data-order_by="{{ $game->order_by }}"
                                        data-active="{{ $game->active }}">
                                        <i class="bx bx-edit-alt"></i> {{ __('Edit') }}
                                    </button>
                                    {{-- ปุ่มลบ (เรียก SweetAlert Confirm) --}}
                                    <button class="btn btn-sm btn-danger" onclick="confirm_destroy('{{ $game->id }}')">
                                        <i class="bx bx-trash"></i> {{ __('Delete') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    {{-- -
    | MODALS
    - --}}

    {{-- 1. Modal สำหรับเพิ่ม Top Game --}}
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {{-- ต้องเปลี่ยน action ไปที่ route สำหรับ store --}}
                <form id="add_form" action="{{ route('top_games.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">{{ __('Add New Top Game') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="add_name">{{ __('Game Name') }}</label>
                            <input type="text" class="form-control" id="add_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="add_provider">{{ __('Provider') }}</label>
                            <input type="text" class="form-control" id="add_provider" name="provider" required>
                        </div>
                        <div class="form-group">
                            <label for="add_link">{{ __('Game Link') }}</label>
                            <input type="text" class="form-control" id="add_link" name="link" required>
                        </div>
                        <div class="form-group">
                            <label for="add_image">{{ __('Image') }}</label>
                            {{-- เพิ่ม event onchange สำหรับเรียกดู Preview --}}
                            <input type="file" class="form-control-file" id="add_image" name="image"
                                onchange="previewImage(this, 'add_image_preview_container')">

                            {{-- DIV สำหรับแสดงตัวอย่างรูปภาพ --}}
                            <div id="add_image_preview_container" class="mt-2">
                                <img id="add_image_preview" src=""
                                    style="max-width: 100%; max-height: 150px; display: none;" alt="Image Preview">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add_order_by">{{ __('Order By') }}</label>
                            <input type="number" class="form-control" id="add_order_by" name="order_by"
                                value="0">
                        </div>
                        <div class="form-group">
                            <label for="add_active">{{ __('Active') }}</label>
                            <select class="form-control" id="add_active" name="active" required>
                                <option value="1">{{ __('Enable') }}</option>
                                <option value="0">{{ __('Disable') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Modal สำหรับแก้ไข Top Game --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {{-- ต้องเปลี่ยน action ไปที่ route สำหรับ update --}}
                <form id="edit_form" action="{{ route('top_games.update', ':id') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">{{ __('Edit Top Game') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">{{ __('Game Name') }}</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_provider">{{ __('Provider') }}</label>
                            <input type="text" class="form-control" id="edit_provider" name="provider" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_link">{{ __('Game Link') }}</label>
                            <input type="text" class="form-control" id="edit_link" name="link" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_image">{{ __('Change Image') }}
                                ({{ __('Leave blank to keep current') }})</label>
                            {{-- เพิ่ม event onchange สำหรับเรียกดู Preview --}}
                            <input type="file" class="form-control-file" id="edit_image" name="image"
                                onchange="previewImage(this, 'edit_image_preview_container')">

                            {{-- DIV สำหรับแสดงตัวอย่างรูปภาพใหม่ --}}
                            <div id="edit_image_preview_container" class="mt-2">
                                <img id="edit_image_preview" src=""
                                    style="max-width: 100%; max-height: 150px; display: none;" alt="New Image Preview">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_order_by">{{ __('Order By') }}</label>
                            <input type="number" class="form-control" id="edit_order_by" name="order_by">
                        </div>
                        <div class="form-group">
                            <label for="edit_active">{{ __('Active') }}</label>
                            <select class="form-control" id="edit_active" name="active" required>
                                <option value="1">{{ __('Enable') }}</option>
                                <option value="0">{{ __('Disable') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Form ซ่อนสำหรับ DELETE --}}
    <form method="post" id="destroy_form" action="{{ route('top_games.destroy', ':id') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" id="destroy_id" name="id">
    </form>
@endsection

{{-- -
| SCRIPTS
- --}}
@section('scripts')
    {{-- Scripts เดิมของคุณ --}}
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/buttons.bootstrap4.css') }}">
    <link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- เพิ่ม SweetAlert2 --}}

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
        function previewImage(input, containerId) {
            const previewImg = document.getElementById(containerId).querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block'; // แสดงรูป Preview
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                previewImg.src = '';
                previewImg.style.display = 'none'; // ซ่อนถ้าไม่มีไฟล์
            }
        }
        // ฟังก์ชันยืนยันการลบโดยใช้ SweetAlert2
        function confirm_destroy(id) {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('You won\'t be able to revert this!') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    // กำหนด ID ใน form ซ่อน
                    // และเปลี่ยน action ของ form ให้เป็น route ที่ถูกต้อง
                    let form = $('#destroy_form');
                    let actionUrl = form.attr('action').replace(':id', id);
                    form.attr('action', actionUrl);

                    form.submit();
                }
            });
        }

        // ฟังก์ชันแสดง Modal แก้ไข
        function editItem(button) {
            const id = $(button).data('id');
            const name = $(button).data('name');
            const provider = $(button).data('provider');
            const link = $(button).data('link');
            const image = $(button).data('image'); // ชื่อ/พาธ รูปภาพปัจจุบัน
            const order_by = $(button).data('order_by');
            const active = $(button).data('active');

            // รีเซ็ต Image Preview ของ Modal แก้ไข
            $('#edit_image_preview').attr('src', '').hide();
            $('#edit_image').val(''); // ล้างค่า input file

            // เปลี่ยน action ของ form ให้เป็น route สำหรับ update ด้วย ID ที่ถูกต้อง
            let form = $('#edit_form');
            let actionUrl = "{{ route('top_games.update', ':id') }}";
            form.attr('action', actionUrl.replace(':id', id));

            // Populate the form fields in the edit modal
            $('#edit_id').val(id);
            $('#edit_name').val(name);
            $('#edit_provider').val(provider);
            $('#edit_link').val(link);
            $('#edit_order_by').val(order_by);
            $('#edit_active').val(active);

            // **แสดงรูปภาพปัจจุบัน**
            const currentImagePreview = $('#edit_image_preview');
            if (image) {
                // ใช้ asset() เพื่อสร้าง URL ที่ถูกต้องสำหรับ public path
                currentImagePreview.attr('src', '{{ asset('') }}' + image);
                currentImagePreview.show();
            } else {
                currentImagePreview.attr('src', '');
                currentImagePreview.hide();
            }

            // Show the edit modal
            $('#editModal').modal('show');
        }

        // ตัวอย่างฟังก์ชัน changeStatus (คุณต้องเพิ่ม route และ logic ใน Controller เอง)
        function changeStatus(id) {
            // โค้ดสำหรับเรียก AJAX เพื่อเปลี่ยนสถานะ active
            // ตัวอย่าง: window.location.href = '/top-games/status/' + id; 
        }

        // การแจ้งเตือน Success/Error จาก Session Flash
        @if (session('success'))
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2500
            })
        @endif

        @if (session('error'))
            Swal.fire({
                title: '{{ __('Validation Failed') }}', // Title หลัก
                icon: 'error',
                html: true,
                text: @json(session('error')),
                showConfirmButton: true,
            })
        @endif

        // **เมื่อ Modal ถูกปิด ให้รีเซ็ต Add Modal Preview ด้วย**
        $('#addModal').on('hidden.bs.modal', function() {
            $('#add_image_preview').attr('src', '').hide();
            $('#add_image').val('');
        });
    </script>
@endsection
