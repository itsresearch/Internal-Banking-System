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
                                        <h5 class="mb-0">Create Customer</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                            Create a new customer profile. You’ll upload documents on the next step.
                                        </p>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger m-3">
                                            <div class="fw-semibold mb-2">Please fix the highlighted errors:</div>
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="card-body">
                                        <form method="POST" action="{{ route('customers.store') }}">
                                            @csrf

                                            <div class="card shadow-sm mb-0">
                                                <div class="card-header bg-primary text-white">
                                                    <h5 class="mb-0">Customer Information</h5>
                                                </div>

                                                <div class="card-body">
                                                    <!-- Account Holder & Type -->
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Account Holder Type</label>
                                                            <select name="account_holder_type" id="accountHolderType"
                                                                class="form-select" required>
                                                                <option value="individual" selected>Individual</option>
                                                                <option value="business">Business</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Account Type</label>
                                                            <select name="account_type" id="accountType"
                                                                class="form-select" required>
                                                                <option value="savings" selected>Savings Account
                                                                </option>
                                                                <option value="current">Current Account</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Common balances -->
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Balance</label>
                                                            <input type="number" name="balance" class="form-control"
                                                                step="0.01" min="0" placeholder="e.g. 1000"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <!-- Savings-specific (info) -->
                                                    <div id="savingsFields" class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Interest Rate </label>
                                                            <input type="text" class="form-control" value="5%"
                                                                readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Account Opening Date</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ now()->toDateString() }}" readonly>
                                                        </div>
                                                    </div>

                                                    <!-- Current-specific -->
                                                    <div id="currentFields" class="row" style="display: none;">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Account Opening Date</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ now()->toDateString() }}" readonly>
                                                        </div>
                                                    </div>

                                                    <!-- Individual Extras -->
                                                    <div id="individualExtras" class="row">
                                                        <!-- Additional individual-only fields can go here -->
                                                    </div>

                                                    <!-- Business Details -->
                                                    <div id="businessFields" class="row" style="display: none;">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Business Name</label>
                                                            <input type="text" name="business_name"
                                                                class="form-control" placeholder="Company or firm name">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Business Type</label>
                                                            <select name="business_type" class="form-select">
                                                                <option value="">Select type</option>
                                                                <option value="company">Company</option>
                                                                <option value="firm">Firm</option>
                                                                <option value="proprietorship">Proprietorship</option>
                                                                <option value="other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Registration Number</label>
                                                            <input type="text" name="registration_number"
                                                                class="form-control" placeholder="Registration no.">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">PAN / VAT</label>
                                                            <input type="text" name="business_pan_vat"
                                                                class="form-control" placeholder="PAN / VAT number">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Business Phone</label>
                                                            <input type="text" name="business_phone"
                                                                class="form-control"
                                                                placeholder="Company contact number">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Business Email</label>
                                                            <input type="email" name="business_email"
                                                                class="form-control"
                                                                placeholder="accounts@company.com">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Authorized Signatory</label>
                                                            <input type="text" name="authorized_signatory"
                                                                class="form-control"
                                                                placeholder="Name of authorized signatory">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Business Address</label>
                                                            <input type="text" name="business_address"
                                                                class="form-control"
                                                                placeholder="Registered office address">
                                                        </div>
                                                    </div>

                                                    <!-- Names -->
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" name="first_name"
                                                                class="form-control" required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Middle Name</label>
                                                            <input type="text" name="middle_name"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" name="last_name"
                                                                class="form-control" required>
                                                        </div>
                                                    </div>

                                                    <!-- Parents -->
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Father's Name</label>
                                                            <input type="text" name="fathers_name"
                                                                class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Mother's Name</label>
                                                            <input type="text" name="mothers_name"
                                                                class="form-control" required>
                                                        </div>
                                                    </div>

                                                    <!-- DOB & Gender -->
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Date of Birth</label>
                                                            <input type="date" name="date_of_birth"
                                                                class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Gender</label>
                                                            <select name="gender" class="form-select" required>
                                                                <option value="">-- Select Gender --</option>
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                                <option value="other">Other</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Contact -->
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Phone</label>
                                                            <input type="text" name="phone" class="form-control"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" name="email" class="form-control"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <!-- Address -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Permanent Address</label>
                                                        <input type="text" name="permanent_address"
                                                            class="form-control" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Temporary Address</label>
                                                        <input type="text" name="temporary_address"
                                                            class="form-control" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Occupation</label>
                                                        <input type="text" name="occupation" class="form-control"
                                                            placeholder="e.g. Engineer">
                                                    </div>

                                                    <!-- Status -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <input type="text" class="form-control"
                                                            id="accountStatusNote" value="Active (individual)"
                                                            readonly>
                                                        <div class="form-text">Business accounts require manager
                                                            verification.</div>
                                                    </div>

                                                    <!-- System info (read-only) -->
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Customer ID</label>
                                                            <input type="text" class="form-control"
                                                                value="Auto after save" readonly>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Customer Code</label>
                                                            <input type="text" class="form-control"
                                                                value="Auto generated" readonly>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Created By (Staff)</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ auth()->id() }}" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-footer text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        Save Customer
                                                    </button>
                                                </div>
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
                                        </script>
                                        <a href="javascript:void(0);" target="_blank">Research Bank of Nepal</a>
                                        <span class="text-muted">, All rights reserved</span>
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
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <script>
        (function() {
            const holderSelect = document.getElementById('accountHolderType');
            const businessFields = document.getElementById('businessFields');
            const accountTypeSelect = document.getElementById('accountType');
            const savingsFields = document.getElementById('savingsFields');
            const currentFields = document.getElementById('currentFields');
            const individualExtras = document.getElementById('individualExtras');
            const businessRequiredFields = [
                document.querySelector('input[name="business_name"]'),
                document.querySelector('input[name="business_pan_vat"]'),
                document.querySelector('input[name="business_phone"]'),
                document.querySelector('input[name="business_email"]'),
                document.querySelector('select[name="business_type"]'),
                document.querySelector('input[name="registration_number"]'),
                document.querySelector('input[name="authorized_signatory"]'),
                document.querySelector('input[name="business_address"]'),
            ];
            const statusNote = document.getElementById('accountStatusNote');

            function toggleBusinessFields() {
                const isBusiness = holderSelect.value === 'business';
                businessFields.style.display = isBusiness ? 'flex' : 'none';
                individualExtras.style.display = isBusiness ? 'none' : 'flex';
                businessRequiredFields.forEach((field) => {
                    if (field) field.required = isBusiness;
                });
                if (statusNote) {
                    statusNote.value = isBusiness ? 'Pending (business)' : 'Active (individual)';
                }
            }

            function toggleAccountTypeFields() {
                const isSavings = accountTypeSelect.value === 'savings';
                savingsFields.style.display = isSavings ? 'flex' : 'none';
                currentFields.style.display = isSavings ? 'none' : 'flex';
            }

            holderSelect?.addEventListener('change', toggleBusinessFields);
            accountTypeSelect?.addEventListener('change', toggleAccountTypeFields);

            toggleBusinessFields();
            toggleAccountTypeFields();
        })();
    </script>
</body>

</html>
