@extends('layouts.guest')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">SBO Bet Settings</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">/</a></li>
                        <li class="breadcrumb-item active">SBO Bet Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ตั้งค่าลิมิตการเดิมพัน SBO (Agent Preset Bet Settings)</h4>
                    <p class="card-title-desc">
                        การตั้งค่านี้จะถูกนำไปใช้กับ Agent เพื่อกำหนดค่าเริ่มต้นสำหรับสมาชิกใหม่
                    </p>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

                    <div class="d-flex align-items-center mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="btn-check-balance">
                            <i class="bx bx-refresh"></i> เช็คยอด Agent
                        </button>
                        <span id="agent-balance-text" class="text-muted">Balance: -</span>
                    </div>

                    <form id="sbo-settings-form" action="{{ route('sbo.settings.update') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label for="min" class="col-sm-2 col-form-label">Min Bet</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="min" name="min"
                                    value="{{ old('min', 50) }}" required min="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="max" class="col-sm-2 col-form-label">Max Bet</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="max" name="max"
                                    value="{{ old('max', 500000) }}" required min="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="max_per_match" class="col-sm-2 col-form-label">Max Per Match</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="max_per_match" name="max_per_match"
                                    value="{{ old('max_per_match', 500000) }}" required min="0">
                                <small class="form-text text-muted">ต้องมากกว่าหรือเท่ากับ Max Bet</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="casino_table_limit" class="col-sm-2 col-form-label">Casino Table Limit</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="casino_table_limit" name="casino_table_limit">
                                    <option value="1" {{ old('casino_table_limit') == 1 ? 'selected' : '' }}>1: Low
                                    </option>
                                    <option value="2" {{ old('casino_table_limit') == 2 ? 'selected' : '' }}>2: Medium
                                    </option>
                                    <option value="3" {{ old('casino_table_limit') == 3 ? 'selected' : '' }}>3: High
                                    </option>
                                    <option value="4" {{ old('casino_table_limit', 4) == 4 ? 'selected' : '' }}>4:
                                        VIP(ALL)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary" id="btn-save">
                                    <i class="bx bx-save"></i> บันทึกการตั้งค่า
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function fetchAgentBalance() {
            const btn = document.getElementById('btn-check-balance');
            const label = document.getElementById('agent-balance-text');
            btn.disabled = true;
            const old = btn.innerHTML;
            btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> กำลังเช็ค...';

            // Check if username is provided in URL query parameters
            const urlParams = new URLSearchParams(window.location.search);
            const usernameParam = urlParams.get('username');
            let url = '{{ route('sbo.settings.balance') }}';
            if (usernameParam) {
                url += '?username=' + encodeURIComponent(usernameParam);
            }

            fetch(url)
                .then(r => r.json())
                .then(j => {
                    if (j.success) {
                        label.textContent = `User: ${j.username} | Balance: ${j.balance} ${j.currency || ''} | Outstanding: ${j.outstanding}`;
                    } else {
                        label.textContent = `Balance: - (Error: ${j.message || 'unknown'})`;
                    }
                })
                .catch(e => {
                    label.textContent = 'Balance: - (เชื่อมต่อไม่สำเร็จ)';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = old;
                });
        }
        document.getElementById('btn-check-balance').addEventListener('click', fetchAgentBalance);
        // auto load on page open
        fetchAgentBalance();

        document.getElementById('sbo-settings-form').addEventListener('submit', function(e) {
            var confirmation = confirm('คุณแน่ใจหรือไม่ที่จะอัปเดตการตั้งค่านี้?');

            if (!confirmation) {
                e.preventDefault();
                return false;
            }

            // ถ้ากดยืนยัน ให้ปิดปุ่มและแสดงสถานะ Loading
            var btn = document.getElementById('btn-save');
            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader bx-spin font-size-16 align-middle mr-2"></i> กำลังบันทึก...';

            // ปล่อยให้ form submit ทำงานต่อ
            return true;
        });
    </script>
@endsection
