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
                                                <h5 class="mb-0">Customer Oversight</h5>
                                                <p class="text-muted mb-0">Read-only profiles with account status
                                                    controls and deletion review.</p>
                                            </div>
                                            <form method="GET" class="d-flex gap-2 flex-wrap">
                                                <input type="text" name="q"
                                                    class="form-control form-control-sm" placeholder="Search customer"
                                                    value="{{ $q }}">
                                                <select name="status" class="form-select form-select-sm"
                                                    style="min-width: 160px;">
                                                    <option value="">All statuses</option>
                                                    <option value="pending"
                                                        @if ($status === 'pending') selected @endif>Pending
                                                    </option>
                                                    <option value="active"
                                                        @if ($status === 'active') selected @endif>Active
                                                    </option>
                                                    <option value="inactive"
                                                        @if ($status === 'inactive') selected @endif>Inactive
                                                    </option>
                                                </select>
                                                <label class="d-flex align-items-center gap-2">
                                                    <input type="checkbox" name="deleted" value="1"
                                                        @if (!empty($deleted)) checked @endif>
                                                    <span class="small">Show deleted</span>
                                                </label>
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-primary">Filter</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if (session('success'))
                                            <div class="alert alert-success mb-3">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                        @if ($errors->any())
                                            <div class="alert alert-danger mb-3">
                                                <strong>Check the details below</strong>
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="card border mb-3">
                                            <div
                                                class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                                                <div>
                                                    <div class="text-muted">Savings interest rate</div>
                                                    <div class="fw-semibold">
                                                        {{ number_format($currentSavingsRate, 2) }}%</div>
                                                    <div class="small text-muted">Applies to all savings accounts.</div>
                                                </div>
                                                <form method="POST"
                                                    action="{{ route('manager.customers.interest-rate') }}"
                                                    class="d-flex flex-wrap align-items-center gap-2">
                                                    @csrf
                                                    <div class="input-group input-group-sm" style="width: 200px;">
                                                        <input type="number" step="0.01" min="0"
                                                            max="100" name="interest_rate" class="form-control"
                                                            value="{{ number_format($currentSavingsRate, 2, '.', '') }}"
                                                            required>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        Update rate
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        @if (!empty($deleted))
                                            <div class="alert alert-warning mb-3">
                                                <strong>Deleted customers view.</strong>
                                                These records have been soft-deleted by staff. Use
                                                <span class="fw-semibold">Hard delete</span> only when you are sure the
                                                customer data is no longer required.
                                            </div>
                                        @endif
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Account</th>
                                                        <th>Status</th>
                                                        <th>Flags</th>
                                                        <th>Deleted At</th>
                                                        <th class="text-end">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($customers as $customer)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">{{ $customer->first_name }}
                                                                    {{ $customer->last_name }}</div>
                                                                <div class="text-muted" style="font-size:0.9rem;">
                                                                    {{ $customer->phone }}</div>
                                                            </td>
                                                            <td>
                                                                <div>{{ $customer->account_number }}</div>
                                                                <div class="text-muted" style="font-size:0.9rem;">
                                                                    {{ $customer->account_type }}</div>
                                                            </td>
                                                            <td class="text-capitalize">{{ $customer->status }}</td>
                                                            <td>
                                                                @if ($customer->is_frozen)
                                                                    <span class="badge bg-warning">Frozen</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $customer->deleted_at ? $customer->deleted_at->format('M d, Y H:i') : '—' }}
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                                    <a href="{{ route('manager.customers.show', $customer->id) }}"
                                                                        class="btn btn-sm btn-outline-primary">View</a>
                                                                    @if ($customer->deleted_at)
                                                                        <form method="POST"
                                                                            action="{{ route('manager.customers.force-delete', $customer->id) }}"
                                                                            onsubmit="return confirm('This will permanently remove the customer and all related data. Continue?');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-danger">
                                                                                Hard delete
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">No
                                                                customers found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-3">
                                            {{ $customers->links() }}
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
