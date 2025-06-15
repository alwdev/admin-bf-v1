@extends('layouts.guest')

@section('styles')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<style>
/* กำหนดความสูงของ Quill editor */
.ql-container {
    height: 600px;  /* ปรับขนาดความสูงให้ใหญ่ขึ้น */
}

/* หรือปรับการแสดงผลของแต่ละบรรทัด */
.ql-editor {
    min-height: 600px;  /* กำหนดความสูงต่ำสุดของ editor */
}
.tag-item{
    margin-top: 0.5rem;
}
</style>
@endsection

@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">เพิ่มบทความ</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('article.index') }}">บทความ</a></li>
                    <li class="breadcrumb-item active">เพิ่มบทความ</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-4 mt-3">
                </div>
                <form class="p-2" action="{{ route('article.store') }}" method="POST" enctype="multipart/form-data" id="article-form">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input class="form-control" type="text" id="title" name="title" required value="{{ old('title') }}">
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="image">รูปภาพ(หัว) (ขนาด 900x400px)</label>
                        <input class="form-control" type="file" id="image" name="image" required  value="{{ old('image') }}"  accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps">
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="image_end">รูปภาพ(ท้าย) (ขนาด 900x400px)</label>
                        <input class="form-control" type="file" id="image_end" name="image_end" required  value="{{ old('image_end') }}"  accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps">
                        <x-input-error :messages="$errors->get('image_end')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="category">หมวดหมู่</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="description">ย่อหน้า</label>
                        <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div class="form-group">
                        <label for="content">บทความ</label>
                        {{-- Quill Editor --}}
                        <div id="editor-container"></div>
                        <input type="hidden" name="content" id="content">
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>
                     <div class="form-group">
                        <label for="tags">Tags</label>
                        <div id="tags-container">
                            <input type="text" id="tag-input" class="form-control" placeholder="Add a tag">
                            <button type="button" class="btn btn-info mt-2" id="add-tag">Add Tag</button>
                            <input type="hidden" id="tags-input" name="tags">
                        </div>
                        <div id="tags-list" class="mt-2"></div>
                    </div>

                    <div class="custom-control custom-checkbox custom-control-inline mb-3">
                        <input type="checkbox" class="custom-control-input" id="enable" name="enable" checked value="1">
                        <label class="custom-control-label" for="enable">Enable (เผยแพร่)</label>
                    </div>
                    <div class="mb-3 text-center">
                        <button class="btn btn-primary btn-block" type="submit"> บันทึก </button>
                    </div>
                </form>
            </div>
            <!-- end card-body -->
        </div>
    </div> <!-- end col-->
</div> <!-- end row -->
@endsection

@section('scripts')
    <!-- third party js -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
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
    <script src="{{ asset('plugins/datatables/vfs_fonts.js')}}"></script>
    <!-- third party js ends -->

    <!-- Datatables init -->
    <script src="{{ asset('pages/datatables-demo.js')}}"></script>

    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0"></script>
    <script>
        // Initializing Quill editor
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'], // Text formatting buttons
                    [{ 'align': [] }], // Text alignment
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }], // Lists
                    ['link', 'image'], // Insert link and image
                    ['blockquote', 'code-block'], // Blockquote and Code block
                    [{ 'size': ['small', false, 'large', 'huge'] }], // Font size
                    [{ 'color': [] }, { 'background': [] }], // Text color and background
                    ['video'], // Embed video
                ],
                 
        imageResize: {
          displaySize: true,  // Show image size when resizing
        },
            }
        });
        const MAX_CHARACTERS = 15000;

quill.on('text-change', function(delta, oldDelta, source) {
    const currentTextLength = quill.getText().length;

    // If text exceeds the max limit of 15,000 characters
    if (currentTextLength > MAX_CHARACTERS) {
        // Calculate how much text is exceeding the limit
        const excessText = currentTextLength - MAX_CHARACTERS;
        const currentText = quill.getText();
        const truncatedText = currentText.slice(0, -excessText);

        // Prevent the "text-change" event from being triggered again
        quill.root.innerHTML = truncatedText;

        // Show a SweetAlert with the message
        Swal.fire({
            icon: 'warning',
            title: 'Character Limit Reached',
            text: 'You have reached the maximum character limit of 15,000.',
            confirmButtonText: 'OK'
        });
    }
});
const imageHandler = function() {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');

    input.addEventListener('change', () => {
        const file = input.files[0];

        if (file) {
            // ตรวจสอบขนาดไฟล์ก่อนที่จะแทรกใน Quill
            const fileSize = file.size; // ขนาดไฟล์เป็น bytes
            if (fileSize > 1048576) {  // 1MB = 1048576 bytes
                alert('ไฟล์ภาพใหญ่เกิน 1MB ไม่สามารถอัปโหลดได้');
            } else {
                // ถ้าขนาดไฟล์ไม่เกิน 1MB ให้ส่งไฟล์ไปยังเซิร์ฟเวอร์
                const formData = new FormData();
                formData.append('file', file);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/upload-image', {
                    method: 'POST',
                     headers: {
                        'X-CSRF-TOKEN': csrfToken // ใส่ CSRF token ใน headers
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.url) {
                        // ถ้าได้รับ URL ของภาพจากเซิร์ฟเวอร์ ให้แทรก URL ของภาพใน Quill
                        const range = quill.getSelection();
                        quill.insertEmbed(range.index, 'image', data.url);
                    } else {
                        alert('ไม่สามารถอัปโหลดภาพได้');
                    }
                })
                .catch(error => {
                    console.error('เกิดข้อผิดพลาด:', error);
                    alert('ไม่สามารถอัปโหลดภาพได้');
                });
            }
        }
    });

    input.click();
};


// ตั้งค่าฟังก์ชันให้ Quill ใช้งาน
quill.getModule('toolbar').addHandler('image', imageHandler);
        // Function to set content from Quill to hidden input when submitting the form
        document.getElementById('article-form').addEventListener('submit', function() {
            // Get the HTML content from the editor and assign it to the hidden input
            var content = quill.root.innerHTML;
            document.getElementById('content').value = content;
        });
    </script>

    <script>
    // JavaScript to handle adding and removing tags
    document.getElementById('add-tag').addEventListener('click', function() {
        var tagInput = document.getElementById('tag-input');
        var tagValue = tagInput.value.trim();

        if (tagValue) {
            // Create tag div with remove button
            var tagDiv = document.createElement('div');
            tagDiv.className = 'tag-item';
            tagDiv.innerHTML = tagValue + ' <button type="button" class="remove-tag btn btn-danger btn-sm">x</button>';
            document.getElementById('tags-list').appendChild(tagDiv);

            // Clear the input field
            tagInput.value = '';

            // Add event listener for remove button
            tagDiv.querySelector('.remove-tag').addEventListener('click', function() {
                tagDiv.remove();
            });
        }
    });

    // Optionally handle form submission
    document.getElementById('article-form').addEventListener('submit', function(e) {
        // Gather all the tags and append to the hidden field
        var tags = [];
        var tagItems = document.querySelectorAll('.tag-item');
        tagItems.forEach(function(tag) {
            tags.push(tag.innerText.replace(' x', ''));
        });
        document.getElementById('tags-input').value = tags.join(',');
    });
</script>
@endsection
