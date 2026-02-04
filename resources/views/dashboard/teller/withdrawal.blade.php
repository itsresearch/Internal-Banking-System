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
                                            <div class="row g-4">
                                                <div class="col-lg-7">
                                                    <div class="row g-3">
                                                        <div class="col-md-12">
                                                            <label class="form-label">Customer Account</label>
                                                            <select id="withdrawCustomer" name="customer_id" class="form-select" required>
                                                                <option value="">-- Select Customer --</option>
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
                                                            <div class="helper-text mt-1">Savings cannot go negative; overdraft applies only to enabled current accounts.</div>
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
                                                    <div class="p-3 border rounded-3 bg-light">
                                                        <div class="section-title">Account summary</div>
                                                        <div class="helper-text mb-2">Populates after you choose an account.</div>
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
            const customerSelect = document.getElementById('withdrawCustomer');
            const summary = document.getElementById('withdrawSummary');

            function formatCurrency(val) {
                const num = Number(val);
                if (Number.isNaN(num)) return '—';
                return 'NPR ' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function updateSummary(option) {
                if (!option || !summary) return;
                const balance = option.getAttribute('data-balance');
                const type = option.getAttribute('data-account-type');
                const overdraft = option.getAttribute('data-overdraft');
                const overdraftLimit = option.getAttribute('data-overdraft-limit');

                summary.innerHTML = `
                    <div>Balance: <strong>${formatCurrency(balance)}</strong></div>
                    <div>Type: <strong>${type ? type.toUpperCase() : '—'}</strong></div>
                    <div>Overdraft: <strong>${overdraft === 'yes' ? 'Enabled up to ' + formatCurrency(overdraftLimit) : 'Not allowed'}</strong></div>
                `;
            }

            if (customerSelect) {
                customerSelect.addEventListener('change', function () {
                    const selected = customerSelect.options[customerSelect.selectedIndex];
                    updateSummary(selected);
                });
            }

            if (form) {
                form.addEventListener('submit', function () {
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
