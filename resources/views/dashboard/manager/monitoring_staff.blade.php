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
                                                <h5 class="mb-0">Staff Activity Monitoring</h5>
                                                <p class="text-muted mb-0">Filter transactions by staff and date.</p>
                                            </div>
                                            <form method="GET" class="d-flex gap-2 flex-wrap">
                                                <select name="staff_id" class="form-select form-select-sm"
                                                    style="min-width: 180px;">
                                                    <option value="">All staff</option>
                                                    @foreach ($staff as $member)
                                                        <option value="{{ $member->id }}"
                                                            @if ($staffId == $member->id) selected @endif>
                                                            {{ $member->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="date" name="from"
                                                    class="form-control form-control-sm" value="{{ $from }}">
                                                <input type="date" name="to"
                                                    class="form-control form-control-sm" value="{{ $to }}">
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-primary">Filter</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Staff</th>
                                                        <th>Customer</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Flags</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($transactions as $tx)
                                                        <tr>
                                                            <td>{{ $tx->created_at?->format('M d, Y H:i') }}</td>
                                                            <td>{{ $tx->createdBy?->name ?? 'Unknown' }}</td>
                                                            <td>{{ $tx->customer?->first_name }}
                                                                {{ $tx->customer?->last_name }}</td>
                                                            <td class="text-capitalize">{{ $tx->transaction_type }}</td>
                                                            <td>NPR {{ number_format($tx->amount, 2) }}</td>
                                                            <td>
                                                                @if ($tx->amount > 100000)
                                                                    <span class="badge bg-warning">High value</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">No
                                                                transactions for the selected filters.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

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
