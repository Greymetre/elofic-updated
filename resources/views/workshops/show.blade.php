<x-app-layout>
       <style>
       .timeline-container {
    padding: 24px 24px 8px 24px;
}

.timeline-entry {
    display: flex;
    gap: 20px;
    position: relative;
}

.timeline-entry.first {
    margin-top: 0;
}

.timeline-entry.last .timeline-line {
    display: none;
}

.timeline-rail {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
    width: 32px;
}

.timeline-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 1;
    position: relative;
}

.timeline-line {
    width: 2px;
    flex: 1;
    min-height: 20px;
    background: linear-gradient(to bottom, #e2e8f0, #e2e8f0);
    margin-top: 4px;
}

.timeline-content {
    flex: 1;
    padding-bottom: 24px;
    min-width: 0;
}

/* ==================== ACTIVITY CARD ==================== */
.activity-card {
    background: #fff;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.2s ease;
}

.activity-card:hover {
    border-color: #e2e8f0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

/* ==================== BADGES & TAGS ==================== */
.action-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.module-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 6px;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.activity-meta {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #94a3b8;
    white-space: nowrap;
}

/* ==================== DIFF TABLE (for updates) ==================== */
.diff-table-wrapper {
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    overflow: hidden;
}

.diff-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.diff-table thead tr {
    background: #f8fafc;
}

.diff-table th {
    padding: 8px 14px;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    border-bottom: 1px solid #f1f5f9;
    text-align: left;
}

.diff-table td {
    padding: 10px 14px;
    border-bottom: 1px solid #f8fafc;
    vertical-align: top;
    word-break: break-word;
}

.diff-table tbody tr:last-child td {
    border-bottom: none;
}

.diff-table tbody tr:hover {
    background: #fafbfc;
}

.field-name {
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
    width: 140px;
    font-size: 12px;
}

.old-col {
    color: #dc2626;
    width: 40%;
}

.new-col {
    color: #16a34a;
    width: 40%;
}

.diff-old {
    display: inline-block;
    background: #fef2f2;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 12px;
    text-decoration: line-through;
    text-decoration-color: #fca5a5;
}

.diff-new {
    display: inline-block;
    background: #f0fdf4;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 12px;
}

.diff-removed {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fef2f2;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    color: #dc2626;
}

/* ==================== FIELDS GRID (for created/deleted) ==================== */
.fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 8px;
}

.field-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 8px 12px;
    background: #f8fafc;
    border-radius: 6px;
    border: 1px solid #f1f5f9;
}

.field-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #94a3b8;
}

.field-value {
    font-size: 13px;
    color: #1e293b;
    font-weight: 500;
    word-break: break-word;
}

/* ==================== FOOTER ==================== */
.activity-footer {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #f8fafc;
}

.performed-by {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #94a3b8;
}

.avatar-xs {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
}

/* ==================== EMPTY STATE ==================== */
.empty-state {
    padding: 60px 20px;
    text-align: center;
}

.empty-icon {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 12px;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .timeline-container {
        padding: 16px 12px 4px 12px;
    }

    .timeline-rail {
        width: 28px;
    }

    .timeline-dot {
        width: 28px;
        height: 28px;
    }

    .timeline-dot i {
        font-size: 12px !important;
    }

    .activity-card {
        padding: 14px;
    }

    .fields-grid {
        grid-template-columns: 1fr;
    }

    .diff-table-wrapper {
        overflow-x: auto;
    }

    .diff-table {
        min-width: 440px;
    }

    .field-name {
        width: 110px;
    }
}

@media (max-width: 480px) {
    .activity-card .d-flex:first-child {
        flex-direction: column;
        align-items: flex-start !important;
    }
}
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon card-header-theme">
                    <div class="card-icon">
                        <i class="material-icons">engineering</i>
                    </div>
                    <h4 class="card-title">
                        View Workshop Details
                        <span class="pull-right">
                            <a href="{{ route('workshops.index') }}" class="btn btn-theme">
                                <i class="material-icons">arrow_back</i> Back to List
                            </a>
                        </span>
                    </h4>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card p-3 shadow-sm" style="background:#fff; color:#000;">
                                <h6 class="text-muted">Total Order Value</h6>
                                <h4 class="fw-bold text-dark">₹{{ number_format($totalOrderValue, 2) }}</h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card p-3 shadow-sm" style="background:#fff; color:#000;">
                                <h6 class="text-muted">Total Quantity</h6>
                                <h4 class="fw-bold text-dark">{{ $totalOrderQty }}</h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card p-3 shadow-sm" style="background:#fff; color:#000;">
                                <h6 class="text-muted">Last Order Date</h6>
                                <h4 class="fw-bold text-dark">{{ $lastOrderDate ?? '-' }}</h4>
                            </div>
                        </div>
                    </div>
                    <!-- Workshop Photos -->
                    <h5 class="mt-3 mb-4 text-theme font-weight-bold">Workshop Photos</h5>
                    <div class="row mb-5">
         <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark">Shop Photo</h6>
                </div>
                <div class="card-body p-3">
                    @if($customer->shop_photo)
                        <img src="{{ asset('storage/'.$customer->shop_photo) }}"
                             class="img-fluid rounded shadow-sm workshop-image-popup cursor-pointer"
                             style="max-height: 250px; object-fit: cover;"
                             alt="Shop Photo">
                    @else
                        <img src="{{ asset('assets/img/placeholder.jpg') }}"
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 250px; object-fit: cover;"
                             alt="No Shop Photo">
                        <p class="text-muted mt-3 mb-0"><em>No photo uploaded</em></p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark">Owner / ID Photo</h6>
                </div>
                <div class="card-body p-3">
                    @if($customer->owner_photo)
                        <img src="{{ asset('storage/'.$customer->owner_photo) }}"
                             class="img-fluid rounded shadow-sm workshop-image-popup cursor-pointer"
                             style="max-height: 250px; object-fit: cover;"
                             alt="Owner Photo">
                    @else
                        <img src="{{ asset('assets/img/placeholder.jpg') }}"
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 250px; object-fit: cover;"
                             alt="No Owner Photo">
                        <p class="text-muted mt-3 mb-0"><em>No photo uploaded</em></p>
                    @endif
                </div>
            </div>
        </div>
                    </div>

                    <!-- Workshop Name -->
                    <div class="row mb-4">
                        <div class="col">
                            <h4 class="mb-0">{{ $customer->shop_name }}</h4>
                            <p class="text-muted mb-0">Workshop</p>
                        </div>
                    </div>

                    <!-- Details (2 Columns) -->
                    <div class="row mt-3">
                        <!-- Basic Information (including statuses) -->
                        <div class="col-md-6">
                            <div class="card card-plain h-100">
                                <div class="card-body p-3">
                                    <div class="ctmr-box">
                                        <h6 class="">Basic Information</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">Type:</strong> &nbsp; WORKSHOP
                                            </li>
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">Sub Type:</strong> &nbsp; {{ $customer->sub_type ?? '-' }}
                                            </li>
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">Owner Name:</strong> &nbsp; {{ $customer->owner_name }}
                                            </li>
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">Mobile Number:</strong> &nbsp; {{ $customer->mobile_number }}
                                            </li>
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">WhatsApp / Alternate:</strong> &nbsp; {{ $customer->whatsapp_number ?? '-' }}
                                            </li>
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">Vehicle Segment:</strong> &nbsp; {{ is_array($customer->vehicle_segment) ? implode(', ', $customer->vehicle_segment) : ($customer->vehicle_segment ?? '-') }}
                                            </li>
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">Distributor Name:</strong> &nbsp; 
                                                @php
    $dist = \App\Models\MasterDistributor::find($customer->distributor_name);
@endphp
@if($dist)
    {{ $dist->distributor_code ?? '-' }} - {{ $dist->legal_name ?? 'N/A' }}
@else
    Not Assigned
@endif
                                            </li>
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">Created At:</strong> &nbsp; {{ showdatetimeformat($customer->created_at) }}
                                            </li>

                                            <!-- Opportunity Status -->
                                            <li class="list-group-item border-0 ps-0 pt-3 text-sm">
                                                <strong class="text-dark">
                                                    <i class="material-icons" style="font-size:18px;vertical-align:middle;">local_fire_department</i>
                                                    Opportunity Status:
                                                </strong>
                                                &nbsp;
                                                <span class="badge badge-pill {{
                                                    $customer->opportunity_status == 'HOT' ? 'badge-danger' :
                                                    ($customer->opportunity_status == 'WARM' ? 'badge-warning' :
                                                    ($customer->opportunity_status == 'COLD' ? 'badge-info' : 'badge-secondary'))
                                                }}">
                                                    {{ $customer->opportunity_status ?? 'N/A' }}
                                                </span>
                                            </li>

                                            <!-- Nistha Awareness Status -->
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                <strong class="text-dark">
                                                    <i class="material-icons" style="font-size:18px;vertical-align:middle;">school</i>
                                                    Nistha Awareness Status:
                                                </strong>
                                                &nbsp;
                                                <span class="badge badge-pill {{ $customer->nistha_awareness_status == 'Done' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $customer->nistha_awareness_status == 'Done' ? 'DONE' : 'NOT DONE' }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="col-md-6">
                            <div class="card card-plain h-100">
                                <div class="card-body p-3">
                                    <div class="ctmr-box">
                                        <h6 class="">Address Information</h6>
                                        @php
                                            $state = \App\Models\State::find($customer->state_id);
                                            $city = \App\Models\City::find($customer->city_id);
                                        @endphp
                                        <ul class="list-group">
                                            @if($customer->address_line)
                                                <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                    <strong class="text-dark">Address:</strong> &nbsp; {{ $customer->address_line }}
                                                </li>
                                            @endif
                                            @if($state?->state_name)
                                                <li class="list-group-item border-0 ps-0 text-sm">
                                                    <strong class="text-dark">State:</strong> &nbsp; {{ $state->state_name }}
                                                </li>
                                            @endif
                                            @if($city?->city_name)
                                                <li class="list-group-item border-0 ps-0 text-sm">
                                                    <strong class="text-dark">City:</strong> &nbsp; {{ $city->city_name }}
                                                </li>
                                            @endif
                                            @if($customer->belt_area_market_name)
                                                <li class="list-group-item border-0 ps-0 text-sm">
                                                    <strong class="text-dark">Belt / Area / Market:</strong> &nbsp; {{ $customer->belt_area_market_name }}
                                                </li>
                                            @endif
                                            @if($customer->gps_location)
                                                <li class="list-group-item border-0 ps-0 text-sm">
                                                    <strong class="text-dark">GPS Coordinates:</strong> &nbsp; 
                                                    <code class="text-success">{{ $customer->gps_location }}</code><br>
                                                    <small>
                                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $customer->gps_location }}"
                                                           target="_blank"
                                                           class="text-decoration-none text-muted">
                                                            <i class="material-icons" style="font-size:14px;vertical-align:middle;">open_in_new</i> Open in Google Maps
                                                        </a>
                                                    </small>
                                                </li>
                                            @endif
                                        </ul>

                                        @if($customer->gps_location)
                                            <div class="mt-4 text-center">
                                                <button type="button"
                                                        class="btn btn-theme btn-round shadow-sm"
                                                        onclick="showWorkshopOnMap()">
                                                    <i class="material-icons">location_on</i>
                                                    View Location on Map
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="showCustomerLocationonMaps" style="display:none;">
                                <div id="workshopMap" style="height: 600px; width: 100%; border-radius: 0.5rem;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Activity Log Panel -->
                    <div class="row mt-5">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="material-icons">history</i>
                    Activity Logs
                </h5>
                @if($activities->count())
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                        {{ $activities->count() }} record{{ $activities->count() > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>
            <div class="card-body p-0">
                @if($activities->count())
                    <div class="timeline-container">
                        @foreach($activities as $index => $activity)
                            @php
                                $isNewest = $index === 0;
                                $isOldest = $index === $activities->count() - 1;

                                // Determine action styling
                                $actionColors = [
                                    'CREATED' => ['bg' => '#10b981', 'icon' => 'add_circle', 'label' => 'Created'],
                                    'UPDATED' => ['bg' => '#3b82f6', 'icon' => 'edit', 'label' => 'Updated'],
                                    'DELETED' => ['bg' => '#ef4444', 'icon' => 'delete', 'label' => 'Deleted'],
                                    'RESTORED' => ['bg' => '#8b5cf6', 'icon' => 'restore', 'label' => 'Restored'],
                                ];
                                $actionStyle = $actionColors[strtoupper($activity->action)] ?? $actionColors['UPDATED'];

                                // Get changed fields for updates
                                $changedFields = [];
                                if (strtoupper($activity->action) === 'UPDATED' && $activity->old_values && $activity->new_values) {
                                    foreach ($activity->new_values as $key => $newValue) {
                                        $oldValue = $activity->old_values[$key] ?? null;
                                        $changedFields[$key] = [
                                            'old' => $oldValue,
                                            'new' => $newValue,
                                            'changed' => $oldValue != $newValue
                                        ];
                                    }
                                    // Check for removed keys
                                    foreach ($activity->old_values as $key => $oldValue) {
                                        if (!array_key_exists($key, $activity->new_values)) {
                                            $changedFields[$key] = [
                                                'old' => $oldValue,
                                                'new' => null,
                                                'changed' => true,
                                                'removed' => true
                                            ];
                                        }
                                    }
                                }
                            @endphp

                            <div class="timeline-entry {{ $isNewest ? 'first' : '' }} {{ $isOldest ? 'last' : '' }}">
                                <!-- Timeline Dot & Line -->
                                <div class="timeline-rail">
                                    <div class="timeline-dot" style="background: {{ $actionStyle['bg'] }};">
                                        <i class="material-icons" style="font-size: 14px; color: #fff;">{{ $actionStyle['icon'] }}</i>
                                    </div>
                                    @if(!$isOldest)
                                        <div class="timeline-line"></div>
                                    @endif
                                </div>

                                <!-- Content Card -->
                                <div class="timeline-content">
                                    <div class="activity-card">
                                        <!-- Header Row -->
                                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="action-badge" style="background: {{ $actionStyle['bg'] }}15; color: {{ $actionStyle['bg'] }}; border: 1px solid {{ $actionStyle['bg'] }}30;">
                                                    {{ $actionStyle['label'] }}
                                                </span>
                                                <span class="module-tag">
                                                    <i class="material-icons" style="font-size: 14px;">widgets</i>
                                                    {{ ucfirst(str_replace('_', ' ', $activity->module)) }}
                                                </span>
                                            </div>
                                            <div class="activity-meta">
                                                <i class="material-icons" style="font-size: 14px;">schedule</i>
                                                {{ showdatetimeformat($activity->created_at) }}
                                            </div>
                                        </div>

                                        <!-- Content based on action type -->
                                        @if(strtoupper($activity->action) === 'UPDATED' && count($changedFields) > 0)
                                            <!-- Diff View for Updates -->
                                            <div class="diff-table-wrapper">
                                                <table class="diff-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Field</th>
                                                            <th class="old-col">Before</th>
                                                            <th class="new-col">After</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($changedFields as $field => $values)
                                                            @if($values['changed'])
                                                                <tr>
                                                                    <td class="field-name">
                                                                        {{ ucfirst(str_replace('_', ' ', $field)) }}
                                                                    </td>
                                                                    <td class="new-col">
                                                                        @if(isset($values['removed']))
                                                                            <span class="diff-removed">
                                                                                <i class="material-icons" style="font-size:14px;">remove_circle</i>
                                                                                Removed
                                                                            </span>
                                                                        @else
                                                                            <span class="diff-new">
                                                                                @if($values['new'] === null)
                                                                                    <em class="text-muted">null</em>
                                                                                @elseif(is_array($values['new']))
                                                                                    {{ json_encode($values['new'], JSON_PRETTY_PRINT) }}
                                                                                @else
                                                                                    {{ e($values['new']) }}
                                                                                @endif
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="new-col">
                                                                        @if(isset($values['removed']))
                                                                            <span class="diff-removed">
                                                                                <i class="material-icons" style="font-size:14px;">remove_circle</i> Removed
                                                                            </span>
                                                                        @else
                                                                            <span class="diff-new">
                                                                                @if($values['new'] === null)
                                                                                    <em class="text-muted">null</em>
                                                                                @elseif(is_array($values['new']) || is_object($values['new']))
                                                                                    {{ json_encode($values['new'], JSON_PRETTY_PRINT) }}
                                                                                @else
                                                                                    {{ e($values['new']) }}
                                                                                @endif
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <!-- Key-Value Pairs for Created/Deleted -->
                                            @php
                                                $displayValues = $activity->new_values ?? $activity->old_values ?? [];
                                                // Skip internal fields
                                                $skipFields = ['created_at', 'updated_at', 'id', 'created_by', 'employee_id'];
                                            @endphp
                                            <div class="fields-grid">
                                                @foreach($displayValues as $key => $value)
                                                    @if(!in_array($key, $skipFields))
                                                        <div class="field-item">
                                                            <span class="field-label">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                                            <span class="field-value">
                                                                @if($value === null)
                                                                    <em class="text-muted">null</em>
                                                                @elseif(is_array($value))
                                                                    {{ e(json_encode($value)) }}
                                                                @else
                                                                    {{ e($value) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Footer -->
                                        <div class="activity-footer">
                                            <div class="performed-by">
                                                <div class="avatar-xs">
                                                    {{ strtoupper(substr($activity->performed_by ?? 'S', 0, 1)) }}
                                                </div>
                                                <span>
                                                    {{ $activity->performed_by ? 'User #' . $activity->performed_by : 'System' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="material-icons empty-icon">inbox</i>
                        <p class="text-muted mb-0">No activity found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

                    <!-- Action Buttons -->
                    <div class="text-right mt-5">
                        <a href="{{ route('workshops.edit', encrypt($customer->id)) }}"
                           class="btn btn-warning btn-lg mr-3 shadow-sm">
                            <i class="material-icons">edit</i> Edit Workshop
                        </a>
                        <a href="{{ route('workshops.index') }}"
                           class="btn btn-secondary btn-lg shadow-sm">
                            <i class="material-icons">list</i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.workshop-image-popup').forEach(img => {
            img.style.cursor = 'zoom-in';

            img.addEventListener('click', function (e) {
                e.stopPropagation();
                const src = this.getAttribute('src');

                // Remove any existing popup
                if (document.getElementById('imagePopupOverlay')) {
                    document.getElementById('imagePopupOverlay').remove();
                }

                const overlay = document.createElement('div');
                overlay.id = 'imagePopupOverlay';
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100vw';
                overlay.style.height = '100vh';
                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                overlay.style.display = 'flex';
                overlay.style.alignItems = 'center';
                overlay.style.justifyContent = 'center';
                overlay.style.zIndex = '9999';
                overlay.style.padding = '40px';
                overlay.style.boxSizing = 'border-box';
                overlay.style.cursor = 'default';

                const imgContainer = document.createElement('div');
                imgContainer.style.position = 'relative';
                imgContainer.style.maxWidth = '95%';
                imgContainer.style.maxHeight = '95%';
                imgContainer.style.overflow = 'hidden'; // Prevent scrollbars

                const largeImg = document.createElement('img');
                largeImg.src = src;
                largeImg.style.maxWidth = '100%';
                largeImg.style.maxHeight = '95vh';
                largeImg.style.objectFit = 'contain';
                largeImg.style.borderRadius = '12px';
                largeImg.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.6)';
                largeImg.style.transition = 'transform 0.3s ease';
                largeImg.style.transform = 'scale(1) translate(0px, 0px)';
                largeImg.style.cursor = 'grab';
                largeImg.style.userSelect = 'none';

                // Dragging variables
                let isDragging = false;
                let startX, startY, translateX = 0, translateY = 0;
                let currentScale = 1;

                // Zoom buttons
                const zoomIn = document.createElement('div');
                zoomIn.innerHTML = '+';
                zoomIn.style.position = 'absolute';
                zoomIn.style.bottom = '20px';
                zoomIn.style.right = '70px';
                zoomIn.style.fontSize = '30px';
                zoomIn.style.color = 'white';
                zoomIn.style.background = 'rgba(0,0,0,0.7)';
                zoomIn.style.width = '50px';
                zoomIn.style.height = '50px';
                zoomIn.style.borderRadius = '50%';
                zoomIn.style.display = 'flex';
                zoomIn.style.alignItems = 'center';
                zoomIn.style.justifyContent = 'center';
                zoomIn.style.cursor = 'pointer';
                zoomIn.style.zIndex = '10';

                const zoomOut = document.createElement('div');
                zoomOut.innerHTML = '−';
                zoomOut.style.position = 'absolute';
                zoomOut.style.bottom = '20px';
                zoomOut.style.right = '10px';
                zoomOut.style.fontSize = '40px';
                zoomOut.style.color = 'white';
                zoomOut.style.background = 'rgba(0,0,0,0.7)';
                zoomOut.style.width = '50px';
                zoomOut.style.height = '50px';
                zoomOut.style.borderRadius = '50%';
                zoomOut.style.display = 'flex';
                zoomOut.style.alignItems = 'center';
                zoomOut.style.justifyContent = 'center';
                zoomOut.style.cursor = 'pointer';
                zoomOut.style.zIndex = '10';

                // Close button
                const closeBtn = document.createElement('div');
                closeBtn.innerHTML = '×';
                closeBtn.style.position = 'absolute';
                closeBtn.style.top = '20px';
                closeBtn.style.right = '20px';
                closeBtn.style.fontSize = '40px';
                closeBtn.style.color = 'white';
                closeBtn.style.background = 'rgba(0,0,0,0.7)';
                closeBtn.style.width = '50px';
                closeBtn.style.height = '50px';
                closeBtn.style.borderRadius = '50%';
                closeBtn.style.display = 'flex';
                closeBtn.style.alignItems = 'center';
                closeBtn.style.justifyContent = 'center';
                closeBtn.style.cursor = 'pointer';
                closeBtn.style.zIndex = '10';

                // Zoom functionality
                const updateTransform = () => {
                    largeImg.style.transform = `scale(${currentScale}) translate(${translateX}px, ${translateY}px)`;
                };

                zoomIn.onclick = (e) => {
                    e.stopPropagation();
                    if (currentScale < 5) {
                        currentScale += 0.25;
                        updateTransform();
                        largeImg.style.cursor = 'grab';
                    }
                };

                zoomOut.onclick = (e) => {
                    e.stopPropagation();
                    if (currentScale > 0.5) {
                        currentScale -= 0.25;
                        // Reset position if zoom out too much
                        if (currentScale <= 1) {
                            translateX = 0;
                            translateY = 0;
                        }
                        updateTransform();
                    }
                };

                // Double click to reset
                largeImg.ondblclick = (e) => {
                    e.stopPropagation();
                    currentScale = 1;
                    translateX = 0;
                    translateY = 0;
                    updateTransform();
                };

                // Mouse drag to pan (only when zoomed)
                largeImg.onmousedown = (e) => {
                    if (currentScale > 1) {
                        isDragging = true;
                        startX = e.clientX - translateX;
                        startY = e.clientY - translateY;
                        largeImg.style.cursor = 'grabbing';
                        e.preventDefault();
                    }
                };

                document.onmousemove = (e) => {
                    if (!isDragging) return;
                    translateX = e.clientX - startX;
                    translateY = e.clientY - startY;
                    updateTransform();
                };

                document.onmouseup = () => {
                    isDragging = false;
                    largeImg.style.cursor = currentScale > 1 ? 'grab' : 'default';
                };

                // Touch support for mobile (pinch zoom baad mein add kar sakte hain agar chahiye)
                // Abhi drag work karega touch devices par bhi

                // Append all
                imgContainer.appendChild(largeImg);
                imgContainer.appendChild(closeBtn);
                imgContainer.appendChild(zoomIn);
                imgContainer.appendChild(zoomOut);
                overlay.appendChild(imgContainer);
                document.body.appendChild(overlay);

                // Close popup
                const closePopup = () => {
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.remove(), 300);
                };

                overlay.onclick = (e) => {
                    if (e.target === overlay || e.target === closeBtn) {
                        closePopup();
                    }
                };

                document.addEventListener('keydown', function escHandler(e) {
                    if (e.key === 'Escape') {
                        closePopup();
                        document.removeEventListener('keydown', escHandler);
                    }
                });
            });
        });
    });

    function showWorkshopOnMap() {
        const gps = "{{ $customer->gps_location }}";
        if (!gps) return;

        const [lat, lng] = gps.split(',').map(coord => parseFloat(coord.trim()));
        if (isNaN(lat) || isNaN(lng)) {
            alert("Invalid GPS coordinates.");
            return;
        }

        const map = new google.maps.Map(document.getElementById('workshopMap'), {
            zoom: 16,
            center: { lat, lng },
            mapTypeId: 'hybrid',
            streetViewControl: true,
            fullscreenControl: true
        });

        const marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
            animation: google.maps.Animation.DROP
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="min-width:220px; padding: 8px;">
                    <h5 class="mb-2"><strong>{{ addslashes($customer->shop_name) }}</strong></h5>
                    <p class="mb-1"><strong>Owner:</strong> {{ $customer->owner_name }}</p>
                    <p class="mb-1"><strong>Mobile:</strong> {{ $customer->mobile_number }}</p>
                    <p class="mb-0 text-muted">
                        <small>{{ $customer->address_line ?? '' }}<br>
                        {{ $city?->city_name ?? '' }}, {{ $state?->state_name ?? '' }}</small>
                    </p>
                </div>
            `
        });

        infoWindow.open(map, marker);
        marker.addListener('click', () => infoWindow.open(map, marker));

        $('.showCustomerLocationonMaps').show();
        document.querySelector('.showCustomerLocationonMaps').scrollIntoView({ behavior: 'smooth' });
    }
</script>
</x-app-layout>
