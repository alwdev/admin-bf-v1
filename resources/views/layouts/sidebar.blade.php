<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <div class="navbar-brand-box">
            <a href="/" class="logo">
                <img src="{{ asset('logo_L.png') }}" width="200" style="height: auto;"/>
            </a>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="/" class="waves-effect"><i class='bx bx-home'></i><span>หน้าหลัก</span></a>
                </li>
                @if( json_decode(auth()->user()->permissions)->member > 1  )
                <li>
                    <a href="{{ route('managemember.index') }}" class=" waves-effect"><i class="bx bx-user-circle"></i><span>จัดการสมาชิก</span></a>
                    {{-- <ul class="" aria-expanded="false">
                        <li class=""><a href="{{ route('managemember.index') }}" class="active">สมาชิก</a></li>
                    </ul> --}}
                </li>
                @endif
                @if( json_decode(auth()->user()->permissions)->transfer > 1  )
                <li>
                    <a href="{{ route('managemember.transaction') }}" class="waves-effect"><i class='bx bx-bitcoin'></i><span>รายการฝากถอน</span></a>
                </li>
                @endif
                @if( json_decode(auth()->user()->permissions)->report > 1  )
                <li>
                    <a href="#" class="waves-effect"><i class="bx bxs-report"></i><span>รายงาน</span></a>
                    <ul class="" aria-expanded="false">
                        <li class=""><a href="https://bo.psg777.com/bo/simpleReport" class="active">รายงาน</a></li>
                        {{-- <li class=""><a href="{{ route('report.memberplay_v2') }}" class="active">รายงานการเล่น</a></li> --}}
                        <li class=""><a href="{{ route('report.edit_balance') }}" class="active">รายงานการแก้ไขยอดเงิน</a></li>
                        {{-- <li class=""><a href="{{ '/sumtrans/0' }}" class="active">รายงานธุรกรรมโดยรวม</a></li> --}}

                    </ul>
                </li>
                <li>
                    <a href="{{ route('report.wrongdeposit') }}" class="waves-effect"><i class='bx bx-bitcoin'></i><span>รายการฝากผิดพลาด</span></a>
                </li>
                @endif
                <li class="menu-title">ตั้งค่า</li>
                <li>
                    <a href="{{ route('bankaccount.index') }}" class="waves-effect"><i class='bx bxs-bank'></i><span>สมุดบัญชีธนาคาร</span></a>
                </li>
                <li>
                    <a href="{{ route('provider.index') }}" class="waves-effect"><i class='bx bx-joystick'></i><span>ค่ายเกม</span></a>
                </li>
                <li>
                    <a href="{{ route('promotion.index') }}" class="waves-effect"><i class='bx bx-purchase-tag-alt'></i><span>โปรโมชั่น</span></a>
                </li>
                <li>
                    <a href="{{ route('promotion_ads.index') }}" class="waves-effect"><i class='bx bx-star'></i><span>Ads</span></a>
                </li>
                @if( json_decode(auth()->user()->permissions)->manageuser > 1  )

                {{-- <li>
                    <a href="{{ route('setting.index') }}" class="waves-effect"><i class='bx bx-cog'></i><span>ตั้งค่า</span></a>
                </li> --}}
                    {{-- @if( json_decode(auth()->user()->level) < 2  ) --}}
                    <li>
                        <a href="{{ route('manageuser.index') }}" class="waves-effect"><i class='bx bx-group'></i><span>จัดการพนักงาน</span></a>
                    </li>
                    <li class="li-setting">
                        <a href="#" class="waves-effect menu-setting"><i class='bx bx-cog'></i><span>ตั้งค่า</span></a>
                        <ul class="" aria-expanded="false">
                            <li class="menu-sub-setting"><a href="{{ route('setting.index') }}" class="active">ตั้งค่าเว็บ</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.affiliate') }}" class="active">ตั้งการแนะนำ</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.popup') }}" class="active">ตั้งค่าป๊อบอัพ</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.level') }}" class="active">ตั้งค่าระดับสมาชิก</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.wheel') }}" class="active">ตั้งค่าวงล้อ</a></li>
                            <li class="menu-sub-setting"><a href="{{ route('setting.deposit_continuously') }}" class="active">ตั้งค่าการฝากต่อเนื่อง</a></li>
                            {{-- <li class="menu-sub-setting"><a href="{{ route('setting.point') }}" class="active">ตั้งค่าการคำนวณแต้ม</a></li> --}}
                            {{-- <li class="menu-sub-setting"><a href="{{ route('setting.ranking') }}" class="active">ตั้งค่าจัดอันดับ</a></li> --}}
                            {{-- <li class="menu-sub-setting"><a href="{{ route('setting.mission') }}" class="active">ตั้งค่าเควสประจำวัน</a></li> --}}
                            <li class="menu-sub-setting"><a href="{{ route('setting.coupon') }}" class="active">ตั้งค่าคูปอง</a></li>
                        </ul>
                    </li>
                    {{-- @endif --}}
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
