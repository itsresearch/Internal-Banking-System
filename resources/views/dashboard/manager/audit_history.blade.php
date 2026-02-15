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
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div>
                                                <h5 class="mb-0">Audit Trail</h5>
                                                <p class="text-muted mb-0">Track changes across core records.</p>
                                            </div>
                                            <form method="GET" class="d-flex gap-2 flex-wrap">
                                                <select name="user_id" class="form-select form-select-sm"
                                                    style="min-width: 180px;">
                                                    <option value="">All users</option>
                                                    @foreach ($users as $member)
                                                        <option value="{{ $member->id }}"
                                                            @if ($userId == $member->id) selected @endif>
                                                            {{ $member->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <select name="event" class="form-select form-select-sm"
                                                    style="min-width: 160px;">
                                                    <option value="">All events</option>
                                                    @foreach (['created', 'updated', 'deleted', 'restored'] as $eventOption)
                                                        <option value="{{ $eventOption }}"
                                                            @if ($event === $eventOption) selected @endif>
                                                            {{ ucfirst($eventOption) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <select name="model" class="form-select form-select-sm"
                                                    style="min-width: 200px;">
                                                    <option value="">All models</option>
                                                    @foreach ($modelOptions as $modelOption)
                                                        <option value="{{ $modelOption }}"
                                                            @if ($model === $modelOption) selected @endif>
                                                            {{ class_basename($modelOption) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="date" name="from"
                                                    class="form-control form-control-sm" value="{{ $from }}">
                                                <input type="date" name="to"
                                                    class="form-control form-control-sm" value="{{ $to }}">
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-primary">Filter</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>User</th>
                                                        <th>Event</th>
                                                        <th>Model</th>
                                                        <th>Record</th>
                                                        <th>IP</th>
                                                        <th>Old</th>
                                                        <th>New</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($audits as $audit)
                                                        @php
                                                            $oldValues = $audit->old_values
                                                                ? json_encode($audit->old_values)
                                                                : '';
                                                            $newValues = $audit->new_values
                                                                ? json_encode($audit->new_values)
                                                                : '';
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $audit->created_at?->format('M d, Y H:i') }}</td>
                                                            <td>{{ $audit->user?->name ?? 'System' }}</td>
                                                            <td class="text-capitalize">{{ $audit->event }}</td>
                                                            <td>{{ class_basename($audit->auditable_type) }}</td>
                                                            <td>#{{ $audit->auditable_id }}</td>
                                                            <td>{{ $audit->ip_address ?? '-' }}</td>
                                                            <td class="text-muted">
                                                                {{ \Illuminate\Support\Str::limit($oldValues, 80) }}
                                                            </td>
                                                            <td class="text-muted">
                                                                {{ \Illuminate\Support\Str::limit($newValues, 80) }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center text-muted">No audit
                                                                entries found for the selected filters.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-3">
                                            {{ $audits->links() }}
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
