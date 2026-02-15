@include('dashboard.staff.css')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('dashboard.staff.sidebar')

            <div class="layout-page">
                @include('dashboard.staff.header')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-soft">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="mb-0">Deleted Customers</h5>
                                            <div class="text-muted" style="font-size: 0.95rem;">
                                                Soft-deleted customers waiting for manager removal.
                                            </div>
                                        </div>
                                        <a href="{{ route('customers.customersList') }}"
                                            class="btn btn-outline-primary">
                                            Back to customers
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if (session('success'))
                                            <div class="alert alert-success mb-4">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        <div class="table-responsive text-nowrap">
                                            <table class="table table-hover align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Customer ID</th>
                                                        <th>Name</th>
                                                        <th>Account #</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Deleted At</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    @forelse ($customers as $customer)
                                                        <tr>
                                                            <td>{{ $customer->id }}</td>
                                                            <td>{{ $customer->first_name }} {{ $customer->last_name }}
                                                            </td>
                                                            <td>{{ $customer->account_number ?? '—' }}</td>
                                                            <td>{{ $customer->email ?? '—' }}</td>
                                                            <td>{{ $customer->phone ?? '—' }}</td>
                                                            <td>{{ optional($customer->deleted_at)->format('M d, Y H:i') }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">No deleted
                                                                customers.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-3">
                                            {{ $customers->links() }}
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
                                        </script>
                                        Research Bank of Nepal, All rights reserved
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
