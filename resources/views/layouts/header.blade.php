<html lang="{{ session('lang', 'en') }}">

<header id="page-topbar">
    <div class="navbar-header">

        <div class="d-flex align-items-left">
            <button type="button" class="btn btn-sm mr-2 d-lg-none px-3 font-size-16 header-item waves-effect"
                id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="dropdown d-none d-sm-inline-block" style="margin-left: 4px">
                <h4>Agent Back Office v.2</h4>
            </div>
        </div>

        <div class="d-flex align-items-center">

            <div class="dropdown d-none d-sm-inline-block ml-2">

            </div>

            <div class="dropdown d-inline-block">

            </div>

            <div class="dropdown d-inline-block">

            </div>
            <a href="/chat">
                <span id="chat_count" style="color: rgb(13, 110, 253); font-weight: 600; margin:12px;"></span>
            </a>
            <span style="color: rgb(20, 179, 5);" id="tran_count">--</span>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ asset('images/users/avatar-1.jpg') }}"
                        alt="Header Avatar">
                    <span class="d-none d-sm-inline-block ml-1">{{ Auth::user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                        href="{{ route('profile.edit') }}">Profile</a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:void(0)"
                        onclick="$('#from-logout').submit()">
                        <span>Log Out</span>
                        <form action="{{ route('logout') }}" id="from-logout" method="post">@csrf</form>
                    </a>
                </div>
            </div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-lang-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @if (session()->has('locale'))
                        @if (session()->get('locale') == 'en')
                            <img class="rounded-circle header-profile-user" src="/images/auth/icon-en.svg"alt="">
                            <span class="d-none d-sm-inline-block ml-1">EN</span>
                        @elseif(session()->get('locale') == 'th')
                            <img class="rounded-circle header-profile-user" src="/images/auth/icon-th.svg"alt="">
                            <span class="d-none d-sm-inline-block ml-1">TH</span>
                        @elseif(session()->get('locale') == 'lo')
                            <img class="rounded-circle header-profile-user" src="/images/auth/icon-lo.svg"alt="">
                            <span class="d-none d-sm-inline-block ml-1">LO</span>
                        @elseif(session()->get('locale') == 'vn')
                            <img class="rounded-circle header-profile-user" src="/images/auth/vn.png"alt="">
                            <span class="d-none d-sm-inline-block ml-1">VN</span>
                        @elseif(session()->get('locale') == 'ko')
                            <img class="rounded-circle header-profile-user" src="/images/auth/ko.png"alt="">
                            <span class="d-none d-sm-inline-block ml-1">KO</span>
                        @elseif(session()->get('locale') == 'jp')
                            <img class="rounded-circle header-profile-user" src="/images/auth/jp.png"alt="">
                            <span class="d-none d-sm-inline-block ml-1">JP</span>
                        @elseif(session()->get('locale') == 'zh')
                            <img class="rounded-circle header-profile-user" src="/images/auth/zh.png"alt="">
                            <span class="d-none d-sm-inline-block ml-1">ZH</span>
                        @else
                            @php
                                Session::put('locale', 'en');
                            @endphp
                            <img class="rounded-circle header-profile-user" src="/images/auth/icon-en.svg"alt="">
                            <span class="d-none d-sm-inline-block ml-1">EN</span>
                        @endif
                    @else
                        @php
                            Session::put('locale', 'en');
                        @endphp
                        <img class="rounded-circle header-profile-user" src="/images/auth/icon-en.svg"alt="">
                        <span class="d-none d-sm-inline-block ml-1">EN</span>
                    @endif

                    <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">


                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                        href="{{ route('change.lang', ['lang' => 'en']) }}" data-icon="/images/united-kingdom.svg">
                        <img src="/images/auth/icon-en.svg" alt="">
                        <span>English</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                        href="{{ route('change.lang', ['lang' => 'th']) }}" data-icon="/images/thailand.svg">
                        <img src="/images/auth/icon-th.svg" alt="">
                        <span>ภาษาไทย</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                        href="{{ route('change.lang', ['lang' => 'lo']) }}" data-icon="">
                        <img src="/images/auth/icon-lo.svg" alt="" style="width: 24px">
                        <span>ພາສາລາວ</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                        href="{{ route('change.lang', ['lang' => 'vn']) }}" data-icon="">
                        <img src="/images/auth/vn.png" alt="" style="width: 24px">
                        <span>Tiếng Việt</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                        href="{{ route('change.lang', ['lang' => 'ko']) }}" data-icon="">
                        <img src="/images/auth/ko.png" alt="" style="width: 24px">
                        <span>한국어</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between"
                        href="{{ route('change.lang', ['lang' => 'jp']) }}" data-icon="">
                        <img src="/images/auth/jp.png" alt="" style="width: 24px">
                        <span>日本語</span>
                    </a>
                    {{-- <a class="dropdown-item d-flex align-items-center justify-content-between"
                        href="{{ route('change.lang', ['lang' => 'zh']) }}" data-icon="/images/auth/zh.png">
                        <img src="/images/auth/zh.png" alt="" style="width: 34px">
                        <span>简体中文</span>
                    </a> --}}
                </div>
            </div>
        </div>
    </div>
</header>

<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.1.1/howler.js"></script>
<script>
    var count_event_lose = 0;
    var count_event = 0;
    if (sessionStorage.getItem("count_event") === null) {
        sessionStorage.setItem("count_event", 0);
    } else {
        count_event_lose = sessionStorage.getItem("count_event");
    }
    var count_chat = 0;
    if (sessionStorage.getItem("count_chat") === null) {
        sessionStorage.setItem("count_chat", 0);
    } else {
        count_chat = sessionStorage.getItem("count_chat");
    }

    setInterval(function() {
        $.ajax({
            type: 'get',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('report.count_last_tranfer') }}',
            success: function(data) {
                if (data) {

                    // console.log(count_event_lose);
                    if (parseInt(data) != 0) {
                        if (parseInt(data) != count_event_lose && count_event < parseInt(data)) {
                            $('#tran_count').text('{{ __('dashboard.AwaitingApproval') }}');
                            var alarm = new Howl({
                                src: ["{{ asset('noti.mp3?002') }}"],
                                autoplay: false,
                                loop: false,
                                // volume: 0.5,
                            });
                            alarm.play();
                            count_event = count_event + 1;
                        }
                        count_event_lose = parseInt(data);
                        sessionStorage.setItem("count_event", parseInt(data));
                    } else {
                        sessionStorage.setItem("count_event", 0);
                        count_event_lose = 0;
                        count_event = 0;
                    }
                } else {
                    console.log('error');
                }
            }
        });

    }, 2000);

    setInterval(function() {
        console.log("count_chat ", count_chat);
        $.ajax({
            type: 'get',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('chat.conversations.count') }}',
            success: function(data) {
                if (data) {
                    console.log("data ",data)
                    if (parseInt(data) != 0) {
                        if (parseInt(data) != count_chat) {
                            var alarm = new Howl({
                                src: ["{{ asset('noti.mp3?002') }}"],
                                autoplay: false,
                                loop: false,
                                // volume: 0.5,
                            });
                            alarm.play();
                            count_chat = parseInt(data);

                            $('#chat_count').text('You have ' + parseInt(data) + ' new chats');
                            sessionStorage.setItem("count_chat", parseInt(data));

                        }
                    } else {
                        $('#chat_count').text('');
                    }
                } else {
                    console.log('error');
                }
            }
        });

    }, 2000);
</script>
