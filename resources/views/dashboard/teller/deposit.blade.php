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
                                        <h5 class="mb-0">Deposit</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.95rem;">
                                            Funds post immediately for active accounts.
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

                                        <form id="depositForm" method="POST" action="{{ route('teller.deposit.store') }}">
                                            @csrf
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <label class="form-label">Customer Account</label>
                                                    <select name="customer_id" class="form-select" required>
                                                        <option value="">-- Select Customer --</option>
                                                        @foreach ($customers as $customer)
                                                            <option value="{{ $customer->id }}">
                                                                {{ $customer->first_name }} {{ $customer->last_name }}
                                                                ({{ $customer->account_number }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="helper-text mt-1">Deposits go to available balance instantly.</div>
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
                                            <div class="d-flex justify-content-end gap-2 mt-4">
                                                <button type="reset" class="btn btn-outline-secondary">Clear</button>
                                                <button type="submit" class="btn btn-primary" data-disable-on-submit>
                                                    Process Deposit
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
            const form = document.getElementById('depositForm');
            if (!form) return;

            form.addEventListener('submit', function (e) {
                const submitBtn = form.querySelector('[data-disable-on-submit]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';
                }
            });
        });
    </script>
</body>

</html>
