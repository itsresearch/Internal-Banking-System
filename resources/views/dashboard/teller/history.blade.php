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
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-soft">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="mb-0">Transaction History</h5>
                                            <p class="text-muted mb-0" style="font-size: 0.95rem;">Latest 20 transactions, newest first.</p>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Reference #</th>
                                                        <th>Customer</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Before</th>
                                                        <th>After</th>
                                                        <th>Status</th>
                                                        <th>Teller</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($transactions as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->reference_number }}</td>
                                                            <td>
                                                                <div class="fw-semibold">{{ $transaction->customer->first_name }} {{ $transaction->customer->last_name }}</div>
                                                                <div class="text-muted" style="font-size: 0.9rem;">{{ $transaction->customer->account_number ?? '—' }}</div>
                                                            </td>
                                                            <td>
                                                                <span class="badge {{ $transaction->transaction_type === 'deposit' ? 'status-approved' : ($transaction->transaction_type === 'withdrawal' ? 'status-rejected' : 'status-pending') }}">
                                                                    {{ ucfirst($transaction->transaction_type) }}
                                                                </span>
                                                            </td>
                                                            <td>NPR {{ number_format($transaction->amount, 2) }}</td>
                                                            <td>NPR {{ number_format($transaction->balance_before, 2) }}</td>
                                                            <td>NPR {{ number_format($transaction->balance_after, 2) }}</td>
                                                            <td>
                                                                <span class="badge
                                                                    {{ $transaction->status === 'approved' ? 'status-approved' : ($transaction->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                                                    {{ ucfirst($transaction->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $transaction->createdBy->name ?? 'N/A' }}</td>
                                                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9" class="text-center text-muted py-4">
                                                                No transactions found yet.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="d-flex justify-content-center">
                                            {{ $transactions->links() }}
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
                                        </script> Research Bank of Nepal, All rights reserved
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
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
