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
                                            Same-day transfers; requests above NPR 100,000 go to approval.
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

                                        <form id="transferForm" method="POST" action="{{ route('teller.transfer.store') }}">
                                            @csrf
                                            <div class="row g-4">
                                                <div class="col-lg-7">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">From Account</label>
                                                            <select id="fromCustomer" name="from_customer_id" class="form-select" required>
                                                                <option value="">-- Select Source Customer --</option>
                                                                @foreach ($customers as $customer)
                                                                    <option value="{{ $customer->id }}"
                                                                        data-balance="{{ $customer->opening_balance }}"
                                                                        data-account-type="{{ $customer->account_type }}"
                                                                        data-overdraft="{{ $customer->overdraft_enabled ? 'yes' : 'no' }}"
                                                                        data-overdraft-limit="{{ $customer->overdraft_limit ?? 0 }}">
                                                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                                                        ({{ $customer->account_number }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="helper-text mt-1">Source account will be debited first.</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">To Account</label>
                                                            <select id="toCustomer" name="to_customer_id" class="form-select" required>
                                                                <option value="">-- Select Destination Customer --</option>
                                                                @foreach ($customers as $customer)
                                                                    <option value="{{ $customer->id }}"
                                                                        data-balance="{{ $customer->opening_balance }}"
                                                                        data-account-type="{{ $customer->account_type }}">
                                                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                                                        ({{ $customer->account_number }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="helper-text mt-1">Destination account will be credited.</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Amount</label>
                                                            <input type="number" name="amount" class="form-control"
                                                                step="0.01" min="0.01" placeholder="0.00" required>
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
                                                            <div>Overdraft: —</div>
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
                                                Avoid selecting the same account for both fields. We will block the submission if detected.
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button type="reset" class="btn btn-outline-secondary">Clear</button>
                                                <button type="submit" class="btn btn-info" data-disable-on-submit>Process Transfer</button>
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
            const fromSelect = document.getElementById('fromCustomer');
            const toSelect = document.getElementById('toCustomer');
            const fromSummary = document.getElementById('fromSummary');
            const toSummary = document.getElementById('toSummary');
            const form = document.getElementById('transferForm');

            function formatCurrency(val) {
                const num = Number(val);
                if (Number.isNaN(num)) return '—';
                return 'NPR ' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function renderFrom(option) {
                if (!option || !fromSummary) return;
                fromSummary.innerHTML = `
                    <div>Balance: <strong>${formatCurrency(option.getAttribute('data-balance'))}</strong></div>
                    <div>Type: <strong>${(option.getAttribute('data-account-type') || '—').toUpperCase()}</strong></div>
                    <div>Overdraft: <strong>${
                        option.getAttribute('data-overdraft') === 'yes'
                            ? 'Enabled up to ' + formatCurrency(option.getAttribute('data-overdraft-limit'))
                            : 'Not allowed'
                    }</strong></div>
                `;
            }

            function renderTo(option) {
                if (!option || !toSummary) return;
                toSummary.innerHTML = `
                    <div>Balance: <strong>${formatCurrency(option.getAttribute('data-balance'))}</strong></div>
                    <div>Type: <strong>${(option.getAttribute('data-account-type') || '—').toUpperCase()}</strong></div>
                `;
            }

            if (fromSelect) {
                fromSelect.addEventListener('change', function () {
                    const opt = fromSelect.options[fromSelect.selectedIndex];
                    renderFrom(opt);
                });
            }

            if (toSelect) {
                toSelect.addEventListener('change', function () {
                    const opt = toSelect.options[toSelect.selectedIndex];
                    renderTo(opt);
                });
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    if (fromSelect && toSelect && fromSelect.value && toSelect.value && fromSelect.value === toSelect.value) {
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
