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
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Verify Customer Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('customers.update', $customer->id) }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <!-- Customer Details -->
                                            <h6 class="text-primary mb-3">Customer Information</h6>
                                            <div class="row mb-4">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Account Type</label>
                                                    <select name="account_type" class="form-control" required disabled>
                                                        <option value="savings"
                                                            {{ $customer->account_type === 'savings' ? 'selected' : '' }}>
                                                            Savings (individuals)</option>
                                                        <option value="current"
                                                            {{ $customer->account_type === 'current' ? 'selected' : '' }}>
                                                            Current (business)</option>
                                                    </select>
                                                    {{-- <small class="text-muted">Savings: demo 4%-6% interest with limited
                                                        withdrawals. Current: unlimited transactions, no
                                                        interest.</small> --}}
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Account Holder Type</label>
                                                    <select name="account_holder_type" id="accountHolderType"
                                                        class="form-control" required disabled>
                                                        <option value="individual"
                                                            {{ $customer->account_holder_type === 'individual' ? 'selected' : '' }}>
                                                            Individual</option>
                                                        <option value="business"
                                                            {{ $customer->account_holder_type === 'business' ? 'selected' : '' }}>
                                                            Business</option>
                                                    </select>
                                                    {{-- <small class="text-muted">Choose Business to reveal business
                                                        details.</small> --}}
                                                </div>
                                                <div id="businessFields" class="row" style="display: none;">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Business Name</label>
                                                        <input type="text" name="business_name" class="form-control"
                                                            readonly value="{{ $customer->business_name }}"
                                                            placeholder="Company or firm name">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">PAN / VAT</label>
                                                        <input type="text" name="business_pan_vat"
                                                            class="form-control" readonly
                                                            value="{{ $customer->business_pan_vat }}"
                                                            placeholder="PAN / VAT number">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Business Phone</label>
                                                        <input type="text" name="business_phone" class="form-control"
                                                            readonly value="{{ $customer->business_phone }}"
                                                            placeholder="Company contact number">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Business Email</label>
                                                        <input type="email" name="business_email" class="form-control"
                                                            readonly value="{{ $customer->business_email }}"
                                                            placeholder="accounts@company.com">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Savings Withdrawal Limit (per
                                                        month)</label>
                                                    <input type="number" name="monthly_withdrawal_limit"
                                                        class="form-control" min="0" readonly
                                                        value="{{ $customer->monthly_withdrawal_limit }}">
                                                    {{-- <small class="text-muted">Applies to Savings. For Current, leave
                                                        blank (unlimited).</small> --}}
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Overdraft Limit (Current)</label>
                                                    <input type="number" name="overdraft_limit" class="form-control"
                                                        min="0" step="0.01" readonly
                                                        value="{{ $customer->overdraft_limit }}">
                                                    {{-- <small class="text-muted">Applies to Current. Savings has no
                                                        overdraft.</small> --}}
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Interest Rate</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $customer->interest_rate }}% (manager assigned)"
                                                        readonly>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">First Name</label>
                                                    <input type="text" name="first_name" class="form-control"
                                                        readonly value="{{ $customer->first_name }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Middle Name</label>
                                                    <input type="text" name="middle_name" class="form-control"
                                                        readonly value="{{ $customer->middle_name }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Last Name</label>
                                                    <input type="text" name="last_name" class="form-control" readonly
                                                        value="{{ $customer->last_name }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Father's Name</label>
                                                    <input type="text" name="fathers_name" class="form-control"
                                                        readonly value="{{ $customer->fathers_name }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Mother's Name</label>
                                                    <input type="text" name="mothers_name" class="form-control"
                                                        readonly value="{{ $customer->mothers_name }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ $customer->email }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Phone</label>
                                                    <input type="text" name="phone" class="form-control"
                                                        value="{{ $customer->phone }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Customer Code</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $customer->customer_code }}" readonly>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Account Number</label>
                                                    <input type="text" name="account_number" class="form-control"
                                                        value="{{ $customer->account_number }}" readonly>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Date of Birth</label>
                                                    <input type="date" name="date_of_birth" class="form-control"
                                                        readonly value="{{ $customer->date_of_birth }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Gender</label>
                                                    <select name="gender" class="form-control" required disabled>
                                                        <option value="male"
                                                            {{ $customer->gender == 'male' ? 'selected' : '' }}>Male
                                                        </option>
                                                        <option value="female"
                                                            {{ $customer->gender == 'female' ? 'selected' : '' }}>
                                                            Female</option>
                                                        <option value="other"
                                                            {{ $customer->gender == 'other' ? 'selected' : '' }}>Other
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="active"
                                                            {{ $customer->status == 'active' ? 'selected' : '' }}>
                                                            Active</option>
                                                        <option value="inactive"
                                                            {{ $customer->status == 'inactive' ? 'selected' : '' }}>
                                                            Frozen</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Permanent Address</label>
                                                    <textarea name="permanent_address" class="form-control" rows="2" required>{{ $customer->permanent_address }}</textarea>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Temporary Address</label>
                                                    <textarea name="temporary_address" class="form-control" rows="2" required>{{ $customer->temporary_address }}</textarea>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nominee Name</label>
                                                    <input type="text" name="nominee_name" class="form-control"
                                                        value="{{ $customer->nominee_name }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nominee Relation</label>
                                                    <input type="text" name="nominee_relation"
                                                        class="form-control"
                                                        value="{{ $customer->nominee_relation }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Authorized Signatory</label>
                                                    <input type="text" name="authorized_signatory"
                                                        class="form-control"
                                                        value="{{ $customer->authorized_signatory }}">
                                                </div>
                                            </div>

                                            <!-- Documents -->
                                            <h6 class="text-primary mb-3">Uploaded Documents</h6>
                                            @if ($customer->documents->count() > 0)
                                                <div class="row">
                                                    @foreach ($customer->documents as $document)
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card h-100">
                                                                <div class="card-body">
                                                                    <h6 class="card-title">
                                                                        {{ ucfirst($document->document_type) }}
                                                                        {{ $document->document_side ? '(' . ucfirst($document->document_side) . ')' : '' }}
                                                                    </h6>
                                                                    <p class="card-text">
                                                                        <strong>Number:</strong>
                                                                        {{ $document->document_number }}<br>
                                                                        <strong>Uploaded:</strong>
                                                                        {{ $document->uploaded_at }}
                                                                    </p>
                                                                    <a href="{{ asset('storage/' . $document->file_path) }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-primary">View
                                                                        Document</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">No documents uploaded yet.</p>
                                            @endif

                                            <h6 class="text-primary mb-3 mt-4">KYC Document Updates (Re-upload)</h6>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Citizenship Number</label>
                                                    <input type="text" name="citizenship_number"
                                                        class="form-control" value="">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Citizenship Front</label>
                                                    <input type="file" name="citizenship_front"
                                                        class="form-control" accept="image/*">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Citizenship Back</label>
                                                    <input type="file" name="citizenship_back"
                                                        class="form-control" accept="image/*">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Customer Photo</label>
                                                    <input type="file" name="customer_photo" class="form-control"
                                                        accept="image/*">
                                                </div>
                                            </div>

                                            <div class="text-end mb-3">
                                                <button type="submit" class="btn btn-primary">Update Details</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-end">
                                        <form method="POST" action="{{ route('customers.verify.confirm') }}"
                                            style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                            <button type="submit" class="btn btn-success">Confirm & Verify
                                                Customer</button>
                                        </form>
                                        <a href="{{ route('customers.documents.create', $customer->id) }}"
                                            class="btn btn-secondary ms-2">Back</a>
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
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script>
        (function() {
            const holderSelect = document.getElementById('accountHolderType');
            const businessFields = document.getElementById('businessFields');

            function toggleBusiness() {
                const show = holderSelect.value === 'business';
                businessFields.style.display = show ? 'flex' : 'none';
            }

            holderSelect?.addEventListener('change', toggleBusiness);
            toggleBusiness();
        })();
    </script>

    <!-- Page JS -->
</body>

</html>
