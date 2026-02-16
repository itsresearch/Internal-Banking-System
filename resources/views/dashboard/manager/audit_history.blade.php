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
                                            <table class="table align-middle table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>User</th>
                                                        <th>Event</th>
                                                        <th>Model</th>
                                                        <th>Record</th>
                                                        <th>Changes</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($audits as $audit)
                                                        @php
                                                            $oldValues = $audit->old_values ?? [];
                                                            $newValues = $audit->new_values ?? [];
                                                            $changedKeys = array_values(
                                                                array_unique(
                                                                    array_merge(
                                                                        array_keys($oldValues),
                                                                        array_keys($newValues),
                                                                    ),
                                                                ),
                                                            );
                                                            $changedPreview = implode(
                                                                ', ',
                                                                array_slice($changedKeys, 0, 4),
                                                            );
                                                            $changedCount = count($changedKeys);
                                                            $oldJson = $oldValues
                                                                ? json_encode(
                                                                    $oldValues,
                                                                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
                                                                )
                                                                : 'N/A';
                                                            $newJson = $newValues
                                                                ? json_encode(
                                                                    $newValues,
                                                                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
                                                                )
                                                                : 'N/A';
                                                            $eventClass = match ($audit->event) {
                                                                'created' => 'status-approved',
                                                                'updated' => 'status-pending',
                                                                'deleted' => 'status-rejected',
                                                                'restored' => 'status-approved',
                                                                default => 'status-pending',
                                                            };
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $audit->created_at?->format('M d, Y H:i') }}</td>
                                                            <td>{{ $audit->user?->name ?? 'System' }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge {{ $eventClass }} text-capitalize">
                                                                    {{ $audit->event }}
                                                                </span>
                                                            </td>
                                                            <td>{{ class_basename($audit->auditable_type) }}</td>
                                                            <td>#{{ $audit->auditable_id }}</td>
                                                            <td class="text-muted">
                                                                @if ($changedCount === 0)
                                                                    No field changes
                                                                @else
                                                                    {{ $changedPreview }}
                                                                    @if ($changedCount > 4)
                                                                        <span
                                                                            class="text-muted">+{{ $changedCount - 4 }}
                                                                            more</span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-secondary"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#audit-{{ $audit->id }}"
                                                                    aria-expanded="false">
                                                                    View
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr class="collapse bg-light" id="audit-{{ $audit->id }}">
                                                            <td colspan="7">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <div class="fw-semibold mb-1">Old values</div>
                                                                        <pre class="mb-0 small">{{ $oldJson }}</pre>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="fw-semibold mb-1">New values</div>
                                                                        <pre class="mb-0 small">{{ $newJson }}</pre>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center text-muted">No audit
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
