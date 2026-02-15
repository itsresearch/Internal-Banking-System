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
                                                <h5 class="mb-0">Daily Transaction Summary</h5>
                                                <p class="text-muted mb-0">Track totals and transaction volume across a
                                                    date range.</p>
                                            </div>
                                            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                                                <input type="date" name="from"
                                                    class="form-control form-control-sm"
                                                    value="{{ $from->toDateString() }}">
                                                <input type="date" name="to"
                                                    class="form-control form-control-sm"
                                                    value="{{ $to->toDateString() }}">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    type="submit">Apply</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Total deposits</div>
                                                    <div class="fs-4 fw-semibold">NPR
                                                        {{ number_format($summary['totalDeposits'], 2) }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Total withdrawals</div>
                                                    <div class="fs-4 fw-semibold">NPR
                                                        {{ number_format($summary['totalWithdrawals'], 2) }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="text-muted">Total transfers</div>
                                                    <div class="fs-4 fw-semibold">NPR
                                                        {{ number_format($summary['totalTransfers'], 2) }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-3">
                                            <div class="col-lg-6">
                                                <div class="p-3 border rounded-3 bg-white h-100">
                                                    <div class="fw-semibold mb-2">Daily totals</div>
                                                    <canvas id="dailyTotalsChart" height="180"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="p-3 border rounded-3 bg-white h-100">
                                                    <div class="fw-semibold mb-2">Transactions over time</div>
                                                    <canvas id="transactionRangeChart" height="180"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <h6 class="mb-3">Cash handled by staff</h6>
                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Staff</th>
                                                            <th class="text-end">Total cash</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($summary['cashByStaff'] as $row)
                                                            <tr>
                                                                <td>{{ $row->createdBy?->name ?? 'Unknown' }}</td>
                                                                <td class="text-end">NPR
                                                                    {{ number_format($row->total_amount, 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="2" class="text-center text-muted">No
                                                                    cash handled yet.</td>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalsCtx = document.getElementById('dailyTotalsChart');
            const rangeCtx = document.getElementById('transactionRangeChart');

            if (totalsCtx) {
                new Chart(totalsCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Deposits', 'Withdrawals', 'Transfers'],
                        datasets: [{
                            label: 'Amount (NPR)',
                            data: [
                                {{ $summary['totalDeposits'] }},
                                {{ $summary['totalWithdrawals'] }},
                                {{ $summary['totalTransfers'] }}
                            ],
                            backgroundColor: ['#2563eb', '#ef4444', '#14b8a6']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            if (rangeCtx) {
                const rangeLabels = @json($summary['seriesLabels']);
                const rangeTotals = @json($summary['seriesTotals']);

                new Chart(rangeCtx, {
                    type: 'line',
                    data: {
                        labels: rangeLabels,
                        datasets: [{
                            label: 'Total amount (NPR)',
                            data: rangeTotals,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.15)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>
