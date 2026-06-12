<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        .ef-dashboard {
            --navy: #0f1e35;
            --accent: #3b82f6;
            --green: #16a34a;
            --amber: #d97706;
            --red: #dc2626;
            --purple: #7c3aed;
            --surface: #fff;
            --surface2: #f8fafc;
            --surface3: #f1f5f9;
            --border: #e2e8f0;
            --border2: #cbd5e1;
            --txt1: #0f172a;
            --txt2: #475569;
            --txt3: #94a3b8;
            background: #f1f5f9;
            color: var(--txt1);
            font-family: 'Inter', sans-serif;
            min-height: calc(100vh - 80px);
            margin: -10px -15px 0;
        }
        .ef-dashboard * { box-sizing: border-box; letter-spacing: 0; }
        .ef-fbar { background: var(--surface); border-bottom: 1px solid var(--border); padding: 0 24px; min-height: 48px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .ef-label { font-size: 10px; font-weight: 700; color: var(--txt3); text-transform: uppercase; }
        .ef-btn { padding: 6px 13px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; border: 1px solid var(--border); background: var(--surface); color: var(--txt2); outline: none; }
        .ef-btn:hover { border-color: var(--accent); color: var(--accent); }
        .ef-btn.active { background: var(--navy); color: #93c5fd; border-color: var(--navy); }
        .ef-sep { width: 1px; height: 20px; background: var(--border); margin: 0 4px; }
        .ef-date { font-size: 11px; border: 1px solid var(--border); border-radius: 6px; padding: 5px 9px; color: var(--txt1); background: var(--surface); outline: none; font-family: 'Inter', sans-serif; }
        .ef-apply { padding: 6px 13px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; border: none; background: #2563eb; color: #fff; outline: none; }
        .ef-tag { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
        .ef-content { padding: 18px 24px 26px; }
        .ef-tabs { display: flex; gap: 3px; margin-bottom: 18px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 4px; width: fit-content; max-width: 100%; overflow-x: auto; }
        .ef-tab { padding: 8px 15px; border-radius: 7px; font-size: 12px; font-weight: 500; cursor: pointer; color: var(--txt2); display: flex; align-items: center; gap: 5px; white-space: nowrap; border: 0; background: transparent; }
        .ef-tab.active { background: var(--navy); color: #93c5fd; font-weight: 600; }
        .ef-panel { display: none; }
        .ef-panel.active { display: block; }
        .ef-slabel { font-size: 10px; font-weight: 700; color: var(--txt3); letter-spacing: .1em; text-transform: uppercase; margin: 18px 0 12px; display: flex; align-items: center; gap: 10px; }
        .ef-slabel:first-child { margin-top: 0; }
        .ef-slabel::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .ef-kgrid, .ef-cgrid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .ef-g2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 12px; }
        .ef-card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; transition: box-shadow .2s; }
        .ef-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); }
        .ef-kcard { padding: 15px 17px; position: relative; overflow: hidden; }
        .ef-kcard::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--accent); }
        .ef-kcard.green::before { background: var(--green); }
        .ef-kcard.amber::before { background: var(--amber); }
        .ef-kcard.red::before { background: var(--red); }
        .ef-klabel { font-size: 10px; font-weight: 700; color: var(--txt3); text-transform: uppercase; margin-bottom: 7px; display: flex; align-items: center; gap: 5px; }
        .ef-kval { font-size: 24px; font-weight: 700; color: var(--txt1); line-height: 1; }
        .ef-ksub, .ef-note { font-size: 10px; color: var(--txt3); margin-top: 5px; font-weight: 500; }
        .ef-trend { font-size: 10px; margin-top: 7px; display: flex; align-items: center; gap: 3px; font-weight: 600; color: var(--green); }
        .ef-target-row { display: flex; gap: 8px; margin-top: 7px; }
        .ef-target-box { flex: 1; background: var(--surface3); border-radius: 5px; padding: 6px 7px; min-width: 0; }
        .ef-target-lbl { font-size: 9px; font-weight: 700; color: var(--txt3); text-transform: uppercase; }
        .ef-target-val { font-size: 13px; font-weight: 700; color: var(--txt1); margin-top: 1px; overflow-wrap: anywhere; }
        .ef-target-pct { font-size: 10px; font-weight: 700; margin-top: 7px; color: var(--amber); }
        .ef-ccard { overflow: hidden; }
        .ef-ctop { padding: 13px 13px 9px; display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
        .ef-cicon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .ef-ctag { font-size: 9px; font-weight: 700; padding: 3px 7px; border-radius: 20px; text-transform: uppercase; }
        .ef-ctotal { font-size: 26px; font-weight: 700; color: var(--txt1); padding: 0 13px 2px; }
        .ef-clbl { font-size: 10px; color: var(--txt3); padding: 0 13px 9px; font-weight: 600; text-transform: uppercase; }
        .ef-csplit { display: flex; gap: 5px; margin: 0 13px 9px; }
        .ef-csb { flex: 1; border-radius: 6px; padding: 7px 8px; min-width: 0; }
        .ef-csb.active { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .ef-csb.inactive { background: var(--surface3); border: 1px solid var(--border); }
        .ef-csnum { font-size: 14px; font-weight: 700; line-height: 1.1; color: var(--txt2); overflow-wrap: anywhere; }
        .ef-csb.active .ef-csnum { color: #15803d; }
        .ef-cslbl { font-size: 9px; font-weight: 700; margin-top: 2px; color: var(--txt3); text-transform: uppercase; }
        .ef-csb.active .ef-cslbl { color: #16a34a; }
        .ef-cbar-wrap { padding: 0 13px 13px; }
        .ef-track { background: var(--surface3); border-radius: 3px; height: 4px; overflow: hidden; }
        .ef-fill { height: 4px; border-radius: 3px; background: var(--accent); }
        .ef-cpct { font-size: 9px; color: var(--txt3); margin-top: 3px; text-align: right; font-weight: 600; }
        .ef-chart { padding: 15px; min-width: 0; }
        .ef-chtitle { font-size: 12px; font-weight: 600; color: var(--txt1); margin-bottom: 9px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 4px; }
        .ef-badge { font-size: 9px; font-weight: 700; padding: 3px 8px; border-radius: 20px; color: #1d4ed8; background: #eff6ff; }
        .ef-badge.purple { color: #6d28d9; background: #faf5ff; }
        .ef-badge.orange { color: #c2410c; background: #fff7ed; }
        .ef-legend { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 7px; }
        .ef-legitem { display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 600; color: var(--txt2); }
        .ef-legsq { width: 9px; height: 9px; border-radius: 2px; flex-shrink: 0; }
        .ef-canvas { position: relative; height: 190px; }
        .ef-canvas.tall { height: 320px; }
        .ef-empty { display: none; align-items: center; justify-content: center; min-height: 120px; color: var(--txt3); font-size: 12px; font-weight: 600; background: var(--surface2); border: 1px dashed var(--border2); border-radius: 8px; }
        .ef-soon { min-height: 300px; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 8px; text-align: center; }
        .ef-soon i { font-size: 36px; color: var(--accent); }
        .ef-soon h3 { margin: 0; font-size: 18px; color: var(--txt1); font-weight: 700; }
        .ef-soon p { margin: 0; color: var(--txt3); font-size: 12px; font-weight: 600; }
        .ef-state { display: none; margin-bottom: 12px; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--txt2); font-size: 12px; font-weight: 600; }
        .ef-state.error { border-color: #fecaca; color: #b91c1c; background: #fef2f2; }
        .ef-cat-wrap { display: flex; align-items: center; gap: 14px; margin-top: 4px; }
        .ef-cat-canvas { position: relative; width: 130px; height: 130px; flex-shrink: 0; }
        .ef-ekgrid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
        .ef-ekcard { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 16px; transition: box-shadow .2s; }
        .ef-ekcard:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); }
        .ef-ekiconwrap { width: 40px; height: 40px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 8px; }
        .ef-ekval { font-size: 28px; font-weight: 700; letter-spacing: 0; line-height: 1; }
        .ef-eklbl { font-size: 11px; color: var(--txt2); font-weight: 600; margin-top: 2px; }
        .ef-eksub { font-size: 10px; font-weight: 600; display: flex; align-items: center; gap: 3px; margin-top: 4px; }
        .ef-emp-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
        .ef-escard { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 12px; }
        .ef-esicon { width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .ef-esval { font-size: 20px; font-weight: 700; color: var(--txt1); line-height: 1.1; }
        .ef-eslbl { font-size: 10px; color: var(--txt3); font-weight: 600; text-transform: uppercase; margin-top: 2px; }
        .ef-emp-chart-wrap { overflow-x: auto; padding-bottom: 6px; }
        .ef-emp-chart-inner { min-width: 1400px; height: 320px; }
        @media (max-width: 1200px) { .ef-kgrid, .ef-cgrid, .ef-ekgrid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 900px) { .ef-g2 { grid-template-columns: 1fr; } .ef-dashboard { margin: 0; } }
        @media (max-width: 640px) {
            .ef-fbar, .ef-content { padding-left: 12px; padding-right: 12px; }
            .ef-kgrid, .ef-cgrid, .ef-ekgrid, .ef-emp-summary { grid-template-columns: 1fr; }
            .ef-sep { display: none; }
            .ef-cat-wrap { align-items: flex-start; flex-direction: column; }
        }
    </style>

    <div class="ef-dashboard" data-secondary-url="{{ route('dashboard.secondary-sales') }}" data-product-url="{{ route('dashboard.products') }}" data-primary-url="{{ route('dashboard.primary-sales') }}" data-employees-url="{{ route('dashboard.employees') }}">
        <div class="ef-fbar">
            <span class="ef-label">Period</span>
            <button type="button" class="ef-btn active" data-filter="MTD">MTD</button>
            <button type="button" class="ef-btn" data-filter="YTD">YTD (Apr-Mar)</button>
            <button type="button" class="ef-btn" data-filter="CUSTOM">Custom</button>
            <div class="ef-sep"></div>
            <div id="efCustomDates" style="display:none;align-items:center;gap:6px;flex-wrap:wrap;">
                <input type="date" id="efFrom" class="ef-date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                <span style="font-size:11px;color:var(--txt3);">to</span>
                <input type="date" id="efTo" class="ef-date" value="{{ now()->format('Y-m-d') }}">
                <button type="button" class="ef-apply" id="efApply">Apply</button>
            </div>
            <div class="ef-sep"></div>
            <span class="ef-label">Zone</span>
            <select id="efZone" class="ef-date">
                <option value="">All Zones</option>
                @foreach(($dashboardZones ?? collect()) as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->branch_name }}</option>
                @endforeach
            </select>
            <div class="ef-sep"></div>
            <span class="ef-tag" id="efPeriodTag">MTD</span>
        </div>

        <div class="ef-content">
            <div id="efLoading" class="ef-state">Loading dashboard data...</div>
            <div id="efError" class="ef-state error"></div>

            <div class="ef-tabs">
                <button type="button" class="ef-tab active" data-tab="secondary"><i class="ti ti-trending-up"></i>Secondary Sales</button>
                <button type="button" class="ef-tab" data-tab="primary"><i class="ti ti-chart-bar"></i>Primary Sales</button>
                <button type="button" class="ef-tab" data-tab="product"><i class="ti ti-package"></i>Product</button>
                <button type="button" class="ef-tab" data-tab="employees"><i class="ti ti-users"></i>Employees</button>
                <!-- <button type="button" class="ef-tab" data-tab="heatmap"><i class="ti ti-map"></i>Customer Map</button> -->
            </div>

            <div id="ef-secondary" class="ef-panel active">
                <div class="ef-slabel">Key Metrics</div>
                <div class="ef-kgrid">
                    <div class="ef-card ef-kcard">
                        <div class="ef-klabel"><i class="ti ti-users"></i>Total Registrations</div>
                        <div class="ef-kval" data-kpi="totalRegistrations">-</div>
                        <div class="ef-ksub">All secondary customer types</div>
                        <div class="ef-trend"><i class="ti ti-info-circle"></i>Total registered base</div>
                    </div>
                    <div class="ef-card ef-kcard green">
                        <div class="ef-klabel"><i class="ti ti-user-check"></i>Active Customers</div>
                        <div class="ef-kval" data-kpi="activeCustomers">-</div>
                        <div class="ef-ksub">At least 1 order in last 12 months</div>
                        <div class="ef-trend"><i class="ti ti-circle-check"></i>Rolling activity rule</div>
                    </div>
                    <div class="ef-card ef-kcard amber">
                        <div class="ef-klabel"><i class="ti ti-coin"></i>Secondary Sales</div>
                        <div class="ef-target-row">
                            <div class="ef-target-box">
                                <div class="ef-target-lbl">Achievement</div>
                                <div class="ef-target-val" data-kpi="secondarySalesAchievement">-</div>
                            </div>
                            <div class="ef-target-box">
                                <div class="ef-target-lbl">Target</div>
                                <div class="ef-target-val" data-kpi="secondarySalesTarget">-</div>
                            </div>
                        </div>
                        <div class="ef-target-pct" data-kpi="targetGap">-</div>
                    </div>
                    <div class="ef-card ef-kcard red">
                        <div class="ef-klabel"><i class="ti ti-target"></i>Overall Achievement</div>
                        <div class="ef-kval" data-kpi="overallAchievementPercent">-</div>
                        <div class="ef-ksub" data-kpi="targetSub">vs target</div>
                        <div class="ef-trend" style="color:var(--amber);"><i class="ti ti-alert-circle"></i>Target recalculates by period</div>
                    </div>
                </div>

                <div class="ef-slabel">Customer Registration Breakdown</div>
                <div class="ef-cgrid" id="efBreakdown"></div>
                <div class="ef-note"><i class="ti ti-info-circle"></i>Registration totals are lifetime totals; active status is rolling 12 months because the business rule is activity-based.</div>

                <div class="ef-slabel">Performance Charts</div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Top 10 customers - by sales order value<span class="ef-badge" data-badge></span></div>
                        <div class="ef-empty" data-empty="sTop">No customer sales found for this period</div>
                        <div class="ef-canvas"><canvas id="sTop"></canvas></div>
                    </div>
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Sales vs target<span class="ef-badge" data-badge></span></div>
                        <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:#3b82f6;"></span>Achievement</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                        <div class="ef-empty" data-empty="sTarget">No sales or target found for this period</div>
                        <div class="ef-canvas"><canvas id="sTarget"></canvas></div>
                    </div>
                </div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Total sales trend<span class="ef-badge" data-badge></span></div>
                        <div class="ef-empty" data-empty="sTrend">No sales trend found for this period</div>
                        <div class="ef-canvas"><canvas id="sTrend"></canvas></div>
                    </div>
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Year-on-year growth<span class="ef-badge">FY comparison</span></div>
                        <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:#bfdbfe;"></span>FY24</span><span class="ef-legitem"><span class="ef-legsq" style="background:#3b82f6;"></span>FY25</span><span class="ef-legitem"><span class="ef-legsq" style="background:#1e3a5f;"></span>FY26</span></div>
                        <div class="ef-empty" data-empty="sYoy">No year-on-year sales found</div>
                        <div class="ef-canvas"><canvas id="sYoy"></canvas></div>
                    </div>
                </div>
                <div class="ef-card ef-chart">
                    <div class="ef-chtitle">Zone-wise sales<span class="ef-badge" data-badge></span></div>
                    <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:#3b82f6;"></span>Achievement</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                    <div class="ef-empty" data-empty="sRegion">No zone-wise sales found for this period</div>
                    <div class="ef-canvas"><canvas id="sRegion"></canvas></div>
                </div>
            </div>

            <div id="ef-primary" class="ef-panel">
                <div class="ef-slabel">Performance Charts</div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Total sales trend<span class="ef-badge" data-badge></span></div>
                        <div class="ef-empty" data-empty="prTrend">No primary sales trend found for this period</div>
                        <div class="ef-canvas" style="height:185px;"><canvas id="prTrend"></canvas></div>
                    </div>
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Sales vs target<span class="ef-badge" data-badge></span></div>
                        <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:#16a34a;"></span>Achievement</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                        <div class="ef-empty" data-empty="prTarget">No primary sales or target found for this period</div>
                        <div class="ef-canvas" style="height:155px;"><canvas id="prTarget"></canvas></div>
                    </div>
                </div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Year-on-year growth<span class="ef-badge">FY comparison</span></div>
                        <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:#bbf7d0;"></span>FY24</span><span class="ef-legitem"><span class="ef-legsq" style="background:#16a34a;"></span>FY25</span><span class="ef-legitem"><span class="ef-legsq" style="background:#14532d;"></span>FY26</span></div>
                        <div class="ef-empty" data-empty="prYoy">No primary year-on-year sales found</div>
                        <div class="ef-canvas" style="height:160px;"><canvas id="prYoy"></canvas></div>
                    </div>
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Zone-wise sales<span class="ef-badge" data-badge></span></div>
                        <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:#16a34a;"></span>Achievement</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                        <div class="ef-empty" data-empty="prRegion">No primary zone-wise sales found for this period</div>
                        <div class="ef-canvas" style="height:155px;"><canvas id="prRegion"></canvas></div>
                    </div>
                </div>
            </div>

            <div id="ef-product" class="ef-panel">
                <div class="ef-slabel">Product Analytics</div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Top 23 products - by sales value<span class="ef-badge purple" data-badge></span></div>
                        <div class="ef-empty" data-empty="pTop">No product sales found for this period</div>
                        <div class="ef-canvas tall"><canvas id="pTop"></canvas></div>
                    </div>
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Slow moving products - by sales value<span class="ef-badge orange" data-badge></span></div>
                        <div class="ef-empty" data-empty="pSlow">No slow moving product data found</div>
                        <div class="ef-canvas tall"><canvas id="pSlow"></canvas></div>
                    </div>
                </div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Segment-wise sales - by sales value<span class="ef-badge purple" data-badge></span></div>
                        <div class="ef-empty" data-empty="pSegment">No segment sales found</div>
                        <div class="ef-cat-wrap">
                            <div class="ef-cat-canvas"><canvas id="pSegment"></canvas></div>
                            <div style="flex:1;width:100%;" id="pSegmentLegend"></div>
                        </div>
                    </div>
                    <div class="ef-card ef-chart">
                        <div class="ef-chtitle">Top 10 SKU trend - by sales value<span class="ef-badge purple" data-badge></span></div>
                        <div id="pSkuLegend" class="ef-legend"></div>
                        <div class="ef-empty" data-empty="pSku">No SKU trend found</div>
                        <div class="ef-canvas"><canvas id="pSku"></canvas></div>
                    </div>
                </div>
            </div>

            <div id="ef-employees" class="ef-panel">
                <div class="ef-slabel">Today's Snapshot</div>
                <div class="ef-ekgrid">
                    <div class="ef-ekcard"><div class="ef-ekiconwrap" style="background:#eff6ff;color:#2563eb;"><i class="ti ti-users"></i></div><div class="ef-ekval" style="color:#1e3a5f;" data-emp-kpi="totalEmployees">-</div><div class="ef-eklbl">Total Employees</div><div class="ef-eksub" style="color:var(--txt3);"><i class="ti ti-building" style="font-size:11px;"></i> All active roles</div></div>
                    <div class="ef-ekcard"><div class="ef-ekiconwrap" style="background:#f0fdf4;color:#16a34a;"><i class="ti ti-map-pin"></i></div><div class="ef-ekval" style="color:#14532d;" data-emp-kpi="todayOnMarket">-</div><div class="ef-eklbl">Today on Market</div><div class="ef-eksub" style="color:#16a34a;"><i class="ti ti-circle-check" style="font-size:11px;"></i> Field presence</div></div>
                    <div class="ef-ekcard"><div class="ef-ekiconwrap" style="background:#fffbeb;color:#d97706;"><i class="ti ti-calendar-off"></i></div><div class="ef-ekval" style="color:#78350f;" data-emp-kpi="todayOnLeave">-</div><div class="ef-eklbl">Today on Leave</div><div class="ef-eksub" style="color:#d97706;"><i class="ti ti-alert-circle" style="font-size:11px;"></i> Of active strength</div></div>
                    <div class="ef-ekcard"><div class="ef-ekiconwrap" style="background:#fef2f2;color:#dc2626;"><i class="ti ti-clock-exclamation"></i></div><div class="ef-ekval" style="color:#7f1d1d;" data-emp-kpi="misPunch">-</div><div class="ef-eklbl">MIS Punch</div><div class="ef-eksub" style="color:#dc2626;"><i class="ti ti-alert-triangle" style="font-size:11px;"></i> Attendance mismatch</div></div>
                </div>

                <div class="ef-slabel">Team Order Summary</div>
                <div class="ef-emp-summary">
                    <div class="ef-escard">
                        <div class="ef-esicon" style="background:#eff6ff;color:#2563eb;"><i class="ti ti-shopping-cart"></i></div>
                        <div><div class="ef-esval" data-emp-summary="totalOrderQty">-</div><div class="ef-eslbl">Total Order Qty (Nos)</div></div>
                    </div>
                    <div class="ef-escard">
                        <div class="ef-esicon" style="background:#f0fdf4;color:#16a34a;"><i class="ti ti-coin"></i></div>
                        <div><div class="ef-esval" data-emp-summary="totalOrderValue">-</div><div class="ef-eslbl">Total Order Value</div></div>
                    </div>
                    <div class="ef-escard">
                        <div class="ef-esicon" style="background:#faf5ff;color:#7c3aed;"><i class="ti ti-user-check"></i></div>
                        <div><div class="ef-esval" data-emp-summary="uniqueActiveCustomers">-</div><div class="ef-eslbl">Unique Active Customers</div></div>
                    </div>
                </div>

                <div class="ef-slabel">Target vs Achievement - all employees (scroll)</div>
                <div class="ef-card ef-chart" style="padding:15px 15px 10px;">
                    <div class="ef-chtitle" style="margin-bottom:8px;">Employee target vs achievement<span class="ef-badge" data-badge></span></div>
                    <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:#16a34a;"></span>90%+</span><span class="ef-legitem"><span class="ef-legsq" style="background:#3b82f6;"></span>75-89%</span><span class="ef-legitem"><span class="ef-legsq" style="background:#d97706;"></span>60-74%</span><span class="ef-legitem"><span class="ef-legsq" style="background:#dc2626;"></span>&lt;60%</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                    <div class="ef-empty" data-empty="empTarget">No employee achievement or target found for this period</div>
                    <div class="ef-emp-chart-wrap"><div class="ef-emp-chart-inner"><canvas id="empTarget"></canvas></div></div>
                </div>
            </div>

            <div id="ef-heatmap" class="ef-panel">
                <div class="ef-slabel">Customer Map</div>
                <div class="ef-card ef-soon"><i class="ti ti-map-pin"></i><h3>Coming Soon</h3><p>Customer map pins will be connected after map data is finalized.</p></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <script>
        (() => {
            const root = document.querySelector('.ef-dashboard');
            const urls = { secondary: root.dataset.secondaryUrl, product: root.dataset.productUrl, primary: root.dataset.primaryUrl, employees: root.dataset.employeesUrl };
            const charts = {};
            const colors = ['#3b82f6','#7c3aed','#16a34a','#ea580c','#d97706','#f43f5e','#06b6d4','#84cc16','#a855f7','#64748b'];
            const state = { filterType: 'MTD', fromDate: document.getElementById('efFrom').value, toDate: document.getElementById('efTo').value, zoneId: document.getElementById('efZone').value, activeTab: 'secondary', loaded: { secondary: false, product: false, primary: false, employees: false } };
            const loading = document.getElementById('efLoading');
            const error = document.getElementById('efError');
            const periodTag = document.getElementById('efPeriodTag');

            const compact = (value) => new Intl.NumberFormat('en-IN').format(Math.round(Number(value || 0)));
            const money = (value) => {
                value = Number(value || 0);
                if (value >= 10000000) return 'Rs. ' + (value / 10000000).toFixed(2).replace(/\.00$/, '') + ' Cr';
                if (value >= 100000) return 'Rs. ' + (value / 100000).toFixed(2).replace(/\.00$/, '') + ' L';
                return 'Rs. ' + compact(value);
            };
            const lakh = (value) => Number((Number(value || 0) / 100000).toFixed(2));
            const crore = (value) => Number((Number(value || 0) / 10000000).toFixed(2));
            const scaledMoney = (value, unit) => {
                const number = Number(value || 0).toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1');
                return '\u20B9' + number + unit;
            };
            const chartTick = (value) => money(value).replace('Rs. ', '');
            const scaledChartOptions = (unit, indexAxis = 'x', categoryTicks = {}) => {
                const valueAxis = indexAxis === 'y' ? 'x' : 'y';
                const categoryAxis = indexAxis === 'y' ? 'y' : 'x';
                return {
                    indexAxis,
                    options: {
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const value = indexAxis === 'y' ? ctx.parsed.x : ctx.parsed.y;
                                        return (ctx.dataset.label ? ctx.dataset.label + ': ' : '') + scaledMoney(value, unit);
                                    }
                                }
                            }
                        },
                        scales: {
                            [categoryAxis]: {
                                ticks: Object.assign({
                                    font: { size: 9, family: 'Inter' },
                                    color: '#94a3b8',
                                    autoSkip: false,
                                    maxRotation: 0,
                                    minRotation: 0
                                }, categoryTicks),
                                grid: { display: false },
                                border: { display: false }
                            },
                            [valueAxis]: {
                                ticks: {
                                    font: { size: 9, family: 'Inter' },
                                    color: '#94a3b8',
                                    callback: value => scaledMoney(value, unit)
                                },
                                grid: { color: 'rgba(0,0,0,.04)' },
                                border: { display: false }
                            }
                        }
                    }
                };
            };
            const hasValues = (rows, keys = ['value']) => rows && rows.some(row => keys.some(key => Number(row[key] || 0) > 0));
            const setVisible = (el, show) => { if (el) el.style.display = show ? 'flex' : 'none'; };
            const setStatus = (msg = '', isError = false) => {
                loading.style.display = msg && !isError ? 'block' : 'none';
                error.style.display = msg && isError ? 'block' : 'none';
                error.textContent = isError ? msg : '';
            };
            const params = () => {
                const query = new URLSearchParams({ filterType: state.filterType });
                if (state.filterType === 'CUSTOM') {
                    query.set('fromDate', state.fromDate);
                    query.set('toDate', state.toDate);
                }
                if (state.zoneId) {
                    query.set('zoneId', state.zoneId);
                }
                return query.toString();
            };
            const fetchJson = async (url) => {
                const res = await fetch(url + '?' + params(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Dashboard API failed with HTTP ' + res.status);
                return res.json();
            };
            const destroy = (id) => { if (charts[id]) { charts[id].destroy(); delete charts[id]; } };
            const empty = (id, rows, keys) => {
                const show = !hasValues(rows, keys);
                setVisible(document.querySelector(`[data-empty="${id}"]`), show);
                const canvas = document.getElementById(id);
                if (canvas) canvas.parentElement.style.display = show ? 'none' : 'block';
                if (show) destroy(id);
                return show;
            };
            const baseOptions = (indexAxis = 'x') => ({
                responsive: true,
                maintainAspectRatio: false,
                indexAxis,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => money(indexAxis === 'y' ? ctx.parsed.x : ctx.parsed.y) } } },
                scales: {
                    x: { ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8', callback: indexAxis === 'y' ? chartTick : undefined }, grid: { display: indexAxis === 'y', color: 'rgba(0,0,0,.04)' }, border: { display: false } },
                    y: { ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8', callback: indexAxis === 'y' ? undefined : chartTick }, grid: { color: 'rgba(0,0,0,.04)', display: indexAxis !== 'y' }, border: { display: false } }
                }
            });
            const bar = (id, labels, datasets, opts = {}) => {
                destroy(id);
                const options = baseOptions(opts.indexAxis);
                if (opts.options) {
                    Object.assign(options, opts.options);
                    if (opts.options.scales) {
                        options.scales = options.scales || {};
                        options.scales.x = Object.assign(baseOptions(opts.indexAxis).scales.x, opts.options.scales.x || {});
                        options.scales.y = Object.assign(baseOptions(opts.indexAxis).scales.y, opts.options.scales.y || {});
                    }
                    if (opts.options.plugins) {
                        options.plugins = Object.assign(baseOptions(opts.indexAxis).plugins, opts.options.plugins);
                    }
                }
                charts[id] = new Chart(document.getElementById(id), { type: 'bar', data: { labels, datasets }, options });
            };
            const line = (id, labels, datasets, opts = {}) => {
                destroy(id);
                const options = baseOptions();
                if (opts.options) {
                    Object.assign(options, opts.options);
                    if (opts.options.scales) {
                        options.scales = options.scales || {};
                        options.scales.x = Object.assign(baseOptions().scales.x, opts.options.scales.x || {});
                        options.scales.y = Object.assign(baseOptions().scales.y, opts.options.scales.y || {});
                    }
                    if (opts.options.plugins) {
                        options.plugins = Object.assign(baseOptions().plugins, opts.options.plugins);
                    }
                }
                charts[id] = new Chart(document.getElementById(id), { type: 'line', data: { labels, datasets }, options });
            };
            const doughnut = (id, rows) => {
                destroy(id);
                charts[id] = new Chart(document.getElementById(id), {
                    type: 'doughnut',
                    data: { labels: rows.map(r => r.label), datasets: [{ data: rows.map(r => r.value), backgroundColor: colors, borderWidth: 0 }] },
                    options: { responsive: false, cutout: '68%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => `${ctx.label}: ${money(ctx.parsed)}` } } } }
                });
            };
            const updateBadges = (label) => {
                periodTag.textContent = label;
                document.querySelectorAll('[data-badge]').forEach(el => el.textContent = label);
            };
            const resetLoaded = () => {
                Object.keys(state.loaded).forEach(key => { state.loaded[key] = false; });
            };
            const renderBreakdown = (rows) => {
                const meta = {
                    Retailers: ['ti-building-store', '#eff6ff', '#2563eb'],
                    Mechanics: ['ti-tool', '#faf5ff', '#7c3aed'],
                    Garages: ['ti-car', '#fff7ed', '#ea580c'],
                    Workshops: ['ti-hammer', '#f0fdf4', '#16a34a']
                };
                document.getElementById('efBreakdown').innerHTML = rows.map(row => {
                    const m = meta[row.type] || ['ti-user', '#eff6ff', '#2563eb'];
                    return `<div class="ef-card ef-ccard">
                        <div class="ef-ctop"><div class="ef-cicon" style="background:${m[1]};color:${m[2]};"><i class="ti ${m[0]}"></i></div><span class="ef-ctag" style="background:${m[1]};color:${m[2]};">${row.type}</span></div>
                        <div class="ef-ctotal">${compact(row.total)}</div><div class="ef-clbl">Total Registered</div>
                        <div class="ef-csplit"><div class="ef-csb active"><div class="ef-csnum">${compact(row.active)}</div><div class="ef-cslbl">Active</div></div><div class="ef-csb inactive"><div class="ef-csnum">${compact(row.inactive)}</div><div class="ef-cslbl">Inactive</div></div></div>
                        <div class="ef-cbar-wrap"><div class="ef-track"><div class="ef-fill" style="width:${row.activePercent}%;background:${m[2]};"></div></div><div class="ef-cpct">${row.activePercent}% active</div></div>
                    </div>`;
                }).join('');
            };
            const renderSecondary = (data) => {
                updateBadges(data.periodLabel);
                const k = data.kpis;
                document.querySelector('[data-kpi="totalRegistrations"]').textContent = compact(k.totalRegistrations);
                document.querySelector('[data-kpi="activeCustomers"]').textContent = compact(k.activeCustomers);
                document.querySelector('[data-kpi="secondarySalesAchievement"]').textContent = money(k.secondarySalesAchievement);
                document.querySelector('[data-kpi="secondarySalesTarget"]').textContent = money(k.secondarySalesTarget);
                document.querySelector('[data-kpi="overallAchievementPercent"]').textContent = Number(k.overallAchievementPercent || 0).toFixed(1).replace('.0', '') + '%';
                document.querySelector('[data-kpi="targetSub"]').textContent = 'vs ' + money(k.secondarySalesTarget) + ' target';
                const gap = Math.max(0, 100 - Number(k.overallAchievementPercent || 0));
                document.querySelector('[data-kpi="targetGap"]').innerHTML = `<i class="ti ti-alert-circle"></i> ${Number(k.overallAchievementPercent || 0).toFixed(1)}% achieved | ${gap.toFixed(1)}% gap`;
                renderBreakdown(data.customerBreakdown || []);

                const top = data.charts.topCustomers || [];
                if (!empty('sTop', top)) bar('sTop', top.map(r => r.label), [{ data: top.map(r => lakh(r.value)), backgroundColor: '#3b82f6', borderRadius: 4, barThickness: 12 }], scaledChartOptions('L', 'x', { font: { size: 8, family: 'Inter' }, maxRotation: 18, minRotation: 18 }));
                const target = data.charts.salesVsTarget || [];
                if (!empty('sTarget', target, ['achievement', 'target'])) bar('sTarget', target.map(r => r.label), [
                    { label: 'Achievement', data: target.map(r => lakh(r.achievement)), backgroundColor: '#3b82f6', borderRadius: 4, barThickness: 14 },
                    { label: 'Target', data: target.map(r => lakh(r.target)), backgroundColor: '#e2e8f0', borderRadius: 4, barThickness: 14 }
                ], scaledChartOptions('L', 'x', { font: { size: 9, family: 'Inter' } }));
                const trend = data.charts.salesTrend || [];
                if (!empty('sTrend', trend)) line('sTrend', trend.map(r => r.label), [{ data: trend.map(r => crore(r.value)), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,.09)', fill: true, tension: .4, pointRadius: 3, pointBackgroundColor: '#3b82f6', borderWidth: 2 }], scaledChartOptions('Cr', 'x', { font: { size: 9, family: 'Inter' } }));
                const yoy = data.charts.yoyGrowth || [];
                const yoyRows = yoy.flatMap(y => y.data || []);
                if (!empty('sYoy', yoyRows)) bar('sYoy', (yoy[0]?.data || []).map(r => r.label), yoy.map((series, i) => ({ label: series.label, data: (series.data || []).map(r => crore(r.value)), backgroundColor: ['#bfdbfe','#3b82f6','#1e3a5f'][i] || colors[i], borderRadius: 3, barThickness: 8 })), scaledChartOptions('Cr', 'x', { font: { size: 9, family: 'Inter' } }));
                const region = data.charts.regionWiseSales || [];
                if (!empty('sRegion', region, ['achievement', 'target'])) bar('sRegion', region.map(r => r.label), [
                    { label: 'Achievement', data: region.map(r => lakh(r.achievement)), backgroundColor: '#3b82f6', borderRadius: 4, barThickness: 16 },
                    { label: 'Target', data: region.map(r => lakh(r.target)), backgroundColor: '#e2e8f0', borderRadius: 4, barThickness: 16 }
                ], scaledChartOptions('L', 'x', { font: { size: 9, family: 'Inter' } }));
            };
            const renderProduct = (data) => {
                updateBadges(data.periodLabel);
                const top = data.charts.topProducts || [];
                if (!empty('pTop', top)) bar('pTop', top.map(r => r.label), [{ data: top.map(r => lakh(r.value)), backgroundColor: '#7c3aed', borderRadius: 3, barThickness: 8 }], scaledChartOptions('L', 'y', { font: { size: 8, family: 'Inter' }, autoSkip: false }));
                const slow = data.charts.slowMovingProducts || [];
                if (!empty('pSlow', slow)) bar('pSlow', slow.map(r => r.label), [{ data: slow.map(r => lakh(r.value)), backgroundColor: '#ea580c', borderRadius: 3, barThickness: 13 }], scaledChartOptions('L', 'y', { font: { size: 8, family: 'Inter' }, autoSkip: false }));
                const seg = data.charts.segmentWiseSales || [];
                if (!empty('pSegment', seg)) {
                    doughnut('pSegment', seg);
                    const total = seg.reduce((sum, row) => sum + Number(row.value || 0), 0);
                    document.getElementById('pSegmentLegend').innerHTML = seg.map((row, i) => `<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;gap:8px;"><span style="display:flex;align-items:center;gap:5px;min-width:0;"><span style="width:8px;height:8px;border-radius:2px;background:${colors[i % colors.length]};display:inline-block;flex-shrink:0;"></span><span style="font-size:11px;color:#475569;font-weight:500;overflow:hidden;text-overflow:ellipsis;">${row.label}</span></span><span style="font-size:12px;font-weight:700;color:#0f172a;">${total ? Math.round((row.value / total) * 100) : 0}%</span></div>`).join('');
                } else {
                    document.getElementById('pSegmentLegend').innerHTML = '';
                }
                const sku = data.charts.topSkuTrend || [];
                const skuRows = sku.flatMap(s => s.data || []);
                if (!empty('pSku', skuRows)) {
                    document.getElementById('pSkuLegend').innerHTML = sku.map((s, i) => `<span class="ef-legitem"><span class="ef-legsq" style="background:${colors[i % colors.length]};"></span><span style="font-size:9px;">${s.label}</span></span>`).join('');
                    line('pSku', (sku[0]?.data || []).map(r => r.label), sku.map((series, i) => ({ label: series.label, data: (series.data || []).map(r => lakh(r.value)), borderColor: colors[i % colors.length], backgroundColor: 'transparent', tension: .4, pointRadius: 2, borderWidth: 1.8 })), scaledChartOptions('L', 'x', { font: { size: 9, family: 'Inter' } }));
                } else {
                    document.getElementById('pSkuLegend').innerHTML = '';
                }
            };
            const renderPrimary = (data) => {
                updateBadges(data.periodLabel);
                const target = data.charts.salesVsTarget || [];
                if (!empty('prTarget', target, ['achievement', 'target'])) bar('prTarget', target.map(r => r.label), [
                    { data: target.map(r => r.achievement), backgroundColor: '#16a34a', borderRadius: 4, barThickness: 14 },
                    { data: target.map(r => r.target), backgroundColor: '#e2e8f0', borderRadius: 4, barThickness: 14 }
                ]);
                const trend = data.charts.salesTrend || [];
                if (!empty('prTrend', trend)) line('prTrend', trend.map(r => r.label), [{ data: trend.map(r => r.value), borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.09)', fill: true, tension: .4, pointRadius: 3, borderWidth: 2 }]);
                const yoy = data.charts.yoyGrowth || [];
                const yoyRows = yoy.flatMap(y => y.data || []);
                if (!empty('prYoy', yoyRows)) bar('prYoy', (yoy[0]?.data || []).map(r => r.label), yoy.map((series, i) => ({ data: (series.data || []).map(r => r.value), backgroundColor: ['#bbf7d0','#16a34a','#14532d'][i] || colors[i], borderRadius: 3, barThickness: 8 })));
                const region = data.charts.regionWiseSales || [];
                if (!empty('prRegion', region, ['achievement', 'target'])) bar('prRegion', region.map(r => r.label), [
                    { data: region.map(r => r.achievement), backgroundColor: '#16a34a', borderRadius: 4, barThickness: 16 },
                    { data: region.map(r => r.target), backgroundColor: '#e2e8f0', borderRadius: 4, barThickness: 16 }
                ]);
            };
            const renderEmployees = (data) => {
                updateBadges(data.periodLabel);
                const k = data.kpis || {};
                const s = data.summary || {};
                document.querySelector('[data-emp-kpi="totalEmployees"]').textContent = compact(k.totalEmployees);
                document.querySelector('[data-emp-kpi="todayOnMarket"]').textContent = compact(k.todayOnMarket);
                document.querySelector('[data-emp-kpi="todayOnLeave"]').textContent = compact(k.todayOnLeave);
                document.querySelector('[data-emp-kpi="misPunch"]').textContent = compact(k.misPunch);
                document.querySelector('[data-emp-summary="totalOrderQty"]').textContent = compact(s.totalOrderQty);
                document.querySelector('[data-emp-summary="totalOrderValue"]').textContent = money(s.totalOrderValue);
                document.querySelector('[data-emp-summary="uniqueActiveCustomers"]').textContent = compact(s.uniqueActiveCustomers);

                const rows = data.charts.employeeTargetVsAchievement || [];
                if (!empty('empTarget', rows, ['achievement', 'target'])) {
                    const inner = document.querySelector('.ef-emp-chart-inner');
                    if (inner) inner.style.minWidth = Math.max(1400, rows.length * 42 + 180) + 'px';
                    const bg = rows.map(row => {
                        const pct = Number(row.achievementPercent || 0);
                        if (pct >= 90) return '#16a34a';
                        if (pct >= 75) return '#3b82f6';
                        if (pct >= 60) return '#d97706';
                        return '#dc2626';
                    });
                    bar('empTarget', rows.map(r => r.label), [
                        { label: 'Achievement', data: rows.map(r => lakh(r.achievement)), backgroundColor: bg, borderRadius: 4, barThickness: 16 },
                        { label: 'Target', data: rows.map(r => lakh(r.target)), backgroundColor: '#e2e8f0', borderColor: '#cbd5e1', borderWidth: 1, borderRadius: 4, barThickness: 16 }
                    ], {
                        options: {
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        title: items => {
                                            const row = rows[items[0]?.dataIndex] || {};
                                            return row.name ? `${row.name} - ${row.label}` : row.label;
                                        },
                                        label: ctx => `${ctx.dataset.label}: ${scaledMoney(ctx.parsed.y, 'L')}`
                                    }
                                }
                            },
                            scales: {
                                x: { ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8', autoSkip: false, maxRotation: 45, minRotation: 45 }, grid: { display: false }, border: { display: false } },
                                y: { ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8', callback: value => scaledMoney(value, 'L') }, grid: { color: 'rgba(0,0,0,.04)' }, border: { display: false } }
                            }
                        }
                    });
                }
            };
            const load = async (force = false) => {
                setStatus('Loading dashboard data...');
                try {
                    if ((state.activeTab === 'secondary' || force) && (!state.loaded.secondary || force)) {
                        renderSecondary(await fetchJson(urls.secondary));
                        state.loaded.secondary = true;
                    }
                    if ((state.activeTab === 'product' || force) && (!state.loaded.product || force)) {
                        renderProduct(await fetchJson(urls.product));
                        state.loaded.product = true;
                    }
                    if ((state.activeTab === 'primary' || force) && (!state.loaded.primary || force)) {
                        renderPrimary(await fetchJson(urls.primary));
                        state.loaded.primary = true;
                    }
                    if ((state.activeTab === 'employees' || force) && (!state.loaded.employees || force)) {
                        renderEmployees(await fetchJson(urls.employees));
                        state.loaded.employees = true;
                    }
                    setStatus('');
                } catch (e) {
                    setStatus(e.message || 'Unable to load dashboard data.', true);
                }
            };

            document.querySelectorAll('.ef-btn[data-filter]').forEach(btn => btn.addEventListener('click', () => {
                document.querySelectorAll('.ef-btn[data-filter]').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                state.filterType = btn.dataset.filter;
                document.getElementById('efCustomDates').style.display = state.filterType === 'CUSTOM' ? 'flex' : 'none';
                if (state.filterType !== 'CUSTOM') {
                    resetLoaded();
                    load();
                }
            }));
            document.getElementById('efApply').addEventListener('click', () => {
                state.fromDate = document.getElementById('efFrom').value;
                state.toDate = document.getElementById('efTo').value;
                resetLoaded();
                load();
            });
            document.getElementById('efZone').addEventListener('change', () => {
                state.zoneId = document.getElementById('efZone').value;
                resetLoaded();
                load();
            });
            document.querySelectorAll('.ef-tab').forEach(tab => tab.addEventListener('click', () => {
                document.querySelectorAll('.ef-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.ef-panel').forEach(panel => panel.classList.remove('active'));
                tab.classList.add('active');
                state.activeTab = tab.dataset.tab;
                document.getElementById('ef-' + state.activeTab).classList.add('active');
                if (state.activeTab === 'secondary' || state.activeTab === 'product' || state.activeTab === 'primary' || state.activeTab === 'employees') load();
            }));
            load();
        })();
    </script>
</x-app-layout>
