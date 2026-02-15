@include('dashboard.manager.css')

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            @include('dashboard.manager.sidebar')
            <!-- / Sidebar -->

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('dashboard.manager.header')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-soft">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                            <div>
                                                <h5 class="card-title text-primary mb-2">Manager Command Center</h5>
                                                <p class="text-muted mb-0">Approve requests, monitor cash flow, and
                                                    reverse transactions.</p>
                                            </div>
                                            <a href="{{ route('manager.approvals.accounts') }}"
                                                class="btn btn-outline-primary">Review approvals</a>
                                        </div>

                                        <div class="row g-3 mt-3">
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Pending accounts</div>
                                                    <div class="fs-4 fw-semibold">{{ $pendingAccounts }}</div>
                                                    <a href="{{ route('manager.approvals.accounts') }}"
                                                        class="small">View queue</a>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Pending transactions</div>
                                                    <div class="fs-4 fw-semibold">{{ $pendingTransactions }}</div>
                                                    <a href="{{ route('manager.approvals.transactions') }}"
                                                        class="small">Review</a>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Frozen accounts</div>
                                                    <div class="fs-4 fw-semibold">{{ $frozenAccounts }}</div>
                                                    <a href="{{ route('manager.customers') }}" class="small">Manage</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-3">
                                            <div class="col-md-6">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <h6 class="mb-2">Today at a glance</h6>
                                                        <p class="text-muted mb-3">Daily totals, cash handled, and staff
                                                            activity.</p>
                                                        <a href="{{ route('manager.monitoring.summary') }}"
                                                            class="btn btn-sm btn-primary">Open summary</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border">
                                                    <div class="card-body">
                                                        <h6 class="mb-2">Customer oversight</h6>
                                                        <p class="text-muted mb-3">Review profiles and apply account
                                                            holds.</p>
                                                        <a href="{{ route('manager.customers') }}"
                                                            class="btn btn-sm btn-outline-primary">Browse customers</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                                <div class="mb-2 mb-md-0">
                                    <span class="text-muted">©
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script>
                                        <a href="javascript:void(0);" target="_blank">Research Bank of Nepal</a>
                                        <span class="text-muted">, All rights reserved</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
</body>

</html>
