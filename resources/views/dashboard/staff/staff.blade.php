@include('dashboard.staff.css')

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            @include('dashboard.staff.sidebar')
            <!-- / Sidebar -->

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('dashboard.staff.header')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card shadow-soft">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                            <div>
                                                <h5 class="card-title text-primary mb-1">Staff Dashboard</h5>
                                                <p class="text-muted mb-0">Quick access to daily work and customer flow.</p>
                                            </div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                                    Add customer
                                                </a>
                                                <a href="{{ route('customers.customersList') }}" class="btn btn-outline-primary">
                                                    Browse customers
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-3">
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Customers created today</div>
                                                    <div class="fs-4 fw-semibold">{{ $customersToday ?? 0 }}</div>
                                                    <div class="small text-muted">Since {{ now()->startOfDay()->format('H:i') }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Total customers created</div>
                                                    <div class="fs-4 fw-semibold">{{ $customersTotal ?? 0 }}</div>
                                                    <div class="small text-muted">All time (your account)</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Pending business approvals</div>
                                                    <div class="fs-4 fw-semibold">{{ $pendingBusinessAccounts ?? 0 }}</div>
                                                    <div class="small text-muted">Waiting for manager</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Moved to deleted</div>
                                                    <div class="fs-4 fw-semibold">{{ $softDeletedByYou ?? 0 }}</div>
                                                    <div class="small text-muted">Soft-deleted by you</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-1">
                                            <div class="col-lg-5">
                                                <div class="card border h-100">
                                                    <div class="card-body">
                                                        <h6 class="mb-2">Quick actions</h6>
                                                        <p class="text-muted mb-3">Common tasks to keep your workflow fast.</p>
                                                        <div class="d-grid gap-2">
                                                            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                                                Create new customer
                                                            </a>
                                                            <a href="{{ route('customers.customersList') }}" class="btn btn-outline-primary">
                                                                Search / view customers
                                                            </a>
                                                            <a href="{{ route('customers.deleted') }}" class="btn btn-outline-danger">
                                                                View deleted customers
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-7">
                                                <div class="card border h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div>
                                                                <h6 class="mb-0">Recent customers</h6>
                                                                <div class="text-muted small">Latest profiles you created</div>
                                                            </div>
                                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('customers.customersList') }}">
                                                                View all
                                                            </a>
                                                        </div>

                                                        <div class="table-responsive">
                                                            <table class="table align-middle mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Customer</th>
                                                                        <th>Account</th>
                                                                        <th>Status</th>
                                                                        <th class="text-end">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse (($recentCustomers ?? []) as $c)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="fw-semibold">{{ $c->first_name }} {{ $c->last_name }}</div>
                                                                                <div class="text-muted small">{{ optional($c->created_at)->format('M d, Y H:i') }}</div>
                                                                            </td>
                                                                            <td>
                                                                                <div>{{ $c->account_number ?? '—' }}</div>
                                                                                <div class="text-muted small text-capitalize">{{ $c->account_type ?? '—' }}</div>
                                                                            </td>
                                                                            <td class="text-capitalize">{{ $c->status ?? '—' }}</td>
                                                                            <td class="text-end">
                                                                                <a class="btn btn-sm btn-outline-primary"
                                                                                    href="{{ route('customers.documents.create', $c->id) }}">
                                                                                    Upload docs
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center text-muted">
                                                                                No customers created yet. Start by creating one.
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
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
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
</body>

</html>
