<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="{{ asset('assets') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Research Bank of Nepal</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- endbuild -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Shared UI polish -->
    <style>
      :root {
        --rbn-bg: #f6f8fb;
        --rbn-card: #ffffff;
        --rbn-border: #e3e8ef;
        --rbn-primary: #2f6fed;
        --rbn-primary-soft: #e8f0ff;
        --rbn-muted: #6b7280;
        --rbn-success: #16a34a;
        --rbn-danger: #dc2626;
        --rbn-warning: #d97706;
      }

      body {
        background-color: var(--rbn-bg);
      }

      .layout-wrapper,
      .layout-page,
      .content-wrapper {
        background-color: var(--rbn-bg);
      }

      .card {
        border: 1px solid var(--rbn-border);
        box-shadow: 0 6px 24px rgba(17, 24, 39, 0.06);
        border-radius: 12px;
      }

      .card-header {
        border-bottom: 1px solid var(--rbn-border);
      }

      .card-header h5,
      .card-title {
        font-weight: 600;
        margin-bottom: 0;
      }

      label.form-label {
        font-weight: 600;
        color: #111827;
      }

      .form-control,
      .form-select,
      textarea.form-control {
        border-radius: 10px;
        border: 1px solid var(--rbn-border);
        padding: 0.65rem 0.9rem;
        transition: all 0.15s ease;
      }

      .form-control:focus,
      .form-select:focus,
      textarea.form-control:focus {
        border-color: var(--rbn-primary);
        box-shadow: 0 0 0 0.2rem rgba(47, 111, 237, 0.15);
      }

      .btn {
        border-radius: 10px;
        padding: 0.65rem 1.1rem;
        font-weight: 600;
      }

      .btn-primary,
      .btn-info {
        background: linear-gradient(90deg, #2f6fed, #4b8dff);
        border-color: #2f6fed;
        color: #fff;
      }

      .btn-secondary {
        background-color: #1f2937;
        border-color: #1f2937;
      }

      .btn-outline-secondary {
        border-color: var(--rbn-border);
        color: #111827;
      }

      .alert {
        border-radius: 12px;
        border: 1px solid var(--rbn-border);
      }

      .badge {
        padding: 0.4rem 0.65rem;
        border-radius: 8px;
        font-weight: 600;
      }

      .status-approved {
        background-color: rgba(22, 163, 74, 0.1);
        color: var(--rbn-success);
      }

      .status-pending {
        background-color: rgba(217, 119, 6, 0.12);
        color: var(--rbn-warning);
      }

      .status-rejected {
        background-color: rgba(220, 38, 38, 0.1);
        color: var(--rbn-danger);
      }

      table.table {
        border: 1px solid var(--rbn-border);
        border-radius: 12px;
        overflow: hidden;
      }

      table.table thead {
        background-color: var(--rbn-primary-soft);
      }

      table.table th {
        color: #111827;
        font-weight: 700;
      }

      table.table td,
      table.table th {
        vertical-align: middle;
      }

      .helper-text {
        color: var(--rbn-muted);
        font-size: 0.9rem;
      }

      .section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.35rem;
      }

      .muted {
        color: var(--rbn-muted);
      }

      .shadow-soft {
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
      }
    </style>

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset('assets/js/config.js') }}"></script>
  </head>