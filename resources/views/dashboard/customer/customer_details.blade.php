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
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="mb-0">Customer Details</h5>
                                            <p class="text-muted mb-0" style="font-size: 0.95rem;">
                                                Account summary and latest activity.
                                            </p>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="{{ route('teller.deposit') }}"
                                                class="btn btn-outline-secondary">Deposit</a>
                                            <a href="{{ route('teller.withdrawal') }}"
                                                class="btn btn-outline-secondary">Withdrawal</a>
                                            <a href="{{ route('teller.transfer') }}"
                                                class="btn btn-outline-secondary">Transfer</a>
                                            <a href="{{ route('customers.show', $customer->id) }}"
                                                class="btn btn-primary">Edit Customer</a>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="section-title">Current balance</div>
                                                    <div class="fs-5 fw-semibold mt-1">
                                                        {{ number_format($currentBalance ?? 0, 2) }}
                                                    </div>
                                                </div>
                                                {{-- <div class="helper-text">This value is the customer’s
                                                    balance updated by approved transactions.</div> --}}
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="section-title">Account status</div>
                                                    <div class="mt-2">
                                                        <span
                                                            class="badge {{ ($customer->status ?? '') === 'active' ? 'status-approved' : 'status-rejected' }}">
                                                            {{ ucfirst($customer->status ?? 'N/A') }}
                                                        </span>
                                                        <div class="helper-text mt-2">Inactive accounts cannot transact.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light h-100">
                                                    <div class="section-title">Customer</div>
                                                    <div class="fw-semibold">{{ $customer->first_name }}
                                                        {{ $customer->last_name }}</div>
                                                    <div class="text-muted" style="font-size: 0.95rem;">
                                                        {{ $customer->account_number ?? 'N/A' }}</div>
                                                    <div class="helper-text mt-2">{{ $customer->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="text-primary mb-3">Account Information</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Customer Code</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->customer_code ?? 'N/A' }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Account Number</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->account_number ?? 'N/A' }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Account Holder Type</label>
                                                <input type="text" class="form-control"
                                                    value="{{ ucfirst($customer->account_holder_type) }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Account Type</label>
                                                <input type="text" class="form-control"
                                                    value="{{ ucfirst($customer->account_type) }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Balance</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->balance }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Interest Rate</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->interest_rate }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Account Opened At</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->account_opened_at ?? 'N/A' }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Status</label>
                                                <input type="text" class="form-control"
                                                    value="{{ ucfirst($customer->status) }}" readonly>
                                            </div>
                                        </div>

                                        <h6 class="text-primary mb-3 mt-3">Personal Information</h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">First Name</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->first_name }}" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Middle Name</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->middle_name ?? 'N/A' }}" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->last_name }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Father's Name</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->fathers_name ?? 'N/A' }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Mother's Name</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->mothers_name ?? 'N/A' }}" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Date of Birth</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->date_of_birth }}" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Gender</label>
                                                <input type="text" class="form-control"
                                                    value="{{ ucfirst($customer->gender) }}" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Occupation</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->occupation ?? 'N/A' }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->email }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->phone }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Permanent Address</label>
                                                <textarea class="form-control" rows="2" readonly>{{ $customer->permanent_address }}</textarea>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Temporary Address</label>
                                                <textarea class="form-control" rows="2" readonly>{{ $customer->temporary_address }}</textarea>
                                            </div>
                                        </div>

                                        <h6 class="text-primary mb-3 mt-3">Business Information</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Business Name</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->businessAccount?->business_name ?? 'N/A' }}"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Business PAN/VAT</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->businessAccount?->business_pan_vat ?? 'N/A' }}"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Business Phone</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->businessAccount?->business_phone ?? 'N/A' }}"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Business Email</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->businessAccount?->business_email ?? 'N/A' }}"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Business Type</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->businessAccount?->business_type ?? 'N/A' }}"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Registration Number</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customer->businessAccount?->registration_number ?? 'N/A' }}"
                                                    readonly>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Business Address</label>
                                                <textarea class="form-control" rows="2" readonly>{{ $customer->businessAccount?->business_address ?? 'N/A' }}</textarea>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <h6 class="mb-3">Documents</h6>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <div class="fw-semibold mb-2">Photo</div>
                                                    @if ($photo)
                                                        <img src="{{ asset('storage/' . $photo->file_path) }}"
                                                            alt="Customer Photo" class="img-fluid rounded">
                                                    @else
                                                        <div class="text-muted">Not uploaded</div>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="fw-semibold mb-2">Citizenship (Front)</div>
                                                    @if ($citizenship_front)
                                                        <img src="{{ asset('storage/' . $citizenship_front->file_path) }}"
                                                            alt="Citizenship Front" class="img-fluid rounded">
                                                    @else
                                                        <div class="text-muted">Not uploaded</div>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="fw-semibold mb-2">Citizenship (Back)</div>
                                                    @if ($citizenship_back)
                                                        <img src="{{ asset('storage/' . $citizenship_back->file_path) }}"
                                                            alt="Citizenship Back" class="img-fluid rounded">
                                                    @else
                                                        <div class="text-muted">Not uploaded</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="mb-0">Transactions (latest 50)</h6>
                                                <a href="{{ route('teller.history') }}"
                                                    class="btn btn-sm btn-outline-secondary">Open full history</a>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Reference</th>
                                                            <th>Type</th>
                                                            <th class="text-end">Amount</th>
                                                            <th class="text-end">Before</th>
                                                            <th class="text-end">After</th>
                                                            <th>Status</th>
                                                            <th>Teller</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($transactions as $t)
                                                            <tr>
                                                                <td class="fw-semibold">{{ $t->reference_number }}
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge {{ $t->transaction_type === 'deposit' ? 'status-approved' : ($t->transaction_type === 'withdrawal' ? 'status-rejected' : 'status-pending') }}">
                                                                        {{ ucfirst($t->transaction_type) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-end">NPR
                                                                    {{ number_format($t->amount, 2) }}</td>
                                                                <td class="text-end">NPR
                                                                    {{ number_format($t->balance_before, 2) }}</td>
                                                                <td class="text-end">NPR
                                                                    {{ number_format($t->balance_after, 2) }}</td>
                                                                <td>
                                                                    <span
                                                                        class="badge {{ $t->status === 'approved' ? 'status-approved' : ($t->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                                                        {{ ucfirst($t->status) }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $t->createdBy->name ?? 'N/A' }}</td>
                                                                <td>{{ optional($t->created_at)->format('Y-m-d H:i') }}
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8"
                                                                    class="text-center text-muted py-4">No
                                                                    transactions
                                                                    for this customer yet.</td>
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


</body>

</html>
