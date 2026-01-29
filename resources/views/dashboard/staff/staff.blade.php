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
                                    <form method="POST" action="{{ route('customers.store') }}">
                                        @csrf


                                        <div class="card shadow-sm">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="mb-0">Customer Information</h5>
                                            </div>

                                            <div class="card-body">
                                                <!-- Customer Code -->
                                                <div class="mb-3">
                                                    <label class="form-label">Customer Code</label>
                                                    <input type="text" name="customer_code" class="form-control"
                                                        placeholder="CUST-001" required>
                                                </div>

                                                <!-- Names -->
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" name="first_name" class="form-control"
                                                            required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Middle Name</label>
                                                        <input type="text" name="middle_name" class="form-control">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" name="last_name" class="form-control"
                                                            required>
                                                    </div>
                                                </div>

                                                <!-- Parents -->
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Father's Name</label>
                                                        <input type="text" name="fathers_name" class="form-control">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Mother's Name</label>
                                                        <input type="text" name="mothers_name" class="form-control">
                                                    </div>
                                                </div>

                                                <!-- DOB & Gender -->
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Date of Birth</label>
                                                        <input type="date" name="date_of_birth" class="form-control">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Gender</label>
                                                        <select name="gender" class="form-select">
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
                                                        <input type="text" name="phone" class="form-control">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control">
                                                    </div>
                                                </div>

                                                <!-- Address -->
                                                <div class="mb-3">
                                                    <label class="form-label">Permanent Address</label>
                                                    <input type="text" name="permanent_address" class="form-control"
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Temporary Address</label>
                                                    <input type="text" name="temporary_address" class="form-control">
                                                </div>

                                                <!-- Status -->
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" selected>Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
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

    <!-- Page JS -->
</body>

</html>
