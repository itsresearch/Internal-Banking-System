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
                                    <div
                                        class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div>
                                            <h5 class="mb-0">Transaction History</h5>
                                            <p class="text-muted mb-0" style="font-size: 0.95rem;">Latest 20
                                                transactions, newest first.</p>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary" id="showTransactions">
                                                Transactions
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="showTransfers">
                                                Transfers
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="transactionSection">
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
                                                            <tr data-bs-toggle="collapse"
                                                                data-bs-target="#transaction-{{ $transaction->id }}"
                                                                aria-expanded="false">
                                                                <td>{{ $transaction->reference_number }}</td>
                                                                <td>
                                                                    <div class="fw-semibold">
                                                                        {{ $transaction->customer->first_name }}
                                                                        {{ $transaction->customer->last_name }}</div>
                                                                    <div class="text-muted" style="font-size: 0.9rem;">
                                                                        {{ $transaction->customer->account_number ?? '—' }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge {{ $transaction->transaction_type === 'deposit' ? 'status-approved' : ($transaction->transaction_type === 'withdrawal' ? 'status-rejected' : 'status-pending') }}">
                                                                        {{ ucfirst($transaction->transaction_type) }}
                                                                    </span>
                                                                </td>
                                                                <td>NPR {{ number_format($transaction->amount, 2) }}
                                                                </td>
                                                                <td>NPR
                                                                    {{ number_format($transaction->balance_before, 2) }}
                                                                </td>
                                                                <td>NPR
                                                                    {{ number_format($transaction->balance_after, 2) }}
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge
                                                                        {{ $transaction->status === 'approved' ? 'status-approved' : ($transaction->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                                                        {{ ucfirst($transaction->status) }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $transaction->createdBy->name ?? 'N/A' }}</td>
                                                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}
                                                                </td>
                                                            </tr>
                                                            <tr class="collapse bg-light"
                                                                id="transaction-{{ $transaction->id }}">
                                                                <td colspan="9">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Reference</div>
                                                                            <div>{{ $transaction->reference_number }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Status</div>
                                                                            <div>{{ ucfirst($transaction->status) }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Created By</div>
                                                                            <div>
                                                                                {{ $transaction->createdBy->name ?? 'N/A' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Approved By</div>
                                                                            <div>
                                                                                {{ $transaction->approvedBy->name ?? 'N/A' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="fw-semibold">Notes</div>
                                                                            <div>{{ $transaction->notes ?? 'N/A' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Created At</div>
                                                                            <div>
                                                                                {{ $transaction->created_at->format('Y-m-d H:i') }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Updated At</div>
                                                                            <div>
                                                                                {{ $transaction->updated_at->format('Y-m-d H:i') }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
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

                                        <div id="transferSection" class="d-none">
                                            <h6 class="mb-3">Transfer History</h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Reference #</th>
                                                            <th>From</th>
                                                            <th>To</th>
                                                            <th>Amount</th>
                                                            <th>Status</th>
                                                            <th>Teller</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($transfers as $transfer)
                                                            <tr data-bs-toggle="collapse"
                                                                data-bs-target="#transfer-{{ $transfer->id }}"
                                                                aria-expanded="false">
                                                                <td>{{ $transfer->reference_number }}</td>
                                                                <td>
                                                                    <div class="fw-semibold">
                                                                        {{ $transfer->fromCustomer?->first_name }}
                                                                        {{ $transfer->fromCustomer?->last_name }}</div>
                                                                    <div class="text-muted" style="font-size: 0.9rem;">
                                                                        {{ $transfer->fromCustomer?->account_number ?? '—' }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="fw-semibold">
                                                                        {{ $transfer->toCustomer?->first_name }}
                                                                        {{ $transfer->toCustomer?->last_name }}</div>
                                                                    <div class="text-muted" style="font-size: 0.9rem;">
                                                                        {{ $transfer->toCustomer?->account_number ?? '—' }}
                                                                    </div>
                                                                </td>
                                                                <td>NPR {{ number_format($transfer->amount, 2) }}</td>
                                                                <td>
                                                                    <span
                                                                        class="badge
                                                                        {{ $transfer->status === 'approved' ? 'status-approved' : ($transfer->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                                                        {{ ucfirst($transfer->status) }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $transfer->createdBy->name ?? 'N/A' }}</td>
                                                                <td>{{ $transfer->created_at->format('Y-m-d H:i') }}
                                                                </td>
                                                            </tr>
                                                            <tr class="collapse bg-light"
                                                                id="transfer-{{ $transfer->id }}">
                                                                <td colspan="7">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Reference</div>
                                                                            <div>{{ $transfer->reference_number }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Status</div>
                                                                            <div>{{ ucfirst($transfer->status) }}</div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Created By</div>
                                                                            <div>
                                                                                {{ $transfer->createdBy->name ?? 'N/A' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Approved By</div>
                                                                            <div>
                                                                                {{ $transfer->approvedBy->name ?? 'N/A' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">From Balance</div>
                                                                            <div>
                                                                                {{ $transfer->from_balance_before ?? 'N/A' }}
                                                                                →
                                                                                {{ $transfer->from_balance_after ?? 'N/A' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">To Balance</div>
                                                                            <div>
                                                                                {{ $transfer->to_balance_before ?? 'N/A' }}
                                                                                →
                                                                                {{ $transfer->to_balance_after ?? 'N/A' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="fw-semibold">Notes</div>
                                                                            <div>{{ $transfer->notes ?? 'N/A' }}</div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Created At</div>
                                                                            <div>
                                                                                {{ $transfer->created_at->format('Y-m-d H:i') }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="fw-semibold">Updated At</div>
                                                                            <div>
                                                                                {{ $transfer->updated_at->format('Y-m-d H:i') }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center text-muted py-4">
                                                                    No transfers found yet.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                {{ $transfers->links() }}
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
    <script>
        (function() {
            const transactionSection = document.getElementById('transactionSection');
            const transferSection = document.getElementById('transferSection');
            const showTransactions = document.getElementById('showTransactions');
            const showTransfers = document.getElementById('showTransfers');

            function setActive(section) {
                const isTransactions = section === 'transactions';
                transactionSection.classList.toggle('d-none', !isTransactions);
                transferSection.classList.toggle('d-none', isTransactions);
                showTransactions.classList.toggle('btn-primary', isTransactions);
                showTransactions.classList.toggle('btn-outline-secondary', !isTransactions);
                showTransfers.classList.toggle('btn-primary', !isTransactions);
                showTransfers.classList.toggle('btn-outline-secondary', isTransactions);
            }

            showTransactions.addEventListener('click', function() {
                setActive('transactions');
            });
            showTransfers.addEventListener('click', function() {
                setActive('transfers');
            });

            setActive('transactions');
        })();
    </script>
</body>

</html>
