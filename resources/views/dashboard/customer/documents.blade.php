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
                                        <h5 class="mb-0">Upload Customer Document</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.95rem;">Attach verified ID or photo for KYC.</p>
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

                                        <form id="documentForm" method="POST" action="{{ route('customers.documents.store', $customer->id) }}"
                                            enctype="multipart/form-data">
                                            @csrf

                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <label class="form-label">Customer (default)</label>
                                                    <input type="text" class="form-control" value="{{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->account_number }})" readonly>
                                                    <div class="helper-text mt-1">This document set will be saved against this customer.</div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Citizenship Number</label>
                                                    <input type="text" name="citizenship_number" class="form-control"
                                                        placeholder="Enter citizenship number" required>
                                                    <div class="helper-text mt-1">Citizenship and customer photo are mandatory.</div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Citizenship (Front)</label>
                                                    <input type="file" name="citizenship_front" class="form-control" accept="image/*" required>
                                                    <div class="helper-text mt-1">JPG/PNG, max 2MB.</div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Citizenship (Back)</label>
                                                    <input type="file" name="citizenship_back" class="form-control" accept="image/*" required>
                                                    <div class="helper-text mt-1">JPG/PNG, max 2MB.</div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Customer Photo</label>
                                                    <input type="file" name="customer_photo" class="form-control" accept="image/*" required>
                                                    <div class="helper-text mt-1">JPG/PNG, max 2MB.</div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-4">
                                                <button type="reset" class="btn btn-outline-secondary">Clear</button>
                                                <button type="submit" class="btn btn-secondary" data-disable-on-submit>
                                                    Upload Document
                                                </button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('documentForm');
            if (!form) return;
            form.addEventListener('submit', function () {
                const submitBtn = form.querySelector('[data-disable-on-submit]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Uploading...';
                }
            });
        });
    </script>
</body>

</html>
