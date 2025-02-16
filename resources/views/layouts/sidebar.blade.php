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
                    <a href="/" class="waves-effect"><i class='bx bxs-dashboard'></i><span>แดชบอร์ด</span></a>
                </li>
                <li>
                    <a href="{{ route('totalincome') }}" class="waves-effect"><i class='bx bxs-dollar-circle' ></i><span>ยอดรวมรายได้</span></a>
                </li>
                <li>
                    <a href="{{ route('report_refund') }}" class="waves-effect"><i class='bx bx-transfer' ></i><span>ประวัติการโอนเงินคืน</span></a>
                </li>
                <li>
                    <a href="{{ route('partner.index') }}" class="waves-effect"><i class='bx bx-user-circle'></i><span>พันธมิตร</span></a>
                </li>
                <li class="li-setting">
                    <a href="#" class="waves-effect menu-setting"><i class='bx bx-cog'></i><span>จัดการข้อมูลสาขา</span></a>
                    <ul class="" aria-expanded="false">
                        <li class="menu-sub-setting"><a href="#">ตั้งค่าเบื้องต้น</a></li>
                        {{-- <li class="menu-sub-setting"><a href="{{ route('company.company')}}">ข้อมูลส่วนตัว</a></li> --}}
                        <li class="menu-sub-setting"><a href="{{ route('company.store.edit') }}">แก้ไขข้อมูลส่วนตัว</a></li>
                        <li class="menu-sub-setting"><a href="{{ route('bankaccount.index') }}">สมุดบัญชีธนาคาร</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="waves-effect"><i class='bx bxs-megaphone' ></i><span>ข่าวสารและกิจกรรมใหม่</span></a>
                </li>
                <li>
                    <a href="#" class="waves-effect"><i class='bx bx-image-alt' ></i><span>เพิ่มโลโก้ชำระเงินบนเว็บไซต์</span></a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
