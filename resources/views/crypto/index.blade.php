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
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Coin</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Coin</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        {{-- <div class="card-header text-right" style="background: transparent;">
            <label for="coin_name">Coin Name:</label>
            <input type="text" id="coin_name" class="form-control d-inline-block"
                style="width: auto; display: inline-block;">
            <button class="btn btn-primary" id="add_coin">Add Coin</button>
        </div> --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Coin List</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="coin_table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Coin Name</th>
                                    <th>Coin Symbol</th>
                                    <th>Price $USD</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coins as $coin)
                                    <tr data-id="{{ $coin->id }}">
                                        <td>{{ $coin->id }}</td>
                                        <td class="coin-name">{{ $coin->name }}</td>
                                        <td class="coin-symbol">{{ $coin->symbol }}</td>
                                        <td>
                                            <span class="coin-price">{{ $coin->price }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-coin" data-id="{{ $coin->id }}"
                                                data-name="{{ $coin->name }}" data-price="{{ $coin->price }}">
                                                Edit Price
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Edit Price Modal -->
    <div class="modal fade" id="editPriceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Coin Price</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> {{-- BS4 --}}
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{-- สำหรับ BS5 ให้ใช้ปุ่มนี้แทน:
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        --}}
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_coin_id">
                    <div class="form-group">
                        <label>Coin</label>
                        <input type="text" id="edit_coin_name" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_coin_price">Price</label>
                        <input type="number" step="0.01" min="0" id="edit_coin_price" class="form-control"
                            placeholder="Enter new price">
                        <small class="text-muted">ใส่ตัวเลขมากกว่าหรือเท่ากับ 0</small>
                    </div>
                    <div class="alert alert-danger d-none" id="edit_price_error"></div>
                    <div class="alert alert-success d-none" id="edit_price_success"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    {{-- BS4 --}}
                    {{-- BS5: data-bs-dismiss="modal" --}}
                    <button type="button" class="btn btn-primary" id="save_price_btn">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>


    <script>
        // Helper: ตรวจ bootstrap version แล้วเรียก show/hide โมดัลให้ถูก
        function showModal(modalEl) {
            if (window.bootstrap && bootstrap.Modal) { // Bootstrap 5
                const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                instance.show();
            } else {
                // Bootstrap 4 (jQuery required by BS4)
                if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
                    $(modalEl).modal('show');
                } else {
                    console.warn('Bootstrap modal not detected. Please ensure BS4/BS5 JS is loaded.');
                }
            }
        }

        function hideModal(modalEl) {
            if (window.bootstrap && bootstrap.Modal) {
                const instance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                instance.hide();
            } else if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
                $(modalEl).modal('hide');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('editPriceModal');
            const idInput = document.getElementById('edit_coin_id');
            const nameInput = document.getElementById('edit_coin_name');
            const priceInput = document.getElementById('edit_coin_price');
            const errorBox = document.getElementById('edit_price_error');
            const successBox = document.getElementById('edit_price_success');
            const saveBtn = document.getElementById('save_price_btn');

            // เปิด Modal พร้อมเติมค่า
            document.querySelectorAll('.edit-coin').forEach(btn => {
                btn.addEventListener('click', function() {
                    errorBox.classList.add('d-none');
                    errorBox.textContent = '';
                    successBox.classList.add('d-none');
                    successBox.textContent = '';

                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const price = this.dataset.price;

                    idInput.value = id;
                    nameInput.value = name;
                    priceInput.value = price;

                    showModal(modalEl);
                    setTimeout(() => priceInput.focus(), 150);
                });
            });

            // บันทึก
            saveBtn.addEventListener('click', function() {
                const id = idInput.value;
                const newPrice = priceInput.value.trim();

                // validate
                if (newPrice === '' || isNaN(newPrice) || Number(newPrice) < 0) {
                    errorBox.textContent = 'กรุณากรอกราคาเป็นตัวเลขที่ถูกต้อง (≥ 0)';
                    errorBox.classList.remove('d-none');
                    return;
                }

                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving...';

                fetch("{{ route('crypto.update') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            id: id,
                            price: newPrice
                        })
                    })
                    .then(res => res.ok ? res.json() : Promise.reject(res))
                    .then(data => {
                        if (data.success) {
                            // อัปเดตราคาในแถว
                            const row = document.querySelector(`tr[data-id="${id}"]`);
                            if (row) {
                                const priceSpan = row.querySelector('.coin-price');
                                const editBtn = row.querySelector('.edit-coin');
                                if (priceSpan) priceSpan.textContent = newPrice;
                                if (editBtn) editBtn.dataset.price = newPrice;
                            }
                            successBox.textContent = 'อัปเดตราคาเรียบร้อย';
                            successBox.classList.remove('d-none');

                            // ปิดโมดัลหลังแสดงสำเร็จสั้น ๆ
                            setTimeout(() => {
                                hideModal(modalEl);
                                successBox.classList.add('d-none');
                                saveBtn.disabled = false;
                                saveBtn.textContent = 'Save';
                            }, 500);
                        } else {
                            throw new Error(data.message || 'อัปเดตไม่สำเร็จ');
                        }
                    })
                    .catch(async err => {
                        let msg = 'เกิดข้อผิดพลาดระหว่างอัปเดต';
                        // ดึงข้อความจาก response ถ้ามี
                        if (err && err.json) {
                            try {
                                const j = await err.json();
                                msg = j.message || msg;
                            } catch (_) {}
                        }
                        errorBox.textContent = msg;
                        errorBox.classList.remove('d-none');
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Save';
                    });
            });
        });
    </script>
@endsection
