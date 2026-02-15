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
                                            <h5 class="mb-0">Pending Approvals</h5>
                                            <p class="text-muted mb-0" style="font-size: 0.95rem;">Requests above NPR
                                                100,000 land here.</p>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if (session('error'))
                                            <div class="alert alert-danger">
                                                {{ session('error') }}
                                            </div>
                                        @endif

                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Reference #</th>
                                                        <th>Customer</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Teller</th>
                                                        <th>Date</th>
                                                        <th>Notes</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($transactions as $transaction)
                                                        <tr>
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
                                                            <td>NPR {{ number_format($transaction->amount, 2) }}</td>
                                                            <td>{{ $transaction->createdBy->name ?? 'N/A' }}</td>
                                                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}
                                                            </td>
                                                            <td>{{ Str::limit($transaction->notes, 30) }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-success"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#approveModal{{ $transaction->id }}">
                                                                    Approve
                                                                </button>
                                                                <button class="btn btn-sm btn-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#rejectModal{{ $transaction->id }}">
                                                                    Reject
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        <!-- Approve Modal -->
                                                        <div class="modal fade" id="approveModal{{ $transaction->id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Approve Transaction</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                        action="{{ route('teller.approve', $transaction->id) }}">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <p>Approve
                                                                                {{ ucfirst($transaction->transaction_type) }}
                                                                                of
                                                                                <strong>{{ number_format($transaction->amount, 2) }}</strong>
                                                                                for <strong>{{ $transaction->customer->first_name }}
                                                                                    {{ $transaction->customer->last_name }}</strong>?
                                                                            </p>
                                                                            <p class="text-muted">Reference:
                                                                                {{ $transaction->reference_number }}
                                                                            </p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit"
                                                                                class="btn btn-success">Approve</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Reject Modal -->
                                                        <div class="modal fade" id="rejectModal{{ $transaction->id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Reject Transaction</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                        action="{{ route('teller.reject', $transaction->id) }}">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <p>Reject
                                                                                {{ ucfirst($transaction->transaction_type) }}
                                                                                of
                                                                                <strong>{{ number_format($transaction->amount, 2) }}</strong>?
                                                                            </p>
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Reason for
                                                                                    Rejection</label>
                                                                                <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Reject</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center text-muted py-4">No
                                                                pending approvals</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <hr class="my-4">
                                        <h6 class="mb-3">Pending Transfers</h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Reference #</th>
                                                        <th>From</th>
                                                        <th>To</th>
                                                        <th>Amount</th>
                                                        <th>Teller</th>
                                                        <th>Date</th>
                                                        <th>Notes</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($transfers as $transfer)
                                                        <tr>
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
                                                            <td>{{ $transfer->createdBy->name ?? 'N/A' }}</td>
                                                            <td>{{ $transfer->created_at->format('Y-m-d H:i') }}</td>
                                                            <td>{{ Str::limit($transfer->notes, 30) }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-success"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#approveTransferModal{{ $transfer->id }}">
                                                                    Approve
                                                                </button>
                                                                <button class="btn btn-sm btn-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#rejectTransferModal{{ $transfer->id }}">
                                                                    Reject
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        <div class="modal fade"
                                                            id="approveTransferModal{{ $transfer->id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Approve Transfer</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                        action="{{ route('teller.transfers.approve', $transfer->id) }}">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <p>Approve transfer of
                                                                                <strong>{{ number_format($transfer->amount, 2) }}</strong>?
                                                                            </p>
                                                                            <p class="text-muted">Reference:
                                                                                {{ $transfer->reference_number }}
                                                                            </p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit"
                                                                                class="btn btn-success">Approve</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal fade"
                                                            id="rejectTransferModal{{ $transfer->id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Reject Transfer</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                        action="{{ route('teller.transfers.reject', $transfer->id) }}">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <p>Reject transfer of
                                                                                <strong>{{ number_format($transfer->amount, 2) }}</strong>?
                                                                            </p>
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Reason for
                                                                                    Rejection</label>
                                                                                <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Cancel</button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Reject</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center text-muted py-4">No
                                                                pending transfers</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="d-flex justify-content-center">
                                            {{ $transactions->links() }}
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            {{ $transfers->links() }}
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
