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
                                        <h5 class="mb-0">Business Account Approvals</h5>
                                        <p class="text-muted mb-0">Review new business account openings and record
                                            decisions.</p>
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

                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Account</th>
                                                        <th>Type</th>
                                                        <th>Requested</th>
                                                        <th class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($accounts as $account)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">{{ $account->first_name }}
                                                                    {{ $account->last_name }}</div>
                                                                <div class="text-muted" style="font-size:0.9rem;">
                                                                    {{ $account->phone }}</div>
                                                            </td>
                                                            <td>
                                                                <div>{{ $account->account_number }}</div>
                                                                <div class="text-muted" style="font-size:0.9rem;">
                                                                    {{ $account->customer_code }}</div>
                                                            </td>
                                                            <td class="text-capitalize">{{ $account->account_type }}
                                                            </td>
                                                            <td>{{ optional($account->created_at)->format('M d, Y') }}
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                                    <a href="{{ route('manager.customers.show', $account->id) }}"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        View details
                                                                    </a>
                                                                    <form method="POST"
                                                                        action="{{ route('manager.approvals.accounts.approve', $account->id) }}">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-success">Approve</button>
                                                                    </form>
                                                                    <form method="POST"
                                                                        action="{{ route('manager.approvals.accounts.reject', $account->id) }}"
                                                                        class="d-flex gap-2">
                                                                        @csrf
                                                                        <input type="text" name="reason"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="Rejection reason" required>
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-danger">Reject</button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">No pending
                                                                accounts right now.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-3">
                                            {{ $accounts->links() }}
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
