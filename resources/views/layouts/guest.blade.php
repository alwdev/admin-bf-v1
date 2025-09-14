<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="MyraStudio" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
        <link rel="stylesheet"
        href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
        <!-- App css -->
        <link href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/theme.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <style>
            .btn-gold{
                background-color: #E3A941 !important;
                border-color: #E3A941 !important;
            }
            .btn-gold:hover {
                color: #fff;
                background-color: #af8433;
                border-color: #af8433;
            }
        </style>
        <style>
            .menu-setting.mm-active,.li-setting{
                background-color: white !important;
                ul{
                    background-color: white !important;
                }
            }
            .menu-sub-setting{
                    background-color: white !important;
                };
        </style>
        <style>
            /* Main menu & submenu active */
.metismenu li.mm-active > a,
.metismenu li.mm-active > a.active,
.metismenu li.mm-active ul li.mm-active > a {
    background-color: #ffa9a9 !important;  /* สีพื้นหลัง active */
    color: rgb(127 29 29) !important;      /* สีตัวอักษร active */
}

/* Submenu ul background */
.metismenu li.mm-active ul {
    background-color: #fff; /* สีพื้นหลังของ submenu */
}

/* Submenu li a */
.metismenu li ul li a {
    color: #000; /* สีตัวอักษรปกติ */
}

.metismenu li ul li a.active {
    color: rgb(127 29 29) !important; /* สีตัวอักษรเมื่อ active */
}

        </style>
        @yield('styles')
    </head>
    <body class="dark-mode">
            <!-- Begin page -->
        <div id="layout-wrapper">

            <!-- ========== Left Sidebar Start ========== -->
            @include('layouts.sidebar')
            <!-- Left Sidebar End -->

            @include('layouts.header')
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">
                        @yield('content')
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                2024 © Mexico333.
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-right d-none d-sm-block">
                                    Design & Develop Mexico333.
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>

            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Overlay-->
        <div class="menu-overlay"></div>

        <!-- jQuery  -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/metismenu.min.js') }}"></script>
        <script src="{{ asset('js/waves.js') }}"></script>
        <script src="{{ asset('js/simplebar.min.js') }}"></script>

        <!-- Morris Js-->
        <script src="{{ asset('plugins/morris-js/morris.min.js') }}"></script>
        <!-- Raphael Js-->
        <script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>

        <!-- Morris Custom Js-->
        <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('pages/dashboard-demo.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('js/theme.js') }}"></script>
        <script>
            function makeid(length) {
                let result = '';
                const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                const charactersLength = characters.length;
                let counter = 0;
                while (counter < length) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                    counter += 1;
                }
                return result;
            }
            function isNumberKey(evt)
            {
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode != 46 && charCode > 31
                    && (charCode < 48 || charCode > 57))
                    return false;

                return true;
            }
        </script>

        <script>
function checkSystemAlerts() {
    fetch('/get-system-alert')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlertsSequentially(data.messages);
            }
        })
        .catch(error => {
            console.error('Error fetching alert data:', error);
        });
}

// ฟังก์ชันสำหรับแสดง SweetAlert ทีละข้อความ
function showAlertsSequentially(messages, index = 0) {
    if (index >= messages.length) {
        return; // จบ loop ถ้าแสดงครบแล้ว
    }

    Swal.fire({
        icon: 'warning',
        title: 'แจ้งเตือนระบบ',
        text: messages[index],
        showConfirmButton: true
    }).then(() => {
        // เมื่อปิด alert อันนี้แล้ว แสดงอันถัดไป
        showAlertsSequentially(messages, index + 1);
    });
}

// เรียกใช้ฟังก์ชันทันทีเมื่อหน้าเว็บโหลด
document.addEventListener('DOMContentLoaded', () => {
    checkSystemAlerts();
});

// เรียกใช้ฟังก์ชันทุก 1 ชั่วโมง (3600000 ms)
setInterval(checkSystemAlerts, 3600000);
</script>

        @yield('scripts')
    </body>
</html>
