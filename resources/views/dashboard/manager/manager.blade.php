@include('dashboard.manager.css')

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            @include('dashboard.manager.sidebar')
            <!-- / Sidebar -->

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('dashboard.manager.header')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary mb-3">👔 Welcome to Manager Dashboard!</h5>
                                        <p class="mb-4">
                                            You are logged in as a <strong>Manager</strong>. This dashboard provides you
                                            with access to manage operations, monitor activities, and generate reports.
                                        </p>
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <strong>Info:</strong> As a manager, you have elevated permissions to
                                            oversee team activities and approve requests.
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>

                                        <h6 class="mt-5 mb-3">Manager Functions:</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item">📊 View analytics and reports</li>
                                            <li class="list-group-item">👥 Manage team members</li>
                                            <li class="list-group-item">✅ Approve pending requests</li>
                                            <li class="list-group-item">📈 Monitor performance metrics</li>
                                            <li class="list-group-item">💼 Manage department operations</li>
                                        </ul>
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

    <!-- Page JS -->
</body>

</html>
