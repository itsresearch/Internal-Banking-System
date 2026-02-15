@include('dashboard.manager.css')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('dashboard.manager.sidebar')

            <div class="layout-page">
                @include('dashboard.manager.header')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <div class="card shadow-soft">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            @if ($photo)
                                                <img src="{{ asset('storage/' . $photo->file_path) }}"
                                                    class="rounded-circle"
                                                    style="width: 96px; height: 96px; object-fit: cover;"
                                                    alt="Customer photo">
                                            @else
                                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                                                    style="width: 96px; height: 96px;">
                                                    <i class="bx bx-user text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <h5 class="text-center mb-1">{{ $customer->first_name }}
                                            {{ $customer->last_name }}</h5>
                                        <p class="text-center text-muted mb-3">{{ $customer->account_number }}</p>

                                        <div class="mb-3">
                                            <div class="text-muted">Status</div>
                                            <div class="text-capitalize fw-semibold">{{ $customer->status }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-muted">Balance</div>
                                            <div class="fw-semibold">NPR
                                                {{ number_format($customer->balance, 2) }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-muted">Flags</div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if ($customer->is_frozen)
                                                    <span class="badge bg-warning">Frozen</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            @if ($customer->deleted_at)
                                                <div class="border border-danger rounded p-2 mb-1">
                                                    <div class="small text-danger fw-semibold mb-1">Danger zone</div>
                                                    <p class="small text-muted mb-2">
                                                        This customer has already been soft-deleted. Hard delete will
                                                        permanently remove the record and all related data.
                                                    </p>
                                                    <form method="POST"
                                                        action="{{ route('manager.customers.force-delete', $customer->id) }}"
                                                        onsubmit="return confirm('Permanently and irreversibly delete this customer and all related data?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm">Hard delete
                                                            customer</button>
                                                    </form>
                                                </div>
                                            @endif
                                            @if ($customer->is_frozen)
                                                <form method="POST"
                                                    action="{{ route('manager.customers.unfreeze', $customer->id) }}">
                                                    @csrf
                                                    <button class="btn btn-outline-success">Unfreeze account</button>
                                                </form>
                                            @else
                                                <form method="POST"
                                                    action="{{ route('manager.customers.freeze', $customer->id) }}">
                                                    @csrf
                                                    <input type="text" name="reason" class="form-control mb-2"
                                                        placeholder="Freeze reason" required>
                                                    <button class="btn btn-outline-warning">Freeze account</button>
                                                </form>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card shadow-soft mb-3">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0">Profile details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2"><strong>Email:</strong> {{ $customer->email }}
                                            </div>
                                            <div class="col-md-6 mb-2"><strong>Phone:</strong> {{ $customer->phone }}
                                            </div>
                                            <div class="col-md-6 mb-2"><strong>Account type:</strong>
                                                {{ $customer->account_type }}</div>
                                            @if ($customer->account_holder_type === 'business')
                                                <div class="col-md-6 mb-2"><strong>Business:</strong>
                                                    {{ $customer->businessAccount?->business_name ?? '—' }}</div>
                                            @endif
                                            <div class="col-md-6 mb-2"><strong>Opened:</strong>
                                                {{ optional($customer->account_opened_at)->format('M d, Y') }}</div>
                                            <div class="col-md-6 mb-2"><strong>Permanent address:</strong>
                                                {{ $customer->permanent_address }}</div>
                                            <div class="col-md-6 mb-2"><strong>Temporary address:</strong>
                                                {{ $customer->temporary_address }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-soft mb-3">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0">Documents</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="border rounded p-2">
                                                    <div class="text-muted mb-2">Citizenship front</div>
                                                    @if ($citizenshipFront)
                                                        <img src="{{ asset('storage/' . $citizenshipFront->file_path) }}"
                                                            class="img-fluid rounded" alt="Citizenship front">
                                                    @else
                                                        <div class="text-muted">Not uploaded</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="border rounded p-2">
                                                    <div class="text-muted mb-2">Citizenship back</div>
                                                    @if ($citizenshipBack)
                                                        <img src="{{ asset('storage/' . $citizenshipBack->file_path) }}"
                                                            class="img-fluid rounded" alt="Citizenship back">
                                                    @else
                                                        <div class="text-muted">Not uploaded</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-soft">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0">Recent transactions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($transactions as $tx)
                                                        <tr>
                                                            <td>{{ $tx->created_at?->format('M d, Y') }}</td>
                                                            <td class="text-capitalize">{{ $tx->transaction_type }}
                                                            </td>
                                                            <td>NPR {{ number_format($tx->amount, 2) }}</td>
                                                            <td class="text-capitalize">{{ $tx->status }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">No recent
                                                                transactions.</td>
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
