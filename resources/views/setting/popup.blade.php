@extends('layouts.guest')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-editor {
        min-height: 200px;
    }
    .ql-hidden {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">ตั้งค่าป๊อบอัพ</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 card">
        <div class="card-body">
            <button type="button" class="btn btn-primary btn-gold waves-effect waves-light"
                data-toggle="modal" data-target="#createPopupModal">
                เพิ่มป๊อบอัพ
            </button>

            <table id="basic-datatable" class="table m-10 table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>รูป</th>
                        <th>เพจ</th>
                        <th>ข้อความ</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $item)
                    <tr id="popup-row-{{ $item->id }}"> <!-- ใช้ ID เพื่อลบ row ได้ -->
                        <td>
                            @if($item->image)
                            <img src="{{ asset($item->image) }}" class="img-thumbnail preview-img" width="100">
                            @endif
                        </td>
                        <td>{{ $item->show_page }}</td>
                        <td>{!! $item->note !!}</td>
                        <td>
                            @if($item->active == 1)
                            <span class="badge badge-success">ใช้งาน</span>
                            @else
                            <span class="badge badge-danger">ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td>
                            <!-- ปุ่มแก้ไข -->
                            <button type="button" class="btn btn-warning btn-sm edit-popup"
                                data-id="{{ $item->id }}"
                                data-image="{{ asset($item->image) }}"
                                data-show_page="{{ $item->show_page }}"
                                data-note="{{ $item->note }}"
                                data-active="{{ $item->active }}"
                                data-toggle="modal" data-target="#editPopupModal">
                                แก้ไข
                            </button>
            
                            <!-- ปุ่มลบ -->
                            <button type="button" class="btn btn-danger btn-sm delete-popup" data-id="{{ $item->id }}">
                                ลบ
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>
</div>

<!-- ✅ Create Popup Modal -->
<div class="modal fade" id="createPopupModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มป๊อบอัพ</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-create-popup" action="{{ route('setting.popup_create') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id">

                    <div class="mb-2">
                        <label for="popup_image">รูปภาพ (ขนาด 400x400px)</label>
                        <input type="file" class="form-control" name="image" id="popup_image" accept="image/png, image/gif, image/jpeg">
                        <br>
                        <img id="preview_image" src="" width="100" style="display: none;">
                    </div>

                    <div class="mb-2">
                        <label>หน้าที่ต้องการแสดง</label><br>
                        @php
                        $pages = ["หน้าแรก" => "homepage", "หน้าโปรโมชั่น" => "promotionpage", "หน้าเกม" => "gamepage"];
                        @endphp
                        @foreach ($pages as $label => $id)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input show_page_checkbox" type="checkbox" id="{{ $id }}" name="show_page[]" value="{{ $label }}" required>
                            <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-2">
                        <label>ข้อความ</label>
                        <div id="editor"></div>
                        <input type="hidden" name="note" id="popup_note">
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="popup_active" name="active">
                        <label class="custom-control-label" for="popup_active">สถานะ</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                <button type="button" onclick="submitCreatePopup()" class="btn btn-primary">บันทึก</button>
            </div>
        </div>
    </div>
</div>

                    <!-- ✅ Edit Popup Modal (อยู่นอก foreach) -->
                    <div class="modal fade" id="editPopupModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">แก้ไขป๊อบอัพ</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-edit-popup" action="{{ route('setting.popup_update') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="edit_popup_id">
                    
                                        <div class="mb-2">
                                            <label for="edit_popup_image">รูปภาพ (ขนาด 400x400px)</label>
                                            <input type="file" class="form-control" name="image" id="edit_popup_image" accept="image/*">
                                            <br>
                                            <img id="edit_preview_image" src="" width="100" style="display: none;">
                                        </div>
                    
                                        <div class="mb-2">
                                            <label>หน้าที่ต้องการแสดง</label><br>
                                            @php
                                            $pages = ["หน้าแรก" => "homepage", "หน้าโปรโมชั่น" => "promotionpage", "หน้าเกม" => "gamepage"];
                                            @endphp
                                            @foreach ($pages as $label => $id)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input show_page_checkbox" type="checkbox" id="edit_{{ $id }}" name="show_page[]" value="{{ $label }}" required>
                                                <label class="form-check-label" for="edit_{{ $id }}">{{ $label }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                    
                                        <div class="mb-2">
                                            <label>ข้อความ</label>
                                            <div id="edit_editor"></div>
                                            <input type="hidden" name="note" id="edit_popup_note">
                                        </div>
                    
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="edit_popup_active" name="active">
                                            <label class="custom-control-label" for="edit_popup_active">สถานะ</label>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                                    <button type="button" onclick="submitEditPopup()" class="btn btn-primary">บันทึก</button>
                                </div>
                            </div>
                        </div>
                    </div>
@endsection

<form action="{{ route('setting.popup_delete') }}" method="post" id="delect_popup">
    @csrf
    <input type="hidden" name="id" id="delete_popup_id">
</form>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
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
document.addEventListener("DOMContentLoaded", function () {


    let quillEditor = null;

    // ✅ ตรวจสอบว่า Quill Editor ถูกสร้างแล้วหรือไม่
    function initQuillEditor() {
        if (!quillEditor) {
            let editorElement = document.getElementById("editor");
            if (editorElement) {
                quillEditor = new Quill('#editor', { theme: 'snow' });
                console.log("✅ Quill Editor ถูกสร้างแล้ว");
            } else {
                console.error("❌ ERROR: ไม่พบ #editor ใน DOM!");
            }
        }
    }

    initQuillEditor();

    // ✅ ฟังก์ชัน Preview รูปภาพตอนเลือกไฟล์
    document.getElementById("popup_image").addEventListener("change", function (event) {
        let file = event.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let previewImage = document.getElementById("preview_image");
                previewImage.src = e.target.result;
                previewImage.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    });

    // ✅ ฟังก์ชัน Submit Form สำหรับ Create
    window.submitCreatePopup = function() {
        if (!quillEditor) {
            console.error("❌ ERROR: Quill Editor ยังไม่ถูกสร้าง!");
            return;
        }

        let form = document.getElementById("form-create-popup");
        if (!form) {
            console.error("❌ ERROR: ไม่พบ #form-create-popup ใน DOM!");
            return;
        }

        document.getElementById("popup_note").value = quillEditor.root.innerHTML;
        form.submit();
    };
});



document.addEventListener("DOMContentLoaded", function () {
    let editQuillEditor = new Quill('#edit_editor', { theme: 'snow' });

    // ✅ ฟังก์ชัน Preview รูปภาพ
    function previewImage(input, previewElement) {
        let file = input.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                previewElement.src = e.target.result;
                previewElement.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    }

    document.getElementById("edit_popup_image").addEventListener("change", function () {
        previewImage(this, document.getElementById("edit_preview_image"));
    });

    // ✅ โหลดข้อมูลเมื่อกดปุ่มแก้ไข
    document.body.addEventListener("click", function (event) {
        let button = event.target.closest(".edit-popup");
        if (button) {
            let id = button.getAttribute("data-id");
            let image = button.getAttribute("data-image");
            let showPage = button.getAttribute("data-show_page").split(",");
            let note = button.getAttribute("data-note");
            let active = button.getAttribute("data-active") == "1";

            document.getElementById("edit_popup_id").value = id;
            document.getElementById("edit_popup_active").checked = active;

            // ✅ โหลดข้อความลง Quill Editor
            editQuillEditor.root.innerHTML = note ? note : "";

            // ✅ แสดงรูปเก่าถ้ามี
            let previewImage = document.getElementById("edit_preview_image");
            if (image && image.trim() !== "") {
                previewImage.src = image;
                previewImage.style.display = "block";
            } else {
                previewImage.style.display = "none";
            }

            // ✅ เช็ค checkbox ของ show_page ตามค่าที่มีอยู่
            document.querySelectorAll(".show_page_checkbox").forEach(checkbox => {
                checkbox.checked = showPage.includes(checkbox.value);
            });

            document.getElementById("edit_popup_image").value = ""; // ✅ รีเซ็ต input file เพื่อให้แก้ไขรูปได้
        }
    });

    window.submitEditPopup = function() {
        document.getElementById("edit_popup_note").value = editQuillEditor.root.innerHTML;
        document.getElementById("form-edit-popup").submit();
    };
});


document.addEventListener("DOMContentLoaded", function () {
    // ✅ ลบ popup เมื่อกดปุ่ม
    document.body.addEventListener("click", function (event) {
        let button = event.target.closest(".delete-popup");
        if (button) {
            let popupId = button.getAttribute("data-id");

            // ✅ ใช้ SweetAlert ยืนยันก่อนลบ
            Swal.fire({
                title: "คุณแน่ใจหรือไม่?",
                text: "เมื่อลบแล้วจะไม่สามารถกู้คืนได้!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "ลบ",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.value) {
                    // ✅ ใช้ fetch() ส่ง DELETE request ไปที่ backend
                    fetch("{{ route('setting.popup_delete') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ id: popupId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // ✅ ลบ row ออกจากตารางโดยไม่ต้องโหลดหน้าใหม่
                            document.getElementById(`popup-row-${popupId}`).remove();
                            Swal.fire("ลบสำเร็จ!", "ป๊อบอัพถูกลบแล้ว", "success");
                        } else {
                            Swal.fire("เกิดข้อผิดพลาด!", data.message, "error");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถลบได้", "error");
                    });
                }
            });
        }
    });
});


</script>

@endsection
