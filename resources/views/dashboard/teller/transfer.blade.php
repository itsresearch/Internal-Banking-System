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
                                        <h5 class="mb-0">Internal Transfer</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.95rem;">
                                            Same-day transfers; requests above NPR 2,000,000 go to approval.
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

                                        <form id="transferForm" method="POST"
                                            action="{{ route('teller.transfer.store') }}">
                                            @csrf
                                            <input type="hidden" name="from_customer_id" id="fromCustomerId" required>
                                            <input type="hidden" name="to_customer_id" id="toCustomerId" required>
                                            <div class="row g-4">
                                                <div class="col-lg-7">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">From Account</label>
                                                            <input id="fromSearch" type="text" class="form-control"
                                                                placeholder="Search by name, account number, or citizenship number"
                                                                autocomplete="off" required>
                                                            <div class="helper-text mt-1">Source account will be debited
                                                                first.</div>
                                                            <div id="fromSearchResults" class="list-group mt-2"
                                                                style="display:none;"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">To Account</label>
                                                            <input id="toSearch" type="text" class="form-control"
                                                                placeholder="Search by name, account number, or citizenship number"
                                                                autocomplete="off" required>
                                                            <div class="helper-text mt-1">Destination account will be
                                                                credited.</div>
                                                            <div id="toSearchResults" class="list-group mt-2"
                                                                style="display:none;"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Amount</label>
                                                            <input type="number" name="amount" class="form-control"
                                                                step="0.01" min="0.01" placeholder="0.00"
                                                                required>
                                                            <div class="helper-text mt-1">Enter amount in NPR.</div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label">Notes</label>
                                                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional note for audit trail"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="p-3 border rounded-3 bg-light mb-3">
                                                        <div class="section-title">Source summary</div>
                                                        <div id="fromSummary" class="helper-text">
                                                            <div>Balance: —</div>
                                                            <div>Type: —</div>
                                                        </div>
                                                    </div>
                                                    <div class="p-3 border rounded-3 bg-light">
                                                        <div class="section-title">Destination summary</div>
                                                        <div id="toSummary" class="helper-text">
                                                            <div>Balance: —</div>
                                                            <div>Type: —</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert alert-warning mt-3 mb-0">
                                                Avoid selecting the same account for both fields. We will block the
                                                submission if detected.
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button type="reset" class="btn btn-outline-secondary">Clear</button>
                                                <button type="submit" class="btn btn-info"
                                                    data-disable-on-submit>Process Transfer</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const fromSearchInput = document.getElementById('fromSearch');
            const toSearchInput = document.getElementById('toSearch');
            const fromResults = document.getElementById('fromSearchResults');
            const toResults = document.getElementById('toSearchResults');
            const fromCustomerIdInput = document.getElementById('fromCustomerId');
            const toCustomerIdInput = document.getElementById('toCustomerId');
            const fromSummary = document.getElementById('fromSummary');
            const toSummary = document.getElementById('toSummary');
            const form = document.getElementById('transferForm');

            let fromDebounceTimer = null;
            let toDebounceTimer = null;
            let lastFromQuery = '';
            let lastToQuery = '';

            function formatCurrency(val) {
                const num = Number(val);
                if (Number.isNaN(num)) return '—';
                return 'NPR ' + num.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function renderFrom(item) {
                if (!item || !fromSummary) return;
                fromSummary.innerHTML = `
                    <div>Balance: <strong>${formatCurrency(item.balance)}</strong></div>
                    <div>Type: <strong>${(item.account_type || '—').toUpperCase()}</strong></div>
                `;
            }

            function renderTo(item) {
                if (!item || !toSummary) return;
                toSummary.innerHTML = `
                    <div>Balance: <strong>${formatCurrency(item.balance)}</strong></div>
                    <div>Type: <strong>${(item.account_type || '—').toUpperCase()}</strong></div>
                `;
            }

            function buildResultButton(item, onSelect) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.innerHTML =
                    `<div class="fw-semibold">${item.name}</div>
                    <div class="text-muted" style="font-size:0.9rem;">${item.account_number || '—'} • ${item.account_type || '—'} • Balance: ${formatCurrency(item.balance)}</div>`;
                btn.addEventListener('click', () => onSelect(item));
                return btn;
            }

            function showResults(target, items, onSelect) {
                if (!target) return;
                target.innerHTML = '';
                if (!items || items.length === 0) {
                    target.style.display = 'none';
                    return;
                }
                items.forEach((item) => {
                    target.appendChild(buildResultButton(item, onSelect));
                });
                target.style.display = 'block';
            }

            async function runSearch(q) {
                const url = new URL("{{ route('customers.search') }}", window.location.origin);
                url.searchParams.set('q', q);
                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) return [];
                return await res.json();
            }

            if (fromSearchInput && fromResults) {
                fromSearchInput.addEventListener('input', function() {
                    const q = (fromSearchInput.value || '').trim();
                    if (q.length < 2) {
                        fromResults.style.display = 'none';
                        return;
                    }
                    lastFromQuery = q;
                    clearTimeout(fromDebounceTimer);
                    fromDebounceTimer = setTimeout(async () => {
                        const items = await runSearch(q);
                        if (lastFromQuery !== q) return;
                        showResults(fromResults, items, (item) => {
                            if (toCustomerIdInput && toCustomerIdInput.value && String(
                                    toCustomerIdInput.value) === String(item.id)) {
                                alert(
                                    'Source and destination accounts must be different.'
                                );
                                return;
                            }
                            if (fromCustomerIdInput) fromCustomerIdInput.value = item
                                .id;
                            fromSearchInput.value =
                                `${item.name} (${item.account_number || '—'})`;
                            renderFrom(item);
                            fromResults.style.display = 'none';
                        });
                    }, 250);
                });
            }

            if (toSearchInput && toResults) {
                toSearchInput.addEventListener('input', function() {
                    const q = (toSearchInput.value || '').trim();
                    if (q.length < 2) {
                        toResults.style.display = 'none';
                        return;
                    }
                    lastToQuery = q;
                    clearTimeout(toDebounceTimer);
                    toDebounceTimer = setTimeout(async () => {
                        const items = await runSearch(q);
                        if (lastToQuery !== q) return;
                        showResults(toResults, items, (item) => {
                            if (fromCustomerIdInput && fromCustomerIdInput.value &&
                                String(fromCustomerIdInput.value) === String(item.id)) {
                                alert(
                                    'Source and destination accounts must be different.'
                                );
                                return;
                            }
                            if (toCustomerIdInput) toCustomerIdInput.value = item.id;
                            toSearchInput.value =
                                `${item.name} (${item.account_number || '—'})`;
                            renderTo(item);
                            toResults.style.display = 'none';
                        });
                    }, 250);
                });
            }

            document.addEventListener('click', function(e) {
                if (fromResults && fromSearchInput && !fromResults.contains(e.target) && e.target !==
                    fromSearchInput) {
                    fromResults.style.display = 'none';
                }
                if (toResults && toSearchInput && !toResults.contains(e.target) && e.target !==
                    toSearchInput) {
                    toResults.style.display = 'none';
                }
            });

            if (form) {
                form.addEventListener('submit', function(e) {
                    if (fromCustomerIdInput && !fromCustomerIdInput.value) {
                        e.preventDefault();
                        alert('Please search and select a source customer first.');
                        return;
                    }
                    if (toCustomerIdInput && !toCustomerIdInput.value) {
                        e.preventDefault();
                        alert('Please search and select a destination customer first.');
                        return;
                    }
                    if (fromCustomerIdInput && toCustomerIdInput && fromCustomerIdInput.value &&
                        toCustomerIdInput.value && fromCustomerIdInput.value === toCustomerIdInput.value) {
                        e.preventDefault();
                        alert('Source and destination accounts must be different.');
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
