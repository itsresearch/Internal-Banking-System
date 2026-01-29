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
                                    <form method="POST" action="{{ route('customers.documents.store') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="card shadow-sm">
                                            <div class="card-header bg-secondary text-white">
                                                <h5 class="mb-0">Upload Customer Document</h5>
                                            </div>

                                            <div class="card-body">

                                                <!-- Customer -->
                                            
                                                <div class="mb-3">
                                                    <label class="form-label">Customer</label>
                                                    <input type="text" name="customer_id" class="form-control"
                                                        placeholder="" value="{{ $customer->first_name}}" required>
                                                </div>

                                                <!-- Document Type -->
                                                <div class="mb-3">
                                                    <label class="form-label">Document Type</label>
                                                    <select name="document_type" class="form-select" required>
                                                        <option value="">-- Select Document Type --</option>
                                                        <option value="citizenship">Citizenship</option>
                                                        <option value="passport">Passport</option>
                                                        <option value="photo">Photo</option>
                                                    </select>
                                                </div>

                                                <!-- Document Number -->
                                                <div class="mb-3">
                                                    <label class="form-label">Document Number</label>
                                                    <input type="text" name="document_number" class="form-control"
                                                        placeholder="Enter document number" required>
                                                </div>

                                                <!-- File -->
                                                <div class="mb-3">
                                                    <label class="form-label">Upload File</label>
                                                    <input type="file" name="file" class="form-control" required>
                                                    <small class="text-muted">
                                                        Allowed: PDF / JPG / PNG
                                                    </small>
                                                </div>

                                            </div>

                                            <div class="card-footer text-end">
                                                <button type="submit" class="btn btn-secondary">
                                                    Upload Document
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
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
</body>

</html>
