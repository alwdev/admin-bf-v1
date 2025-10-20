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
                <h4 class="mb-0 font-size-18">{{ __('main.Edit Promotion') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">{{ __('main.Edit Promotion') }}</li>
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

                    {{-- **เพิ่มส่วนนี้สำหรับแสดง Validation Errors รวม** --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>มีข้อผิดพลาดเกิดขึ้น!</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    {{-- **สิ้นสุดส่วน Validation Errors รวม** --}}

                    <form class="p-2" action="{{ route('promotion.update', $promotion->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- Laravel ใช้ method spoofing สำหรับ PUT/PATCH requests --}}
                        @method('PUT') {{-- *** สำคัญมาก! ต้องมี @method('PUT') สำหรับการอัปเดต *** --}}

                        <div class="form-group">
                            <label for="name">{{ __('main.promotion') }}</label>
                            <input class="form-control" type="text" id="name" name="name" required
                                value="{{ old('name', $promotion->name) }}">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div class="form-group">
                            <label for="deposit">{{ __('dashboard.deposit') }}</label>
                            <input class="form-control" type="number" id="deposit" name="deposit" required
                                onkeypress="return isNumberKey(event)" value="{{ old('deposit', $promotion->deposit) }}">
                            <x-input-error :messages="$errors->get('deposit')" class="mt-2" />
                        </div>

                        {{-- === ส่วนของโบนัส/เทิร์น/ถอน (เปลี่ยนแปลงตาม is_percentage_based) === --}}
                        {{-- is_percentage_based (ใช้การคำนวณแบบ %) --}}
                        <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_percentage_based"
                                name="is_percentage_based" value="1"
                                {{ old('is_percentage_based', $promotion->is_percentage_based) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_percentage_based">ใช้การคำนวณแบบเปอร์เซ็นต์
                                (Bonus/Turnover/Withdraw Limit)</label>
                            <x-input-error :messages="$errors->get('is_percentage_based')" class="mt-2" />
                        </div>
                        <div class="custom-control custom-checkbox custom-control mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_turnover_x2" name="is_turnover_x2"
                                value="1" {{ old('is_turnover_x2', $promotion->is_turnover_x2) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_turnover_x2">ใช้ Turnover จากยอดฝาก +
                                โบนัส</label>
                            <x-input-error :messages="$errors->get('is_turnover_x2')" class="mt-2" />
                        </div>

                        {{-- Bonus Field (จะซ่อน/แสดงตาม is_percentage_based) --}}
                        <div class="form-group" id="bonus_amount_group"
                            style="display: {{ old('is_percentage_based', $promotion->is_percentage_based) ? 'none' : 'block' }};">
                            <label for="bonus">โบนัส(บาท)</label>
                            <input class="form-control" type="number" id="bonus" name="bonus"
                                onkeypress="return isNumberKey(event)" value="{{ old('bonus', $promotion->bonus) }}">
                            <x-input-error :messages="$errors->get('bonus')" class="mt-2" />
                        </div>

                        {{-- Bonus Percentage Field (จะซ่อน/แสดงตาม is_percentage_based) --}}
                        <div class="form-group" id="bonus_percentage_group"
                            style="display: {{ old('is_percentage_based', $promotion->is_percentage_based) ? 'block' : 'none' }};">
                            <label for="bonus_percentage">โบนัส (%)</label>
                            <input class="form-control float-number" type="text" id="bonus_percentage"
                                name="bonus_percentage" value="{{ old('bonus_percentage', $promotion->bonus_percentage) }}"
                                placeholder="เช่น 10.00 สำหรับ 10%">
                            <x-input-error :messages="$errors->get('bonus_percentage')" class="mt-2" />
                        </div>

                        {{-- Turnover Field (จะซ่อน/แสดงตาม is_percentage_based) --}}
                        <div class="form-group" id="turnover_amount_group"
                            style="display: {{ old('is_percentage_based', $promotion->is_percentage_based) ? 'none' : 'block' }};">
                            <label for="turnover">เทิร์นโอเวอร์ (เท่าของยอด)</label>
                            <input class="form-control float-number" type="text" id="turnover" name="turnover"
                                value="{{ old('turnover', $promotion->turnover) }}">
                            <x-input-error :messages="$errors->get('turnover')" class="mt-2" />
                        </div>

                        {{-- turnover_percentage --}}
                        <div class="form-group" id="turnover_percentage_group"
                            style="display: {{ old('is_percentage_based', $promotion->is_percentage_based) ? 'block' : 'none' }};">
                            <label for="turnover_percentage">เทิร์นโอเวอร์ (%)</label>
                            <input class="form-control float-number" type="text" id="turnover_percentage"
                                name="turnover_percentage"
                                value="{{ old('turnover_percentage', $promotion->turnover_percentage) }}"
                                placeholder="เช่น 500 สำหรับ 5 เท่า หรือ 50.00 สำหรับ 50%">
                            <x-input-error :messages="$errors->get('turnover_percentage')" class="mt-2" />
                        </div>

                        {{-- Withdraw Limit Field (จะซ่อน/แสดงตาม is_percentage_based) --}}
                        <div class="form-group" id="withdraw_limit_amount_group"
                            style="display: {{ old('is_percentage_based', $promotion->is_percentage_based) ? 'none' : 'block' }};">
                            <label for="withdraw_limit">ถอนได้สูงสุด (บาท)</label>
                            <input class="form-control" type="number" id="withdraw_limit" name="withdraw_limit"
                                onkeypress="return isNumberKey(event)"
                                value="{{ old('withdraw_limit', $promotion->withdraw_limit) }}">
                            <x-input-error :messages="$errors->get('withdraw_limit')" class="mt-2" />
                        </div>

                        {{-- withdraw_limit_percentage --}}
                        <div class="form-group" id="withdraw_limit_percentage_group"
                            style="display: {{ old('is_percentage_based', $promotion->is_percentage_based) ? 'block' : 'none' }};">
                            <label for="withdraw_limit_percentage">ถอนได้สูงสุด (%)</label>
                            <input class="form-control float-number" type="text" id="withdraw_limit_percentage"
                                name="withdraw_limit_percentage"
                                value="{{ old('withdraw_limit_percentage', $promotion->withdraw_limit_percentage) }}"
                                placeholder="เช่น 100.00 สำหรับ 100%">
                            <x-input-error :messages="$errors->get('withdraw_limit_percentage')" class="mt-2" />
                        </div>
                        {{-- === จบส่วนของโบนัส/เทิร์น/ถอน === --}}

                        {{-- is_recurring_promotion (โปรโมชั่นต่อเนื่อง) --}}
                        <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_recurring_promotion"
                                name="is_recurring_promotion" value="1"
                                {{ old('is_recurring_promotion', $promotion->is_recurring_promotion) ? 'checked' : '' }}>
                            <label class="custom-control-label"
                                for="is_recurring_promotion">เป็นโปรโมชั่นต่อเนื่อง</label>
                            <x-input-error :messages="$errors->get('is_recurring_promotion')" class="mt-2" />
                        </div>

                        {{-- recurring_promotion_days --}}
                        <div class="form-group" id="recurring_days_group"
                            style="display: {{ old('is_recurring_promotion', $promotion->is_recurring_promotion) ? 'block' : 'none' }};">
                            <label for="recurring_promotion_days">จำนวนวันโปรโมชั่นต่อเนื่อง</label>
                            <input class="form-control" type="number" id="recurring_promotion_days"
                                name="recurring_promotion_days" onkeypress="return isNumberKey(event)"
                                value="{{ old('recurring_promotion_days', $promotion->recurring_promotion_days) }}">
                            <x-input-error :messages="$errors->get('recurring_promotion_days')" class="mt-2" />
                        </div>

                        {{-- เพิ่ม recurring_bonus_percentage --}}
                        <div class="form-group" id="recurring_bonus_percentage_group"
                            style="display: {{ old('is_recurring_promotion', $promotion->is_recurring_promotion) ? 'block' : 'none' }};">
                            <label for="recurring_bonus_percentage">โบนัสต่อเนื่อง (%)</label>
                            <input class="form-control float-number" type="text" id="recurring_bonus_percentage"
                                name="recurring_bonus_percentage"
                                value="{{ old('recurring_bonus_percentage', $promotion->recurring_bonus_percentage) }}"
                                placeholder="เช่น 20.00 สำหรับ 20%">
                            <x-input-error :messages="$errors->get('recurring_bonus_percentage')" class="mt-2" />
                        </div>

                        {{-- *** NEW: recurring_turnover_amount_group (สำหรับเทิร์นต่อเนื่องแบบบาท/เท่า) *** --}}
                        {{-- <div class="form-group" id="recurring_turnover_amount_group" style="display: {{ old('is_recurring_promotion', $promotion->is_recurring_promotion) ? 'block' : 'none' }};">
                            <label for="recurring_turnover">เทิร์นโอเวอร์ต่อเนื่อง (เท่าของยอด)</label>
                            <input class="form-control float-number" type="text" id="recurring_turnover" name="recurring_turnover" value="{{ old('recurring_turnover', $promotion->recurring_turnover) }}">
                            <x-input-error :messages="$errors->get('recurring_turnover')" class="mt-2" />
                        </div> --}}

                        {{-- *** NEW: recurring_turnover_percentage_group (สำหรับเทิร์นต่อเนื่องแบบเปอร์เซ็นต์) *** --}}
                        <div class="form-group" id="recurring_turnover_percentage_group"
                            style="display: {{ old('is_recurring_promotion', $promotion->is_recurring_promotion) ? 'block' : 'none' }};">
                            <label for="recurring_turnover_percentage">เทิร์นโอเวอร์ต่อเนื่อง (%)</label>
                            <input class="form-control float-number" type="text" id="recurring_turnover_percentage"
                                name="recurring_turnover_percentage"
                                value="{{ old('recurring_turnover_percentage', $promotion->recurring_turnover_percentage) }}"
                                placeholder="เช่น 500 สำหรับ 5 เท่า หรือ 50.00 สำหรับ 50%">
                            <x-input-error :messages="$errors->get('recurring_turnover_percentage')" class="mt-2" />
                        </div>

                        {{-- applicable_games --}}
                        <div class="form-group">
                            <label for="applicable_games">{{ __('main.Applicable to games') }}</label>
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
                                    // แปลง $promotion->applicable_games ให้เป็น array ที่ถูกต้องก่อนนำไปใช้
                                    // ตรวจสอบว่า $promotion->applicable_games เป็น array อยู่แล้วหรือไม่
                                    $selectedGames = is_array($promotion->applicable_games)
                                        ? $promotion->applicable_games
                                        : [];
                                    // ใช้ old() เพื่อให้ค่าเดิมถูกเลือกเมื่อ validation ไม่ผ่าน
                                    $selectedGames = old('applicable_games', $selectedGames);
                                @endphp
                                @foreach ($gameOptions as $game)
                                    <option value="{{ $game }}"
                                        {{ in_array($game, $selectedGames) ? 'selected' : '' }}>
                                        {{ $game }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('applicable_games')" class="mt-2" />
                            <x-input-error :messages="$errors->get('applicable_games.*')" class="mt-2" />
                        </div>

                        <div class="form-group">
                            <label for="image">{{ __('main.Image (size 400x400px)') }}</label><br>
                            @if ($promotion->image)
                                <img src="{{ $promotion->image }}" class="img-thumbnail rounded"
                                    style="height:200px;cursor: pointer;" onclick="showImage('{{ $promotion->image }}')">
                            @endif
                            <input class="form-control" type="file" id="image" name="image"
                                value="{{ old('image') }}"
                                accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps">
                            {{-- หากมี error จาก image validation จะแสดงที่นี่ --}}
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                        <div class="form-group">
                            <label for="description">{{ __('main.details') }}</label>
                            <textarea class="form-control" id="description" rows="15" name="description">{{ old('description', $promotion->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_newuser" name="is_newuser"
                                value="1" {{ old('is_newuser', $promotion->is_newuser) ? 'checked' : '' }}>
                            <label class="custom-control-label"
                                for="is_newuser">{{ __('main.For new players only') }}</label>
                            <x-input-error :messages="$errors->get('is_newuser')" class="mt-2" />
                        </div>
                        {{-- เพิ่ม is_first_deposit_bonus --}}
                        {{-- <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_first_deposit_bonus" name="is_first_deposit_bonus" value="1" {{ old('is_first_deposit_bonus', $promotion->is_first_deposit_bonus) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_first_deposit_bonus">สำหรับยอดฝากครั้งแรก</label>
                            <x-input-error :messages="$errors->get('is_first_deposit_bonus')" class="mt-2" />
                        </div> --}}

                        <div class="custom-control custom-checkbox custom-control-inline mb-3">
                            <input type="checkbox" class="custom-control-input" id="enable" name="enable"
                                value="1" {{ old('enable', $promotion->enable) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable">Enable ({{ __('main.Publish') }})</label>
                            <x-input-error :messages="$errors->get('enable')" class="mt-2" />
                        </div>
                        <div class="mb-3 text-center">
                            <button class="btn btn-primary btn-block" type="submit"> {{ __('main.save') }} </button>
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
            // Initialise Select2 สำหรับ applicable_games
            $('.select2-multi').select2({
                placeholder: "เลือกเกมที่ใช้ได้",
                allowClear: true
            });

            // Logic to show/hide recurring_promotion fields based on is_recurring_promotion checkbox
            $('#is_recurring_promotion').change(function() {
                if ($(this).is(':checked')) {
                    $('#recurring_days_group').slideDown();
                    $('#recurring_bonus_percentage_group').slideDown();
                    // *** NEW: แสดงช่อง recurring turnover ด้วย ***
                    $('#recurring_turnover_amount_group').slideDown();
                    $('#recurring_turnover_percentage_group').slideDown();

                } else {
                    $('#recurring_days_group').slideUp();
                    $('#recurring_bonus_percentage_group').slideUp();
                    // *** NEW: ซ่อนช่อง recurring turnover ด้วย ***
                    $('#recurring_turnover_amount_group').slideUp();
                    $('#recurring_turnover_percentage_group').slideUp();

                    // Clear values if hidden
                    $('#recurring_promotion_days').val('');
                    $('#recurring_bonus_percentage').val('');
                    // *** NEW: Clear ค่า recurring turnover ด้วย ***
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

            // Trigger change on load for initial display based on current values (or old input if validation failed)
            $('#is_recurring_promotion').trigger('change');
            $('#is_percentage_based').trigger('change');

        }); // End $(document).ready()

        // Function สำหรับเช็คตัวเลข
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        // Function สำหรับเช็คตัวเลขทศนิยม
        jQuery(document).ready(function() {
            $('.float-number').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event
                        .which > 57)) {
                    event.preventDefault();
                }
            });
        });

        // Function แสดงรูปภาพใน SweetAlert
        function showImage(image) {
            Swal.fire({
                imageUrl: image,
                imageHeight: 500,
                imageAlt: "A tall image"
            });
        }
    </script>
@endsection
