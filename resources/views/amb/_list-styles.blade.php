<style>
    .amb-list-page .card {
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
    }

    .amb-list-page .card-body {
        padding: 1.35rem 1.5rem;
    }

    .amb-list-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.5rem 0.75rem;
        margin-bottom: 1.25rem;
    }

    .amb-list-toolbar .form-inline {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.5rem 0.5rem;
        margin-bottom: 0;
    }

    .amb-list-toolbar .form-inline .form-control {
        min-width: 140px;
    }

    .amb-list-toolbar .btn-gold,
    .amb-list-toolbar .btn-primary,
    .amb-list-toolbar .btn-light {
        height: calc(1.5em + 0.75rem + 2px);
        line-height: 1.5;
    }

    .amb-list-page .table-responsive {
        border-radius: 8px;
        overflow-x: auto;
    }

    .amb-list-page .table {
        margin-bottom: 0;
        font-size: 0.92rem;
    }

    /* หัวตาราง / เนื้อหา: ใช้สีเข้มบนพื้นอ่อน (การ์ดใน dark-mode หลายธีมยังเป็นพื้นขาว) */
    .amb-list-page .table thead th {
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #343a40 !important;
        background-color: #f1f3f5 !important;
        border-bottom-width: 2px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .amb-list-page .table tbody td {
        vertical-align: middle;
        color: #1e2226 !important;
        background-color: #fff;
    }

    .amb-list-page .table tbody tr:hover td {
        background-color: #fffdf8;
    }

    .amb-list-page .table tbody tr:hover {
        background: rgba(227, 169, 65, 0.06);
    }

    .amb-list-page .img-thumb-game,
    .amb-list-page .img-thumb-prod {
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .amb-list-page .pagination-wrap {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        padding-top: 1rem;
        width: 100%;
    }

    /* Bootstrap 4 pagination (ใช้กับ ->links('pagination::bootstrap-4')) */
    .amb-list-page .pagination {
        margin-bottom: 0;
        font-size: 0.875rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .amb-list-page .pagination .page-link {
        padding: 0.4rem 0.7rem;
        min-width: 2.25rem;
        text-align: center;
        line-height: 1.35;
        color: #343a40;
        border-color: #dee2e6;
        background-color: #fff;
    }

    .amb-list-page .pagination .page-item.active .page-link {
        background-color: #E3A941;
        border-color: #E3A941;
        color: #fff;
    }

    .amb-list-page .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: #f8f9fa;
    }

    /* ถ้าใช้ default Laravel (Tailwind) โดยไม่มี utility: จำกัด SVG ลูกศร */
    .amb-list-page .pagination-wrap nav[role="navigation"] {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        max-width: 100%;
    }

    .amb-list-page .pagination-wrap nav[role="navigation"] svg {
        width: 0.875rem !important;
        height: 0.875rem !important;
        max-width: 0.875rem !important;
        max-height: 0.875rem !important;
        flex-shrink: 0;
    }

    body.dark-mode .amb-list-page .table thead.table-light th {
        background: #e9ecef !important;
        color: #343a40 !important;
        border-color: #dee2e6 !important;
    }

    body.dark-mode .amb-list-page .table tbody td {
        color: #1e2226 !important;
        background-color: #fff !important;
        border-color: #dee2e6 !important;
    }

    body.dark-mode .amb-list-page .pagination .page-link {
        background: #fff;
        border-color: #dee2e6;
        color: #343a40;
    }
</style>
