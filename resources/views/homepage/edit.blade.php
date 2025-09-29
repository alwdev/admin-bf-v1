{{-- resources/views/admin/home_page/edit.blade.php --}}

@extends('layouts.guest')
@section('styles')
    <style>
        .ck-editor__editable {
            min-height: 800px !important;
            overflow-y: auto;
        }

        .big-editor-height {
            min-height: 800px;
        }

        #content_editor {
            border: 1px solid #dbdbdb;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <h2>Edit Home Page Content</h2>
        <hr>

        <form action="{{ route('homepage.update') }}" method="POST" id="form_home_page">
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
                    <div class="form-group">
                        <label for="meta_keywords">Meta Keywords</label>
                        <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="3">{{ old('meta_keywords', $homePage->meta_keywords) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ฟิลด์ Content (Text Editor Area) --}}
            <div class="card mb-4">
                <div class="card-header">Main Content</div>
                <div class="card-body">
                    {{-- <div class="form-group">
                        <label for="content_editor">Page Content (HTML)</label>
                        <textarea rows="20" class="form-control big-editor-height" id="content_editor" name="content_html"
                            style="min-height: 500px;"> 
                            {{ old('content_html', $homePage->content['main_html'] ?? '') }}
                        </textarea>
                    </div> --}}
                    <div class="form-group">
                        <label for="content_editor">Page Content (HTML)</label>
                        <div id="content_editor" class="big-editor-height">
                            {!! old('content_html', $homePage->content['main_html'] ?? '') !!}
                        </div>
                        <input type="hidden" name="content_html" id="content_html_hidden">
                    </div>

                </div>
            </div>

            {{-- สถานะ (Optional, ใช้ hidden field หากต้องการควบคุมผ่าน logic เท่านั้น) --}}
            <div class="card mb-4">
                <div class="card-header">Content Status</div>
                <div class="card-body">
                    <div class="form-group">
                        <select class="form-control" name="active">
                            <option value="1" @selected($homePage->active == 1)>Enable</option>
                            <option value="0" @selected($homePage->active == 0)>Disable</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Home Page</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/decoupled-document/ckeditor.js"></script>


    <script>
        const csrfToken = document.querySelector('input[name="_token"]').value;

        let editorInstance;

        DecoupledEditor
            .create(document.querySelector('#content_editor'), {
                ckfinder: {
                    uploadUrl: '{{ route('ckeditor.image_upload') }}',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                },
                image: {
                    toolbar: ['imageTextAlternative', '|', 'resizeImage:50', 'resizeImage:75', 'resizeImage:original'],
                    resizeOptions: [{
                            name: 'resizeImage:original',
                            label: 'Original',
                            value: null
                        },
                        {
                            name: 'resizeImage:50',
                            label: '50%',
                            value: '50'
                        },
                        {
                            name: 'resizeImage:75',
                            label: '75%',
                            value: '75'
                        }
                    ]
                }
            })
            .then(editor => {
                editorInstance = editor;
                const toolbarContainer = document.createElement('div');
                toolbarContainer.id = 'toolbar-container';
                document.querySelector('#content_editor').before(toolbarContainer);
                toolbarContainer.appendChild(editor.ui.view.toolbar.element);
            })
            .catch(error => console.error(error));

        // 3. ก่อน submit form ให้อัปเดตค่า hidden input
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('homepage.update') }}"]');
            const hiddenInput = document.querySelector('#content_html_hidden');

            if (!form || !hiddenInput) return;

            form.addEventListener('submit', function(e) {
                hiddenInput.value = editorInstance.getData();
                console.log('Editor data:', hiddenInput.value);
                // alert(hiddenInput.value); // สำหรับ debug
            });
        });
    </script>

    <script>
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
