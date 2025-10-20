@extends('layouts.guest')
@section('styles')
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">เพิ่มโปรโมชั่น</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                        <li class="breadcrumb-item active">เพิ่มโปรโมชั่น</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4 mt-3">
                    </div>
                    <form class="p-2" action="{{ route('promotion.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">โปรโมชั่น</label>
                            <input class="form-control" type="text" id="name" name="name" required
                                value="{{ old('name') }}">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div class="form-group">
                            <label for="deposit">ฝาก(บาท)</label>
                            <input class="form-control" type="number" id="deposit" name="deposit" required
                                onkeypress="return isNumberKey(event)" value="{{ old('deposit') }}">
                            <x-input-error :messages="$errors->get('deposit')" class="mt-2" />
                        </div>

                        {{-- === ส่วนของโบนัส/เทิร์น/ถอน (เปลี่ยนแปลงตาม is_percentage_based) === --}}
                        {{-- is_percentage_based (ใช้การคำนวณแบบ %) --}}
                        <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_percentage_based"
                                name="is_percentage_based" value="1" {{ old('is_percentage_based') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_percentage_based">ใช้การคำนวณแบบเปอร์เซ็นต์
                                (Bonus/Turnover/Withdraw Limit)</label>
                            <x-input-error :messages="$errors->get('is_percentage_based')" class="mt-2" />
                        </div>
                        <div class="custom-control custom-checkbox custom-control mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_turnover_x2" name="is_turnover_x2"
                                value="1" {{ old('is_turnover_x2') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_turnover_x2">ใช้ Turnover จากยอดฝาก +
                                โบนัส</label>
                            <x-input-error :messages="$errors->get('is_turnover_x2')" class="mt-2" />
                        </div>

                        {{-- Bonus Field (จะซ่อน/แสดงตาม is_percentage_based) --}}
                        <div class="form-group" id="bonus_amount_group"
                            style="display: {{ old('is_percentage_based') ? 'none' : 'block' }};">
                            <label for="bonus">โบนัส(บาท)</label>
                            <input class="form-control" type="number" id="bonus" name="bonus"
                                onkeypress="return isNumberKey(event)" value="{{ old('bonus') }}">
                            <x-input-error :messages="$errors->get('bonus')" class="mt-2" />
                        </div>

                        {{-- Bonus Percentage Field (จะซ่อน/แสดงตาม is_percentage_based) --}}
                        <div class="form-group" id="bonus_percentage_group"
                            style="display: {{ old('is_percentage_based') ? 'block' : 'none' }};">
                            <label for="bonus_percentage">โบนัส (%)</label>
                            <input class="form-control float-number" type="text" id="bonus_percentage"
                                name="bonus_percentage" value="{{ old('bonus_percentage') }}"
                                placeholder="เช่น 10.00 สำหรับ 10%">
                            <x-input-error :messages="$errors->get('bonus_percentage')" class="mt-2" />
                        </div>

                        {{-- Turnover Field (จะซ่อน/แสดงตาม is_percentage_based) --}}
                        <div class="form-group" id="turnover_amount_group"
                            style="display: {{ old('is_percentage_based') ? 'none' : 'block' }};">
                            <label for="turnover">เทิร์นโอเวอร์ (เท่าของยอด)</label>
                            <input class="form-control float-number" type="text" id="turnover" name="turnover"
                                value="{{ old('turnover') }}">
                            <x-input-error :messages="$errors->get('turnover')" class="mt-2" />
                        </div>

                        {{-- turnover_percentage --}}
                        <div class="form-group" id="turnover_percentage_group"
                            style="display: {{ old('is_percentage_based') ? 'block' : 'none' }};">
                            <label for="turnover_percentage">เทิร์นโอเวอร์ (%)</label>
                            <input class="form-control float-number" type="text" id="turnover_percentage"
                                name="turnover_percentage" value="{{ old('turnover_percentage') }}"
                                placeholder="เช่น 500 สำหรับ 5 เท่า หรือ 50.00 สำหรับ 50%">
                            <x-input-error :messages="$errors->get('turnover_percentage')" class="mt-2" />
                        </div>

                        {{-- Withdraw Limit Field (จะซ่อน/แสดงตาม is_percentage_based) --}}
                        <div class="form-group" id="withdraw_limit_amount_group"
                            style="display: {{ old('is_percentage_based') ? 'none' : 'block' }};">
                            <label for="withdraw_limit">ถอนได้สูงสุด (บาท)</label>
                            <input class="form-control" type="number" id="withdraw_limit" name="withdraw_limit"
                                onkeypress="return isNumberKey(event)" value="{{ old('withdraw_limit') }}">
                            <x-input-error :messages="$errors->get('withdraw_limit')" class="mt-2" />
                        </div>

                        {{-- withdraw_limit_percentage --}}
                        <div class="form-group" id="withdraw_limit_percentage_group"
                            style="display: {{ old('is_percentage_based') ? 'block' : 'none' }};">
                            <label for="withdraw_limit_percentage">ถอนได้สูงสุด (%)</label>
                            <input class="form-control float-number" type="text" id="withdraw_limit_percentage"
                                name="withdraw_limit_percentage" value="{{ old('withdraw_limit_percentage') }}"
                                placeholder="เช่น 100.00 สำหรับ 100%">
                            <x-input-error :messages="$errors->get('withdraw_limit_percentage')" class="mt-2" />
                        </div>
                        {{-- === จบส่วนของโบนัส/เทิร์น/ถอน === --}}

                        {{-- is_recurring_promotion (โปรโมชั่นต่อเนื่อง) --}}
                        <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_recurring_promotion"
                                name="is_recurring_promotion" value="1"
                                {{ old('is_recurring_promotion') ? 'checked' : '' }}>
                            <label class="custom-control-label"
                                for="is_recurring_promotion">เป็นโปรโมชั่นต่อเนื่อง</label>
                            <x-input-error :messages="$errors->get('is_recurring_promotion')" class="mt-2" />
                        </div>

                        {{-- Fields for recurring promotion --}}
                        <div id="recurring_promotion_fields"
                            style="display: {{ old('is_recurring_promotion') ? 'block' : 'none' }};">
                            {{-- recurring_promotion_days --}}
                            <div class="form-group" id="recurring_days_group">
                                <label for="recurring_promotion_days">จำนวนวันโปรโมชั่นต่อเนื่อง (นับจากวันสมัคร)</label>
                                <input class="form-control" type="number" id="recurring_promotion_days"
                                    name="recurring_promotion_days" onkeypress="return isNumberKey(event)"
                                    value="{{ old('recurring_promotion_days') }}">
                                <x-input-error :messages="$errors->get('recurring_promotion_days')" class="mt-2" />
                            </div>

                            {{-- recurring_bonus_percentage --}}
                            <div class="form-group" id="recurring_bonus_percentage_group">
                                <label for="recurring_bonus_percentage">โบนัสต่อเนื่อง (%)</label>
                                <input class="form-control float-number" type="text" id="recurring_bonus_percentage"
                                    name="recurring_bonus_percentage" value="{{ old('recurring_bonus_percentage') }}"
                                    placeholder="เช่น 20.00 สำหรับ 20%">
                                <x-input-error :messages="$errors->get('recurring_bonus_percentage')" class="mt-2" />
                            </div>

                            {{-- *** NEW: recurring_turnover *** --}}
                            {{-- <div class="form-group" id="recurring_turnover_group">
                                <label for="recurring_turnover">Turnover ต่อเนื่อง (จำนวนเท่า)</label>
                                <input class="form-control" type="number" step="0.01" id="recurring_turnover" name="recurring_turnover" value="{{ old('recurring_turnover') }}" placeholder="เช่น 3 สำหรับ 3 เท่า">
                                <x-input-error :messages="$errors->get('recurring_turnover')" class="mt-2" />
                            </div> --}}

                            {{-- *** NEW: recurring_turnover_percentage *** --}}
                            <div class="form-group" id="recurring_turnover_percentage_group">
                                <label for="recurring_turnover_percentage">Turnover ต่อเนื่อง
                                    (เป็นเปอร์เซ็นต์ของยอดเงินรวม)</label>
                                <input class="form-control float-number" type="text"
                                    id="recurring_turnover_percentage" name="recurring_turnover_percentage"
                                    value="{{ old('recurring_turnover_percentage') }}"
                                    placeholder="เช่น 300 สำหรับ 300% ของ (ยอดฝาก+โบนัส)">
                                <x-input-error :messages="$errors->get('recurring_turnover_percentage')" class="mt-2" />
                            </div>
                        </div>


                        {{-- applicable_games --}}
                        <div class="form-group">
                            <label for="applicable_games">ใช้ได้กับเกม</label>
                            <select class="form-control select2-multi" id="applicable_games" name="applicable_games[]"
                                multiple="multiple" required>
                                @php
                                    $gameOptions = [
                                        'ทั้งหมด',
                                        'สล็อต',
                                        'คาสิโนสด',
                                        'ยิงปลา',
                                        'เกมส์ไพ่',
                                        'หวย',
                                        'กีฬา',
                                    ];
                                    $oldSelectedGames = old('applicable_games', ['ทั้งหมด']);
                                @endphp
                                @foreach ($gameOptions as $game)
                                    <option value="{{ $game }}"
                                        {{ in_array($game, $oldSelectedGames) ? 'selected' : '' }}>
                                        {{ $game }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('applicable_games')" class="mt-2" />
                            <x-input-error :messages="$errors->get('applicable_games.*')" class="mt-2" />
                        </div>

                        <div class="form-group">
                            <label for="image">รูปภาพ (ขนาด 400x400px)</label>
                            <input class="form-control" type="file" id="image" name="image" required
                                value="{{ old('image') }}" accept="image/jpeg,image/gif,image/png">
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                        <div class="form-group">
                            <label for="description">รายละเอียด</label>
                            <textarea class="form-control" id="description" rows="15" name="description">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_newuser" name="is_newuser"
                                value="1" {{ old('is_newuser') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_newuser">เฉพาะผู้เล่นใหม่</label>
                            <x-input-error :messages="$errors->get('is_newuser')" class="mt-2" />
                        </div>
                        {{-- เพิ่ม is_first_deposit_bonus --}}
                        {{-- <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_first_deposit_bonus" name="is_first_deposit_bonus" value="1" {{ old('is_first_deposit_bonus') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_first_deposit_bonus">สำหรับยอดฝากครั้งแรก</label>
                            <x-input-error :messages="$errors->get('is_first_deposit_bonus')" class="mt-2" />
                        </div> --}}

                        <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="enable" name="enable" checked
                                value="1" {{ old('enable', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable">Enable (เผยแพร่)</label>
                            <x-input-error :messages="$errors->get('enable')" class="mt-2" />
                        </div>
                        <div class="mb-3 text-center">
                            <button class="btn btn-primary btn-block" type="submit"> บันทึก </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('pages/datatables-demo.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#bank_name').change(function() {
                if ($('option:selected').val() == '') {
                    $('#bank-logo').html('');
                    $('#bank_logo').val('');
                } else {
                    $('#bank-logo').html('<img src="' + $('option:selected').attr('data-logo') +
                        '" alt="" width="54"/>');
                    $('#bank_logo').val($('option:selected').attr('data-img'));
                }
            });

            $('.select2-multi').select2({
                placeholder: "เลือกเกมที่ใช้ได้",
                allowClear: true
            });

            // Logic to show/hide recurring_promotion_days AND recurring_bonus_percentage AND recurring_turnover fields based on is_recurring_promotion checkbox
            $('#is_recurring_promotion').change(function() {
                if ($(this).is(':checked')) {
                    $('#recurring_promotion_fields').slideDown();
                } else {
                    $('#recurring_promotion_fields').slideUp();
                    // Clear values from hidden recurring fields
                    $('#recurring_promotion_days').val('');
                    $('#recurring_bonus_percentage').val('');
                    $('#recurring_turnover').val('');
                    $('#recurring_turnover_percentage').val('');
                }
            });

            // Logic to show/hide amount/percentage fields based on is_percentage_based checkbox
            $('#is_percentage_based').change(function() {
                if ($(this).is(':checked')) {
                    // Show percentage fields, hide amount fields
                    $('#bonus_percentage_group').slideDown();
                    $('#turnover_percentage_group').slideDown();
                    $('#withdraw_limit_percentage_group').slideDown();

                    $('#bonus_amount_group').slideUp();
                    $('#turnover_amount_group').slideUp();
                    $('#withdraw_limit_amount_group').slideUp();

                    // Clear values from hidden "amount" fields
                    $('#bonus').val('');
                    $('#turnover').val('');
                    $('#withdraw_limit').val('');
                } else {
                    // Show amount fields, hide percentage fields
                    $('#bonus_percentage_group').slideUp();
                    $('#turnover_percentage_group').slideUp();
                    $('#withdraw_limit_percentage_group').slideUp();

                    $('#bonus_amount_group').slideDown();
                    $('#turnover_amount_group').slideDown();
                    $('#withdraw_limit_amount_group').slideDown();

                    // Clear values from hidden "percentage" fields
                    $('#bonus_percentage').val('');
                    $('#turnover_percentage').val('');
                    $('#withdraw_limit_percentage').val('');
                }
            });

            // Trigger change on load for old values to ensure correct initial display
            // This is crucial if old('is_recurring_promotion') or old('is_percentage_based') is true
            // as it will set the display property from 'none' to 'block'
            $('#is_recurring_promotion').trigger('change');
            $('#is_percentage_based').trigger('change');

        });

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
        jQuery(document).ready(function() {
            $('.float-number').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event
                        .which > 57)) {
                    event.preventDefault();
                }
            });
        });

        function showImage(image) {
            Swal.fire({
                imageUrl: image,
                imageHeight: 500,
                imageAlt: "A tall image"
            });
        }
    </script>
@endsection
