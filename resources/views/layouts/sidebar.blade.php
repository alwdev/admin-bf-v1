<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <div class="navbar-brand-box">
            <a href="/" class="logo">
                <img src="{{ env('APP_LOGO') }}" width="200" style="height: auto;" />
            </a>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="/" class="waves-effect"><i
                            class='bx bx-home'></i><span>{{ __('main.main_page') }}</span></a>
                </li>
                @if (json_decode(auth()->user()->permissions)->member > 1)
                    <li>
                        <a href="{{ route('managemember.index') }}" class=" waves-effect"><i
                                class="bx bx-user-circle"></i><span>{{ __('main.member_manage') }}</span></a>
                        {{-- <ul class="" aria-expanded="false">
                        <li class=""><a href="{{ route('managemember.index') }}" class="active">สมาชิก</a></li>
                    </ul> --}}
                    </li>
                @endif
                <li>
                    {{-- <a href="{{ env('APP_LOTTO_ADMIN_URL') }}/signin/{{ env('APP_CODE') }}/{{ auth()->user()->lotto_login_token }}" target="_blank" class=" waves-effect"><i class="bx bx-purchase-tag-alt"></i><span>Manage Lotto</span></a> --}}
                    <a href="{{ env('APP_LOTTO_ADMIN_URL') }}/site/login?company_code={{ env('LOTTO_COMPANY_CODE') }}&username={{ env('LOTTO_USERNAME') }}&password={{ env('LOTTO_PASSWORD') }}"
                        class=" waves-effect"><i class="bx bx-purchase-tag-alt"></i><span>Manage Lotto</span></a>
                </li>
                @if (json_decode(auth()->user()->permissions)->transfer > 1)
                    <li>
                        <a href="{{ route('managemember.transaction') }}" class="waves-effect"><i
                                class='bx bx-bitcoin'></i><span>{{ __('main.transfer_list') }}</span></a>
                    </li>
                @endif
                @if (json_decode(auth()->user()->permissions)->report > 1)
                    <li>
                        <a href="#" class="waves-effect"><i
                                class="bx bxs-report"></i><span>{{ __('main.report') }}</span></a>
                        <ul class="" aria-expanded="false">
                            <li class=""><a href="https://bo.psg777.com/bo/simpleReport"
                                    class="active">{{ __('main.report') }}</a></li>
                            <li class=""><a href="/member_transfer/0"
                                    class="active">{{ __('main.report_transfer') }}</a></li>
                            <li class=""><a href="{{ route('report.edit_balance') }}"
                                    class="active">{{ __('main.report_edit_credit') }}</a></li>
                            {{-- <li class=""><a href="{{ '/pghard_report/0/0' }}" class="active">PG HARD eport</a></li> --}}
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('report.wrongdeposit') }}" class="waves-effect"><i
                                class='bx bx-bitcoin'></i><span>{{ __('main.report_error_transfert') }}</span></a>
                    </li>
                @endif
                <li class="menu-title">{{ __('main.setting') }}</li>
                <li>
                    <a href="{{ route('bankaccount.index') }}" class="waves-effect"><i
                            class='bx bxs-bank'></i><span>{{ __('main.book_bank') }}</span></a>
                </li>
                <li>
                    <a href="{{ route('sbo.games.index') }}" class="waves-effect"><i
                            class='bx bx-joystick'></i><span>SBO Games</span></a>
                </li>
                <li>
                    <a href="{{ route('sbo.providers.index') }}" class="waves-effect"><i
                            class='bx bx-layer'></i><span>SBO Providers</span></a>
                </li>
                <li>
                    <a href="{{ route('promotion.index') }}" class="waves-effect"><i
                            class='bx bx-purchase-tag-alt'></i><span>{{ __('main.promotion') }}</span></a>
                </li>
                <li>
                    <a href="{{ route('promotion_ads.index') }}" class="waves-effect"><i
                            class='bx bx-star'></i><span>Ads</span></a>
                </li>
                <li>
                    <a href="{{ route('article.index') }}" class="waves-effect"><i
                            class='bx bx-copy'></i><span>{{ __('main.article') }}</span></a>
                </li>
                @if (json_decode(auth()->user()->permissions)->manageuser > 1)

                    <li>
                        <a href="{{ route('partner.index') }}" class="waves-effect"><i
                                class='bx bx-user-circle'></i><span>{{ __('main.partners') }}</span></a>
                    </li>
                    {{-- @if (json_decode(auth()->user()->level) < 2) --}}
                    <li>
                        <a href="{{ route('manageuser.index') }}" class="waves-effect"><i
                                class='bx bx-group'></i><span>{{ __('main.staff_manage') }}</span></a>
                    </li>
                    <li class="li-setting">
                        <a href="#" class="waves-effect menu-setting"><i
                                class='bx bx-cog'></i><span>{{ __('main.setting') }}</span></a>
                        <ul class="" aria-expanded="false">
                            <li class="menu-sub-setting"><a href="{{ route('setting.index') }}"
                                    class="active">{{ __('main.website_setting') }}</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.affiliate') }}"
                                    class="active">{{ __('main.recommend_setting') }}</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.popup') }}"
                                    class="active">{{ __('main.popup_setting') }}</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.level') }}"
                                    class="active">{{ __('main.member_level_setting') }}</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.wheel') }}"
                                    class="active">{{ __('main.wheel_setting') }}</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.deposit_continuously') }}"
                                    class="active">{{ __('main.continuous_deposit_setting') }}</a></li>
                            {{-- <li class="menu-sub-setting"><a href="{{ route('setting.point') }}" class="active">ตั้งค่าการคำนวณแต้ม</a></li> --}}
                            {{-- <li class="menu-sub-setting"><a href="{{ route('setting.ranking') }}" class="active">ตั้งค่าจัดอันดับ</a></li> --}}
                            {{-- <li class="menu-sub-setting"><a href="{{ route('setting.mission') }}" class="active">ตั้งค่าเควสประจำวัน</a></li> --}}
                            <li class="menu-sub-setting"><a href="{{ route('setting.coupon') }}"
                                    class="active">{{ __('main.coupon_setting') }}</a></li>
                            @if (auth()->user()->active == 99)
                                <li class="menu-sub-setting"><a href="{{ route('setting.alert') }}"
                                        class="active">Alert
                                        System</a></li>
                            @endif
                        </ul>
                    </li>
                    {{-- @endif --}}
                @endif
                <li>
                    <a href="document.pdf" target="_blank" class="waves-effect"><i
                            class='bx bx-copy'></i><span>{{ __('main.user_manual') }}</span></a>
                </li>
                <li>
                    <a href="Document_Lotto.pdf" target="_blank" class="waves-effect"><i
                            class='bx bx-copy'></i><span>{{ __('Lotto manual') }}</span></a>
                </li>
                <li>
                    <a href="{{ route('smsLog.index') }}" class="waves-effect"><i class='bx bx-copy'></i><span>SMS
                            Logs</span></a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
