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
                                            <h5 class="mb-0">All Customers</h5>
                                            <div class="text-muted" style="font-size: 0.9rem;">
                                                Use delete to move a customer into the deleted list (soft delete only).
                                            </div>
                                        </div>
                                        <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                            Add Customer
                                        </a>
                                    </div>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Customer ID</th>
                                                    <th>First Name</th>
                                                    <th>Last Name</th>
                                                    <th>Account #</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Temporary Address</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                @foreach ($customers as $customer)
                                                    <tr>
                                                        <td>{{ $customer->id }}</td>
                                                        <td>{{ $customer->first_name }}</td>
                                                        <td>{{ $customer->last_name }}</td>
                                                        <td>{{ $customer->account_number ?? '—' }}</td>
                                                        <td>{{ $customer->email }}</td>
                                                        <td>{{ $customer->phone }}</td>
                                                        <td>{{ $customer->temporary_address }}</td>
                                                        <td class="text-end">
                                                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                                <a href="{{ route('customers.customerDetails', ['id' => $customer->id]) }}"
                                                                    class="btn btn-sm btn-outline-primary">View</a>
                                                                <form method="POST"
                                                                    action="{{ route('customers.destroy', $customer->id) }}"
                                                                    onsubmit="return confirm('Move this customer to the deleted list? They can still be managed by the manager.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-outline-danger">
                                                                        Move to deleted
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>


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
