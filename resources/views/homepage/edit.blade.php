{{-- resources/views/admin/home_page/edit.blade.php --}}

@extends('layouts.guest')
@section('style')
    <style>
        /* กำหนดความสูงสำหรับพื้นที่แก้ไขของ CKEditor */
        /* .ck-editor__editable เป็น Class หลักที่ CKEditor ใช้ */
        .ck-editor__editable {
            /* ปรับค่า 500px, 600px, หรือ 700px ตามความต้องการ */
            min-height: 600px !important;
            max-height: 80vh;
            /* ป้องกันความสูงเกินจอหากเปิดในจอขนาดเล็ก */
            overflow-y: auto;
        }

        /* หากใช้ textarea เดิมเป็นตัวควบคุมความสูง (ใช้ในกรณีไม่มี editor) */
        .big-editor-height {
            min-height: 600px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <h2>Edit Home Page Content</h2>
        <hr>

        <form action="{{ route('homepage.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- ฟิลด์ Meta/SEO --}}
            <div class="card mb-4">
                <div class="card-header">SEO / Metadata</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                            value="{{ old('meta_title', $homePage->meta_title) }}">
                    </div>
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $homePage->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ฟิลด์ Content (Text Editor Area) --}}
            <div class="card mb-4">
                <div class="card-header">Main Content</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="content_editor">Page Content (HTML)</label>
                        {{-- ดึง HTML ที่เก็บไว้ในโครงสร้าง JSON: $homePage->content['main_html'] --}}
                        <textarea class="form-control big-editor-height" id="content_editor" name="content_html" style="min-height: 500px;"> 
        {{ old('content_html', $homePage->content['main_html'] ?? '') }}
    </textarea>
                    </div>
                </div>
            </div>

            {{-- สถานะ (Optional, ใช้ hidden field หากต้องการควบคุมผ่าน logic เท่านั้น) --}}
            <input type="hidden" name="active" value="1">

            <button type="submit" class="btn btn-primary">Save Home Page</button>
        </form>
    </div>
@endsection

@section('scripts')
    {{-- ต้องโหลด Text Editor Library (ตัวอย่าง CKEditor 5) --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

    <script>
        // 1. Initialize the Text Editor
        ClassicEditor
            .create(document.querySelector('#content_editor'))
            .catch(error => {
                console.error(error);
            });

        // 2. SweetAlert Error Handling (นำโค้ดเดิมมาใส่)
        @if (session('error'))
            Swal.fire({
                title: 'Validation Failed',
                icon: 'error',
                html: true,
                text: @json(session('error')),
                showConfirmButton: true,
            })
        @endif

        @if (session('success'))
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2500
            })
        @endif
    </script>
@endsection
