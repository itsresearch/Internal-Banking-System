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
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Withdrawal</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.95rem;">
                                            Requests above NPR 100,000 will route for manager approval.
                                        </p>
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

                                        <form id="withdrawalForm" method="POST" action="{{ route('teller.withdrawal.store') }}">
                                            @csrf
                                            <input type="hidden" name="customer_id" id="withdrawCustomerId" required>
                                            <div class="row g-4">
                                                <div class="col-lg-7">
                                                    <div class="row g-3">
                                                        <div class="col-md-12">
                                                            <label class="form-label">Customer (search)</label>
                                                            <input id="withdrawSearch" type="text" class="form-control"
                                                                placeholder="Search by name, account number, or citizenship number"
                                                                autocomplete="off" required>
                                                            <div class="helper-text mt-1">Select a customer to populate the summary.</div>
                                                            <div id="withdrawSearchResults" class="list-group mt-2" style="display:none;"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Amount</label>
                                                            <input type="number" name="amount" class="form-control"
                                                                step="0.01" min="10" placeholder="0.00" required>
                                                            <div class="helper-text mt-1">Enter amount in NPR (min NPR 10).</div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label">Notes</label>
                                                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional note for audit trail"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="p-3 border rounded-3 bg-light">
                                                        <div class="section-title">Account summary</div>
                                                        <div class="helper-text mb-2">Populates after you select a customer.</div>
                                                        <div id="withdrawSummary" class="text-muted">
                                                            <div>Balance: —</div>
                                                            <div>Type: —</div>
                                                            <div>Overdraft: —</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 mt-4">
                                                <button type="reset" class="btn btn-outline-secondary">Clear</button>
                                                <button type="submit" class="btn btn-danger" data-disable-on-submit>
                                                    Process Withdrawal
                                                </button>
                                            </div>
                                        </form>
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
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('withdrawalForm');
            const searchInput = document.getElementById('withdrawSearch');
            const results = document.getElementById('withdrawSearchResults');
            const summary = document.getElementById('withdrawSummary');
            const customerIdInput = document.getElementById('withdrawCustomerId');

            function formatCurrency(val) {
                const num = Number(val);
                if (Number.isNaN(num)) return '—';
                return 'NPR ' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            let debounceTimer = null;
            let lastQuery = '';

            function showResults(items) {
                if (!results) return;
                results.innerHTML = '';
                if (!items || items.length === 0) {
                    results.style.display = 'none';
                    return;
                }

                items.forEach((item) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action';
                    btn.innerHTML = `<div class="fw-semibold">${item.name}</div>
                        <div class="text-muted" style="font-size:0.9rem;">${item.account_number || '—'} • ${item.account_type || '—'} • Balance: ${formatCurrency(item.opening_balance)}</div>`;
                    btn.addEventListener('click', () => {
                        if (customerIdInput) customerIdInput.value = item.id;
                        if (searchInput) searchInput.value = `${item.name} (${item.account_number || '—'})`;
                        if (summary) {
                            summary.innerHTML = `
                                <div>Balance: <strong>${formatCurrency(item.opening_balance)}</strong></div>
                                <div>Type: <strong>${(item.account_type || '—').toUpperCase()}</strong></div>
                                <div>Overdraft: <strong>${item.overdraft_enabled ? 'Enabled up to ' + formatCurrency(item.overdraft_limit) : 'Not allowed'}</strong></div>
                            `;
                        }
                        results.style.display = 'none';
                    });
                    results.appendChild(btn);
                });
                results.style.display = 'block';
            }

            async function runSearch(q) {
                const url = new URL("{{ route('customers.search') }}", window.location.origin);
                url.searchParams.set('q', q);
                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return [];
                return await res.json();
            }

            if (searchInput && results) {
                searchInput.addEventListener('input', function () {
                    const q = (searchInput.value || '').trim();
                    if (q.length < 2) {
                        results.style.display = 'none';
                        return;
                    }
                    lastQuery = q;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(async () => {
                        const items = await runSearch(q);
                        if (lastQuery !== q) return;
                        showResults(items);
                    }, 250);
                });

                document.addEventListener('click', function (e) {
                    if (!results.contains(e.target) && e.target !== searchInput) {
                        results.style.display = 'none';
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    if (customerIdInput && !customerIdInput.value) {
                        e.preventDefault();
                        alert('Please search and select a customer first.');
                        return;
                    }
                    const submitBtn = form.querySelector('[data-disable-on-submit]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Processing...';
                    }
                });
            }
        });
    </script>
</body>

</html>
