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
            :root {
                --app-bg: #f5f6f8;
                --app-surface: #ffffff;
                --app-surface-muted: #f8f9fa;
                --app-border: #dee2e6;
                --app-text: #383c40;
                --app-heading: #1f2328;
                --app-muted: #6c757d;
                --app-input-bg: #ffffff;
            }

            body.dark-mode {
                --app-bg: #10141b;
                --app-surface: #1e2530;
                --app-surface-muted: #2b3544;
                --app-border: #485568;
                --app-text: #edf2f7;
                --app-heading: #ffffff;
                --app-muted: #c4cedb;
                --app-input-bg: #18202b;
            }

            body {
                background-color: var(--app-bg);
                color: var(--app-text);
            }

            .page-content,
            .main-content,
            .footer {
                background-color: var(--app-bg);
                color: var(--app-text);
            }

            .navbar-header,
            .vertical-menu,
            .card,
            .card-body,
            .dropdown-menu,
            .modal-content {
                background-color: var(--app-surface);
                color: var(--app-text);
            }

            .footer,
            .card,
            .table,
            .modal-content,
            .dropdown-menu {
                border-color: var(--app-border);
            }

            .header-item,
            .header-item:hover,
            .card h1,
            .card h2,
            .card h3,
            .card h4,
            .card h5,
            .card h6,
            .card-title,
            .page-title-box h1,
            .page-title-box h2,
            .page-title-box h3,
            .page-title-box h4,
            .page-title-box h5,
            .page-title-box h6,
            .page-title-box,
            .breadcrumb-item.active,
            .dropdown-item,
            #sidebar-menu ul li a,
            #sidebar-menu .menu-title {
                color: var(--app-heading);
            }

            .text-muted,
            .card-subtitle,
            .breadcrumb-item a {
                color: var(--app-muted) !important;
            }

            body.dark-mode .card-animate {
                box-shadow: 0 10px 28px rgba(0, 0, 0, 0.28);
            }

            body.dark-mode .card .text-muted {
                color: var(--app-muted) !important;
            }

            body.dark-mode .card h3,
            body.dark-mode .card [data-plugin="counterup"],
            body.dark-mode .table tbody td {
                color: var(--app-heading) !important;
            }

            .dropdown-item:hover,
            .dropdown-item:focus {
                background-color: var(--app-surface-muted);
                color: var(--app-text);
            }

            .table,
            .bootstrap-table,
            .fixed-table-container,
            .fixed-table-body,
            .fixed-table-pagination {
                background-color: var(--app-surface) !important;
                color: var(--app-text) !important;
            }

            .table td,
            .table th,
            #basic-datatable td,
            #basic-datatable th,
            #table td,
            #table th {
                border-color: var(--app-border) !important;
                color: var(--app-text) !important;
            }

            .table thead,
            .table thead th,
            .table-light,
            .table-light > th,
            .table-light > td,
            #basic-datatable thead th,
            #basic-datatable .table-light .th-inner,
            #table thead th,
            .bootstrap-table .fixed-table-header,
            .bootstrap-table .fixed-table-container .table thead th,
            .bootstrap-table .fixed-table-container .table thead th .th-inner {
                background-color: var(--app-surface-muted) !important;
                border-color: var(--app-border) !important;
                color: var(--app-text) !important;
            }

            .table tbody tr,
            .table tbody td {
                background-color: var(--app-surface) !important;
                color: var(--app-text) !important;
            }

            .table-striped tbody tr:nth-of-type(odd),
            .table-hover tbody tr:hover,
            .bootstrap-table .fixed-table-container .table tbody tr:hover td {
                background-color: var(--app-surface-muted) !important;
                color: var(--app-text) !important;
            }

            .bootstrap-table .fixed-table-toolbar .search input,
            .bootstrap-table .filter-control input,
            .bootstrap-table .filter-control select,
            .form-control,
            .form-select,
            .custom-select,
            .fixed-table-pagination .pagination-detail,
            .fixed-table-pagination .page-list,
            .fixed-table-pagination .page-link {
                background-color: var(--app-input-bg) !important;
                border-color: var(--app-border) !important;
                color: var(--app-text) !important;
            }

            .form-control:focus,
            .form-select:focus,
            .custom-select:focus {
                background-color: var(--app-input-bg) !important;
                border-color: #E3A941 !important;
                color: var(--app-heading) !important;
                box-shadow: 0 0 0 0.15rem rgba(227, 169, 65, 0.25) !important;
            }

            .form-control::placeholder {
                color: var(--app-muted) !important;
            }

            body.dark-mode .daterangepicker,
            body.dark-mode .daterangepicker .calendar-table {
                background-color: var(--app-surface) !important;
                border-color: var(--app-border) !important;
                color: var(--app-text) !important;
            }

            body.dark-mode .daterangepicker:before {
                border-bottom-color: var(--app-border) !important;
            }

            body.dark-mode .daterangepicker:after {
                border-bottom-color: var(--app-surface) !important;
            }

            body.dark-mode .daterangepicker .ranges li {
                color: var(--app-text) !important;
                background-color: transparent !important;
            }

            body.dark-mode .daterangepicker .ranges li:hover {
                color: var(--app-heading) !important;
                background-color: var(--app-surface-muted) !important;
            }

            body.dark-mode .daterangepicker .ranges li.active {
                color: #111827 !important;
                background-color: #E3A941 !important;
                font-weight: 600;
            }

            body.dark-mode .daterangepicker td.available:hover,
            body.dark-mode .daterangepicker th.available:hover,
            body.dark-mode .daterangepicker td.in-range {
                background-color: var(--app-surface-muted) !important;
                color: var(--app-heading) !important;
            }

            body.dark-mode .daterangepicker td.off,
            body.dark-mode .daterangepicker td.off.in-range,
            body.dark-mode .daterangepicker td.off.start-date,
            body.dark-mode .daterangepicker td.off.end-date {
                background-color: var(--app-surface) !important;
                color: #8f9bad !important;
            }

            body.dark-mode .daterangepicker td.active,
            body.dark-mode .daterangepicker td.active:hover {
                background-color: #E3A941 !important;
                color: #111827 !important;
            }

            .fixed-table-pagination .page-item.active .page-link {
                background-color: #E3A941 !important;
                border-color: #E3A941 !important;
                color: #fff !important;
            }

            body:not(.dark-mode) .menu-setting.mm-active,
            body:not(.dark-mode) .li-setting,
            body:not(.dark-mode) .menu-sub-setting {
                background-color: white !important;
            }

            body.dark-mode .menu-setting.mm-active,
            body.dark-mode .li-setting,
            body.dark-mode .menu-sub-setting {
                background-color: var(--app-surface-muted) !important;
            }

            .theme-toggle-btn {
                min-width: 48px;
                font-size: 22px;
            }
        </style>
        <script>
            (function() {
                const savedTheme = localStorage.getItem('admin-theme') || 'dark';
                document.documentElement.dataset.theme = savedTheme;
            })();
        </script>
        @yield('styles')
    </head>
    <body class="dark-mode">
        <script>
            (function() {
                const savedTheme = localStorage.getItem('admin-theme') || 'dark';
                document.body.classList.toggle('dark-mode', savedTheme === 'dark');
                document.body.classList.toggle('light-mode', savedTheme === 'light');
            })();
        </script>
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
        {{-- <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('theme-toggle-btn');
    const toggleIcon = document.getElementById('theme-toggle-icon');

    function setTheme(theme) {
        const isDark = theme === 'dark';

        document.documentElement.dataset.theme = theme;
        document.body.classList.toggle('dark-mode', isDark);
        document.body.classList.toggle('light-mode', !isDark);
        localStorage.setItem('admin-theme', theme);

        if (toggleIcon) {
            toggleIcon.classList.toggle('bx-moon', !isDark);
            toggleIcon.classList.toggle('bx-sun', isDark);
        }
    }

    setTheme(localStorage.getItem('admin-theme') || 'dark');

    if (toggleButton) {
        toggleButton.addEventListener('click', function() {
            setTheme(document.body.classList.contains('dark-mode') ? 'light' : 'dark');
        });
    }
});
</script>

        @yield('scripts')
    </body>
</html>
