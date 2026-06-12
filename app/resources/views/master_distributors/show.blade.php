<x-app-layout>

    <style>
        .card.text-center .card-body.p-3 {
            font-size: 16px;
            font-weight: 900;
            line-height: 40px;
            text-shadow: 2px 2px 10px gray;
        }
    </style>
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

    <!-- Points Summary Cards (Agar Master Distributor ke liye points system hai to rakho, warna remove kar dena) -->
    <!-- <div class="row all-points mb-4">
        <div class="col-md-2">
            <div class="card card-primary text-center card-body p-3">
                Total Point Earn <br> <span>{{ $total_points ?? 0 }}</span>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-primary text-center card-body p-3">
                Total Active Point <br> <span>{{ $active_points ?? 0 }}</span>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-primary text-center card-body p-3">
                Total Provision Point <br> <span>{{ $provision_points ?? 0 }}</span>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-success text-center card-body p-3">
                Total Redeem Point <br> <span>{{ $total_redemption ?? 0 }}</span>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-danger text-center card-body p-3">
                Total Rejected Point <br> <span>{{ $total_rejected ?? 0 }}</span>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-info text-center card-body p-3">
                Total Balance Point <br> <span>{{ $total_balance ?? 0 }}</span>
            </div>
        </div>
    </div> -->
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

    <div class="row">
        <div class="col">
            <div class="card card-body">
                <!-- Edit & Download Buttons -->
                <div class="row">
                    <div class="col-md-12">
                        <span class="float-right">
                            <div class="btn-group">
                                @if(auth()->user()->can(['master-distributor_edit']))
                                <a href="{{ route('master-distributors.edit', encrypt($distributor->id)) }}" class="btn btn-just-icon btn-theme">
                                    <i class="material-icons">edit</i>
                                </a>
                                @endif

                                <!-- Agar transaction history download chahiye to add kar sakte ho -->
                            </div>
                        </span>
                    </div>
                </div>

                

                <!-- Profile Header with Image and Name -->

<div class="ctmr-box mt-4">
    <h6>Distributor Images</h6>

    {{-- Legal & Trade Name --}}
    <div class="mb-4">
        <ul class="list-group">

            <li class="list-group-item border-0 ps-0 text-sm">
                <strong class="text-dark">Legal Name:</strong>
                &nbsp;
                {{ $distributor->legal_name ?? '-' }}
            </li>

            <li class="list-group-item border-0 ps-0 text-sm">
                <strong class="text-dark">Trade Name:</strong>
                &nbsp;
                {{ $distributor->trade_name ?? '-' }}
            </li>

        </ul>
    </div>

    <div class="row">

        {{-- Shop Image --}}
        <div class="col-md-6 text-center mb-4">

            <h6 class="mb-2 text-dark">Shop Image</h6>

            @if($distributor->shop_image)

                <a href="{{ asset('storage/' . $distributor->shop_image) }}" target="_blank">

                    <img src="{{ asset('storage/' . $distributor->shop_image) }}"
                         class="img-fluid rounded shadow border"
                         style="height:300px; width:100%; object-fit:cover;">

                </a>

            @else

                <div class="border rounded p-4 bg-light">
                    <p class="text-muted mb-0">No Shop Image</p>
                </div>

            @endif

        </div>

        {{-- Profile Image --}}
        <div class="col-md-6 text-center mb-4">

            <h6 class="mb-2 text-dark">Profile Image</h6>

            @if($distributor->profile_image)

                <a href="{{ asset('storage/' . $distributor->profile_image) }}" target="_blank">

                    <img src="{{ asset('storage/' . $distributor->profile_image) }}"
                         class="img-fluid rounded shadow border"
                         style="height:300px; width:100%; object-fit:cover;">

                </a>

            @else

                <div class="border rounded p-4 bg-light">
                    <p class="text-muted mb-0">No Profile Image</p>
                </div>

            @endif

        </div>

    </div>
</div>              


                <div class="">
                    <div class="tab-content tab-subcategories">
                        <div class="tab-pane active show" id="profile-tabs-detail">
                            <div class="row mt-3">

                                <!-- Personal & Basic Info -->
                                <div class="col-md-4 col-xl-6 mt-md-0 mt-4 position-relative">
                                    <div class="card card-plain">
                                        <div class="card-body p-3">
                                            <div class="ctmr-box">
                                                <h6>Basic Information</h6>
                                                <ul class="list-group">
                                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                        <strong class="text-dark">Distributor Code:</strong> &nbsp; {{ $distributor->distributor_code ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Contact Person:</strong> &nbsp; {{ $distributor->contact_person ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Mobile:</strong> &nbsp; 
                                                        @if($distributor->mobile)
                                                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $distributor->mobile) }}" target="_blank" class="text-success">
                                                                <i class="fab fa-whatsapp"></i> {{ $distributor->mobile }}
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Email:</strong> &nbsp; {{ $distributor->email ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Business Status:</strong> &nbsp;
                                                        <span class="badge badge-{{ $distributor->business_status === 'Active' ? 'success' : 'danger' }}">
                                                            {{ strtoupper($distributor->business_status ?? 'N/A') }}
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Start Date:</strong> &nbsp; 
                                                        {{ $distributor->business_start_date ? \Carbon\Carbon::parse($distributor->business_start_date)->format('d M Y') : '-' }}
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="ctmr-box">
                                                <h6>Billing Address</h6>
                                                <ul class="list-group">
                                                    <!-- <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                        <strong class="text-dark">Address:</strong> &nbsp; {{ $distributor->billing_address ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">City:</strong> &nbsp; {{ $distributor->billing_city ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">District:</strong> &nbsp; {{ $distributor->billing_district ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">State:</strong> &nbsp; {{ $distributor->billing_state ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Pincode:</strong> &nbsp; {{ $distributor->billing_pincode ?? '-' }}
                                                    </li> -->

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Country:</strong> &nbsp;
                                                        {{ $distributor->billing_country_export_name ?? '-' }}
                                                    </li>

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">State:</strong> &nbsp;
                                                        {{ $distributor->billing_state_export_name ?? '-' }}
                                                    </li>

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">District:</strong> &nbsp;
                                                        {{ $distributor->billing_district_export_name ?? '-' }}
                                                    </li>

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">City:</strong> &nbsp;
                                                        {{ $distributor->billing_city_export_name ?? '-' }}
                                                    </li>

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Pincode:</strong> &nbsp;
                                                        {{ $distributor->billing_pincode_export_name ?? '-' }}
                                                    </li>
                                                    <div class="ctmr-box mt-4">
                                                        <h6>Shipping Address</h6>

                                                        <ul class="list-group">

                                                            @php
                                                                $shippingAddresses = is_array($distributor->shipping_address)
                                                                    ? $distributor->shipping_address
                                                                    : json_decode($distributor->shipping_address, true);
                                                            @endphp

                                                            @if($shippingAddresses && count($shippingAddresses))

                                                                @foreach($shippingAddresses as $index => $address)

                                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                                        <strong class="text-dark">
                                                                            Shipping Address {{ $index + 1 }}:
                                                                        </strong>
                                                                        &nbsp;
                                                                        {{ $address }}
                                                                    </li>

                                                                @endforeach

                                                            @else

                                                                <li class="list-group-item border-0 ps-0 text-sm">
                                                                    <span class="text-muted">No Shipping Address</span>
                                                                </li>

                                                            @endif

                                                        </ul>
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KYC & Banking + Business Capacity -->
                                <div class="col-md-4 col-xl-6 mt-md-0 mt-4 position-relative">
                                    <div class="card card-plain h-100">
                                        <div class="card-body p-3">
                                            <div class="ctmr-box">
                                                <h6>KYC & Banking Details</h6>
                                                <ul class="list-group">
                                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                        <strong class="text-dark">GST Number:</strong> &nbsp; {{ $distributor->gst_number ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">PAN Number:</strong> &nbsp; {{ $distributor->pan_number ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Bank Name:</strong> &nbsp; {{ $distributor->bank_name ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Account Holder:</strong> &nbsp; {{ $distributor->account_holder ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Account Number:</strong> &nbsp; {{ $distributor->account_number ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">IFSC Code:</strong> &nbsp; {{ $distributor->ifsc ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Cancelled Cheque:</strong> &nbsp;
                                                        @if($distributor->cancelled_cheque)
                                                            <a href="{{ asset('storage/' . $distributor->cancelled_cheque) }}" target="_blank" class="btn btn-sm btn-theme">
                                                                <i class="material-icons">visibility</i> View
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">MOU File:</strong> &nbsp;

                                                        @if($distributor->mou_file)
                                                            <a href="{{ asset('storage/' . $distributor->mou_file) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-theme">
                                                                <i class="material-icons">visibility</i> View
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="ctmr-box">
                                                <h6>Business Capacity</h6>
                                                <ul class="list-group">
                                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                                        <strong class="text-dark">Credit Limit:</strong> &nbsp; ₹{{ number_format($distributor->credit_limit ?? 0) }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Credit Days:</strong> &nbsp; {{ $distributor->credit_days ?? '-' }} days
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Monthly Sales:</strong> &nbsp; ₹{{ number_format($distributor->monthly_sales ?? 0) }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Turnover:</strong> &nbsp; ₹{{ number_format($distributor->turnover ?? 0) }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Staff Strength:</strong> &nbsp; {{ $distributor->staff_strength ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Vehicles:</strong> &nbsp; {{ $distributor->vehicles_capacity ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Warehouse Size:</strong> &nbsp; {{ $distributor->warehouse_size ?? '-' }}
                                                    </li>

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Sales Executives:</strong> &nbsp;

                                                        @if($distributor->salesExecutives()->count())

                                                            @foreach($distributor->salesExecutives() as $executive)

                                                                <span class="badge bg-info text-dark">
                                                                    {{ $executive->name }}
                                                                </span>

                                                            @endforeach

                                                        @else

                                                            <span class="text-muted">-</span>

                                                        @endif
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Supervisor:</strong> &nbsp;

                                                        {{ $distributor->supervisor->name ?? '-' }}
                                                    </li>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Market Classification:</strong>
                                                        &nbsp; {{ $distributor->market_classification ?? '-' }}
                                                    </li>

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Beat Route:</strong>
                                                        &nbsp; {{ $distributor->beat->beat_name ?? '-' }}
                                                    </li>

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Competitor Brands:</strong>
                                                        &nbsp; {{ $distributor->competitor_brands ?? '-' }}
                                                    </li>

                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong class="text-dark">Customer Segment:</strong>
                                                        &nbsp; {{ $distributor->customer_segment ?? '-' }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Activities Timeline (Right Side) -->
                                <!-- <div class="col-md-4 mt-md-0 mt-4">
                                    <div class="card card-plain h-100 activity-conv">
                                        <div class="card-header pb-0 p-3">
                                            <h6>Recent Activities</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <ul class="timeline timeline-simple"> -->
                                                <!-- Agar activities hai to loop mein daal dena, abhi placeholder -->
                                                <!-- <li class="timeline-inverted">
                                                    <div class="timeline-badge info">
                                                        <i class="material-icons">add_task</i>
                                                    </div>
                                                    <div class="timeline-panel">
                                                        <div class="timeline-body">
                                                            <p>Distributor created</p>
                                                        </div>
                                                        <h6><i class="ti-time"></i> {{ $distributor->created_at->format('d M Y') }}</h6>
                                                    </div>
                                                </li>
                                                <li class="timeline-inverted">
                                                    <h6 class="text-muted">No other activities yet</h6>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div> -->

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
                                                                    <td class="old-col">
                                                                        <span class="diff-old">
                                                                            <!-- {{ $values['old'] === null ? '<em class="text-muted">null</em>' : e($values['old']) }} -->
                                                                              {{ $values['old'] === null ? '<em class="text-muted">null</em>' : (is_array($values['old']) ? e(json_encode($values['old'])) : e($values['old'])) }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="new-col">
                                                                        @if(isset($values['removed']))
                                                                            <span class="diff-removed">
                                                                                <i class="material-icons" style="font-size:14px;">remove_circle</i> Removed
                                                                            </span>
                                                                        @else
                                                                            <span class="diff-new">
                                                                                {{ $values['new'] === null ? '<em class="text-muted">null</em>' : (is_array($values['new']) ? e(json_encode($values['new'])) : e($values['new'])) }}   
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
                                                                    {{ e(json_encode($value, JSON_PRETTY_PRINT)) }}
                                                                @elseif(is_bool($value))
                                                                    {{ $value ? 'true' : 'false' }}
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

                            <!-- Additional Documents -->
                            @if($distributor->documents && ($docs = json_decode($distributor->documents, true)) && count($docs) > 0)
                            <div class="row mt-5">
                                <div class="col-12">
                                    <h5 class="text-theme mb-4"><strong>Additional Documents ({{ count($docs) }})</strong></h5>
                                    <div class="row">
                                        @foreach($docs as $index => $doc)

    @php
        $extension = pathinfo($doc, PATHINFO_EXTENSION);
        $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','webp']);
    @endphp

    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 text-center">

        <a href="{{ asset('storage/' . $doc) }}" target="_blank">

            @if($isImage)

                <img src="{{ asset('storage/' . $doc) }}"
                     class="img-fluid rounded shadow-sm"
                     style="height:150px; width:100%; object-fit:cover;">

            @else

                <img src="{{ asset('assets/img/pdf-icon.png') }}"
                     alt="Document"
                     style="width:80px;"
                     class="mb-2">

            @endif

            <br>

            <small class="text-primary">
                View Document {{ $index + 1 }}
            </small>

        </a>

    </div>

@endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Timestamp Footer -->
                            <div class="text-center mt-5 text-muted border-top pt-4">
                                <small>
                                    Created: {{ showdatetimeformat($distributor->created_at) }} 
                                    | Last Updated: {{ showdatetimeformat($distributor->updated_at) }}
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>