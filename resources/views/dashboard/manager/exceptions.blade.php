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
                            <div class="col-12">
                                <div class="card shadow-soft">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">Reverse a Transaction</h5>
                                        <p class="text-muted mb-0">Create a reversal entry for an approved transaction.
                                        </p>
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
                                                        <th>Reference</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th class="text-end">Reverse</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($recentApproved as $tx)
                                                        <tr>
                                                            <td>{{ $tx->reference_number }}</td>
                                                            <td class="text-capitalize">{{ $tx->transaction_type }}</td>
                                                            <td>NPR {{ number_format($tx->amount, 2) }}</td>
                                                            <td class="text-end">
                                                                <form method="POST"
                                                                    action="{{ route('manager.exceptions.reverse', $tx->id) }}"
                                                                    class="d-flex gap-2">
                                                                    @csrf
                                                                    <input type="text" name="reason"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Reason" required>
                                                                    <button
                                                                        class="btn btn-sm btn-outline-warning">Reverse</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">No
                                                                approved transactions available.</td>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('adjustSearch');
                    const results = document.getElementById('adjustSearchResults');
                    const customerIdInput = document.getElementById('adjustCustomerId');

                    let debounceTimer = null;
                    let lastQuery = '';

                    <
                    /html>
