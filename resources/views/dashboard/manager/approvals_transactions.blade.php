@include('dashboard.manager.css')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('dashboard.manager.sidebar')

            <div class="layout-page">
                @include('dashboard.manager.header')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-soft">
                                    <div class="card-header bg-white">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div>
                                                <h5 class="mb-0">Transaction Approvals</h5>
                                                <p class="text-muted mb-0">Approve high-value (above NPR 2,000,000) or
                                                    pending transactions.</p>
                                            </div>
                                            <form method="GET" class="d-flex gap-2 flex-wrap">
                                                <select name="type" class="form-select form-select-sm"
                                                    style="min-width: 160px;">
                                                    <option value="">All types</option>
                                                    <option value="deposit"
                                                        @if ($type === 'deposit') selected @endif>Deposit
                                                    </option>
                                                    <option value="withdrawal"
                                                        @if ($type === 'withdrawal') selected @endif>Withdrawal
                                                    </option>
                                                    <option value="transfer"
                                                        @if ($type === 'transfer') selected @endif>Transfer
                                                    </option>
                                                </select>
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-primary">Filter</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <strong>Check the details below</strong>
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        @if (session('success'))
                                            <div class="alert alert-success mb-4">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Balance After</th>
                                                        <th class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($transactions as $tx)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">
                                                                    {{ $tx->customer?->first_name }}
                                                                    {{ $tx->customer?->last_name }}</div>
                                                                <div class="text-muted" style="font-size:0.9rem;">
                                                                    {{ $tx->customer?->account_number }}</div>
                                                            </td>
                                                            <td class="text-capitalize">{{ $tx->transaction_type }}</td>
                                                            <td>NPR {{ number_format($tx->amount, 2) }}</td>
                                                            <td>
                                                                NPR {{ number_format($tx->balance_after, 2) }}
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                                    <form method="POST"
                                                                        action="{{ route('manager.approvals.transactions.approve', $tx->id) }}">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-success">Approve</button>
                                                                    </form>
                                                                    <form method="POST"
                                                                        action="{{ route('manager.approvals.transactions.reject', $tx->id) }}"
                                                                        class="d-flex gap-2">
                                                                        @csrf
                                                                        <input type="text" name="reason"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="Reason" required>
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-danger">Reject</button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">No pending
                                                                transactions.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        @if ($type === 'transfer' || $type === '')
                                            <hr class="my-4">
                                            <h6 class="mb-3">Pending Transfers</h6>
                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Reference</th>
                                                            <th>From</th>
                                                            <th>To</th>
                                                            <th>Amount</th>
                                                            <th>Status</th>
                                                            <th class="text-end">Actions</th>
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
                                                                    <div class="text-muted" style="font-size:0.9rem;">
                                                                        {{ $transfer->fromCustomer?->account_number }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="fw-semibold">
                                                                        {{ $transfer->toCustomer?->first_name }}
                                                                        {{ $transfer->toCustomer?->last_name }}</div>
                                                                    <div class="text-muted" style="font-size:0.9rem;">
                                                                        {{ $transfer->toCustomer?->account_number }}
                                                                    </div>
                                                                </td>
                                                                <td>NPR {{ number_format($transfer->amount, 2) }}</td>
                                                                <td class="text-capitalize">{{ $transfer->status }}
                                                                </td>
                                                                <td class="text-end">
                                                                    <div
                                                                        class="d-flex justify-content-end gap-2 flex-wrap">
                                                                        <form method="POST"
                                                                            action="{{ route('manager.approvals.transfers.approve', $transfer->id) }}">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-success">Approve</button>
                                                                        </form>
                                                                        <form method="POST"
                                                                            action="{{ route('manager.approvals.transfers.reject', $transfer->id) }}"
                                                                            class="d-flex gap-2">
                                                                            @csrf
                                                                            <input type="text" name="reason"
                                                                                class="form-control form-control-sm"
                                                                                placeholder="Reason" required>
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-outline-danger">Reject</button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">No
                                                                    pending
                                                                    transfers.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-3">
                                                {{ $transfers->links() }}
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            {{ $transactions->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
