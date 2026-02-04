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
                                    <h5 class="card-header">All Customers</h5>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table">
                                            <thead>
                                              
                                                <tr>
                                                    <th>Customer ID</th>
                                                    <th>First Name</th>
                                                    <th>Last Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Temporary Address</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                @foreach ($customers as $customer)
                                                    <tr onclick="window.location='{{ route('customers.customerDetails', ['id' => $customer->id]) }}'" style="cursor:pointer;">
                                                        <td>{{ $customer->id }}</td>
                                                        <td>{{ $customer->first_name }}</td>
                                                        <td>{{ $customer->last_name }}</td>
                                                        <td>{{ $customer->email }}</td>
                                                        <td>{{ $customer->phone }}</td>
                                                        <td>{{ $customer->temporary_address }}</td>
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

    <script>
        (function() {
            const holderSelect = document.getElementById('accountHolderType');
            const businessFields = document.getElementById('businessFields');
            const accountTypeSelect = document.getElementById('accountType');
            const savingsFields = document.getElementById('savingsFields');
            const currentFields = document.getElementById('currentFields');
            const overdraftEnabled = document.getElementById('overdraftEnabled');
            const overdraftLimitField = document.getElementById('overdraftLimitField');
            const individualExtras = document.getElementById('individualExtras');

            function toggleBusinessFields() {
                const isBusiness = holderSelect.value === 'business';
                businessFields.style.display = isBusiness ? 'flex' : 'none';
                individualExtras.style.display = isBusiness ? 'none' : 'flex';
            }

            function toggleAccountTypeFields() {
                const isSavings = accountTypeSelect.value === 'savings';
                savingsFields.style.display = isSavings ? 'flex' : 'none';
                currentFields.style.display = isSavings ? 'none' : 'flex';
            }

            function toggleOverdraftLimit() {
                overdraftLimitField.style.display = overdraftEnabled.value === '1' ? 'block' : 'none';
            }

            holderSelect?.addEventListener('change', toggleBusinessFields);
            accountTypeSelect?.addEventListener('change', toggleAccountTypeFields);
            overdraftEnabled?.addEventListener('change', toggleOverdraftLimit);

            toggleBusinessFields();
            toggleAccountTypeFields();
            toggleOverdraftLimit();
        })();
    </script>
</body>

</html>
