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

        /* ===== Secondary Sales redesign ===== */
        .ef-dashboard { --brand: #2f4b8c; --brand2: #3b82f6; --shadow: 0 1px 2px rgba(15,30,53,.04), 0 4px 14px rgba(15,30,53,.05); --shadow-h: 0 10px 28px rgba(47,75,140,.14); }
        @keyframes efUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        @keyframes efGrow { from { width: 0; } }
        @keyframes efShimmer { 0% { background-position: -400px 0; } 100% { background-position: 400px 0; } }
        @keyframes efPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(22,163,74,.35); } 50% { box-shadow: 0 0 0 6px rgba(22,163,74,0); } }
        .ef-panel.active .ef-reveal { animation: efUp .55s cubic-bezier(.2,.7,.2,1) both; animation-delay: var(--d, 0s); }
        .ef-card { border-radius: 14px; box-shadow: var(--shadow); transition: box-shadow .25s, transform .25s, border-color .25s; }
        .ef-card:hover { box-shadow: var(--shadow-h); transform: translateY(-2px); border-color: #c7d6f5; }
        .ef-tab { transition: background .2s, color .2s; }
        .ef-tab:not(.active):hover { background: var(--surface3); color: var(--txt1); }

        .ef-hero { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; padding: 16px 20px; border-radius: 14px; color: #fff; background: linear-gradient(120deg, #1e3366 0%, var(--brand) 45%, #3b6fd8 100%); position: relative; overflow: hidden; }
        .ef-hero::after { content: ''; position: absolute; right: -60px; top: -80px; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,.18), transparent 65%); }
        .ef-hero h2 { margin: 0; font-size: 17px; font-weight: 700; color: #fff; }
        .ef-hero p { margin: 3px 0 0; font-size: 11px; color: #c7d6f5; font-weight: 500; }
        .ef-hero-stat { display: flex; gap: 18px; position: relative; z-index: 1; }
        .ef-hero-stat div { text-align: right; }
        .ef-hero-stat b { display: block; font-size: 16px; font-weight: 700; }
        .ef-hero-stat span { font-size: 9px; text-transform: uppercase; letter-spacing: .08em; color: #c7d6f5; font-weight: 700; }

        .ef-kcard { padding: 16px 18px; }
        .ef-kcard::before { display: none; }
        .ef-kcard::after { content: ''; position: absolute; right: -30px; bottom: -30px; width: 110px; height: 110px; border-radius: 50%; background: var(--tone-bg, #eff6ff); opacity: .7; transition: transform .4s; }
        .ef-kcard:hover::after { transform: scale(1.25); }
        .ef-kcard > * { position: relative; z-index: 1; }
        .ef-kcard { --tone: #2563eb; --tone-bg: #eff6ff; }
        .ef-kcard.green { --tone: #16a34a; --tone-bg: #f0fdf4; }
        .ef-kcard.amber { --tone: #d97706; --tone-bg: #fffbeb; }
        .ef-kcard.red, .ef-kcard.purple { --tone: #7c3aed; --tone-bg: #f5f3ff; }
        .ef-khead { display: flex; align-items: center; gap: 9px; margin-bottom: 12px; }
        .ef-kicon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: var(--tone); background: var(--tone-bg); flex-shrink: 0; }
        .ef-khead .ef-klabel { margin: 0; }
        .ef-kval { font-size: 28px; font-variant-numeric: tabular-nums; }
        .ef-trend { color: var(--tone); }
        .ef-target-box { background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 7px 9px; }
        .ef-target-val { font-size: 15px; }
        .ef-ach { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .ef-ring { width: 74px; height: 74px; flex-shrink: 0; position: relative; }
        .ef-ring svg { transform: rotate(-90deg); }
        .ef-ring circle { fill: none; stroke-width: 7; }
        .ef-ring .bg { stroke: var(--surface3); }
        .ef-ring .fg { stroke: var(--tone); stroke-linecap: round; transition: stroke-dashoffset 1.2s cubic-bezier(.2,.7,.2,1), stroke .3s; }
        .ef-ring i { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--tone); }
        .ef-pill { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 20px; margin-top: 8px; }
        .ef-pill.ok { background: #f0fdf4; color: #15803d; }
        .ef-pill.warn { background: #fffbeb; color: #b45309; }
        .ef-pill.ok i { animation: efPulse 2s infinite; border-radius: 50%; }

        .ef-ccard { --tone: #2563eb; }
        .ef-ctotal { font-variant-numeric: tabular-nums; }
        .ef-fill { animation: efGrow 1.1s cubic-bezier(.2,.7,.2,1) both; animation-delay: .25s; }
        .ef-track { height: 6px; border-radius: 6px; }
        .ef-fill { height: 6px; border-radius: 6px; }

        .ef-chart { padding: 16px 18px; }
        .ef-chtitle { font-size: 13px; margin-bottom: 12px; }
        .ef-chtitle small { display: block; font-size: 10px; font-weight: 500; color: var(--txt3); margin-top: 2px; }
        .ef-chicon { width: 28px; height: 28px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; background: #eff6ff; color: #2563eb; font-size: 15px; margin-right: 8px; flex-shrink: 0; }
        .ef-chtitle > .ef-tleft { display: flex; align-items: center; }
        .ef-stack { display: grid; gap: 12px; grid-template-rows: auto auto; }
        .ef-g2 > .ef-card, .ef-stack > .ef-card { min-width: 0; }

        .ef-lb { display: flex; flex-direction: column; gap: 4px; }
        .ef-lbrow { display: flex; align-items: center; gap: 11px; padding: 8px 10px; border-radius: 10px; transition: background .2s; animation: efUp .45s cubic-bezier(.2,.7,.2,1) both; animation-delay: calc(var(--i) * 55ms + .15s); }
        .ef-lbrow:hover { background: var(--surface2); }
        .ef-rank { width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--txt2); background: var(--surface3); flex-shrink: 0; }
        .ef-rank.r1 { background: linear-gradient(135deg,#fde68a,#f59e0b); color: #78350f; }
        .ef-rank.r2 { background: linear-gradient(135deg,#e2e8f0,#94a3b8); color: #1e293b; }
        .ef-rank.r3 { background: linear-gradient(135deg,#fed7aa,#ea580c); color: #fff; }
        .ef-lbmain { flex: 1; min-width: 0; }
        .ef-lbhead { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; }
        .ef-lbname { font-size: 12px; font-weight: 600; color: var(--txt1); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ef-lbval { font-size: 12px; font-weight: 700; color: var(--brand); white-space: nowrap; font-variant-numeric: tabular-nums; }
        .ef-lbmeta { display: flex; align-items: center; gap: 6px; margin: 3px 0 5px; flex-wrap: wrap; font-size: 10px; color: var(--txt3); font-weight: 500; }
        .ef-st { display: inline-flex; align-items: center; gap: 3px; padding: 1px 7px 1px 5px; border-radius: 20px; font-size: 10px; font-weight: 600; background: var(--st-bg, #eff6ff); color: var(--st-fg, #1d4ed8); }
        .ef-st i { font-size: 11px; }
        .ef-lbshare { margin-left: auto; font-weight: 700; color: var(--txt2); }
        .ef-lbtrack { height: 5px; border-radius: 5px; background: var(--surface3); overflow: hidden; }
        .ef-lbfill { height: 100%; width: var(--w); border-radius: 5px; background: linear-gradient(90deg, #93c5fd, #2563eb); animation: efGrow 1s cubic-bezier(.2,.7,.2,1) both; animation-delay: calc(var(--i) * 55ms + .3s); }
        .ef-stchips { display: flex; gap: 6px; flex-wrap: wrap; margin: -4px 0 10px; }
        .ef-stchips .ef-st { padding: 3px 9px 3px 7px; }
        .ef-stchips .ef-st b { font-weight: 700; margin-left: 2px; }

        .ef-dashboard.is-loading .ef-panel.active .ef-kval,
        .ef-dashboard.is-loading .ef-panel.active .ef-target-val { color: transparent; border-radius: 6px; background: linear-gradient(90deg, var(--surface3) 0, #e8eef7 40%, var(--surface3) 80%); background-size: 800px 100%; animation: efShimmer 1.2s linear infinite; }
        .ef-loadbar { position: fixed; left: 0; top: 0; height: 3px; width: 0; z-index: 9999; background: linear-gradient(90deg, #60a5fa, var(--brand)); transition: width .4s, opacity .4s; opacity: 0; }
        .ef-dashboard.is-loading .ef-loadbar { width: 70%; opacity: 1; transition: width 2.5s cubic-bezier(.1,.6,.2,1); }

        @media (max-width: 640px) { .ef-hero-stat { width: 100%; justify-content: space-between; } .ef-hero-stat div { text-align: left; } }
        @media (prefers-reduced-motion: reduce) { .ef-dashboard *, .ef-dashboard *::before, .ef-dashboard *::after { animation: none !important; transition: none !important; } }
    </style>

    <div class="ef-dashboard" data-secondary-url="{{ route('dashboard.secondary-sales') }}" data-product-url="{{ route('dashboard.products') }}" data-primary-url="{{ route('dashboard.primary-sales') }}" data-employees-url="{{ route('dashboard.employees') }}">
        <div class="ef-loadbar"></div>
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
                <div class="ef-hero ef-reveal">
                    <div style="position:relative;z-index:1;">
                        <h2>Secondary Sales Overview</h2>
                        <p>Retailer, mechanic, garage &amp; workshop performance for <span data-badge>-</span></p>
                    </div>
                    <div class="ef-hero-stat">
                        <div><b data-hero="sales">-</b><span>Sales</span></div>
                        <div><b data-hero="orders">-</b><span>Top 10 orders</span></div>
                        <div><b data-hero="states">-</b><span>States (Top 10)</span></div>
                    </div>
                </div>

                <div class="ef-slabel">Key Metrics</div>
                <div class="ef-kgrid">
                    <div class="ef-card ef-kcard ef-reveal" style="--d:.05s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-users"></i></span><span class="ef-klabel">Total Registrations</span></div>
                        <div class="ef-kval" data-kpi="totalRegistrations">-</div>
                        <div class="ef-ksub">All secondary customer types</div>
                        <div class="ef-trend"><i class="ti ti-database"></i>Total registered base</div>
                    </div>
                    <div class="ef-card ef-kcard green ef-reveal" style="--d:.12s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-user-check"></i></span><span class="ef-klabel">Active Customers</span></div>
                        <div class="ef-kval" data-kpi="activeCustomers">-</div>
                        <div class="ef-ksub">At least 1 order in last 12 months</div>
                        <div class="ef-trend"><i class="ti ti-activity"></i><span data-kpi="activeShare">Rolling activity rule</span></div>
                    </div>
                    <div class="ef-card ef-kcard amber ef-reveal" style="--d:.19s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-coin-rupee"></i></span><span class="ef-klabel">Secondary Sales</span></div>
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
                        <div data-kpi="targetGap"></div>
                    </div>
                    <div class="ef-card ef-kcard purple ef-reveal" style="--d:.26s" data-kpi="achCard">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-target-arrow"></i></span><span class="ef-klabel">Overall Achievement</span></div>
                        <div class="ef-ach">
                            <div>
                                <div class="ef-kval" data-kpi="overallAchievementPercent">-</div>
                                <div class="ef-ksub" data-kpi="targetSub">vs target</div>
                            </div>
                            <div class="ef-ring">
                                <svg width="74" height="74" viewBox="0 0 74 74"><circle class="bg" cx="37" cy="37" r="31"/><circle class="fg" data-kpi="ring" cx="37" cy="37" r="31" stroke-dasharray="194.8" stroke-dashoffset="194.8"/></svg>
                                <i class="ti ti-trophy"></i>
                            </div>
                        </div>
                        <div class="ef-trend"><i class="ti ti-refresh"></i>Target recalculates by period</div>
                    </div>
                </div>

                <div class="ef-slabel">Customer Registration Breakdown</div>
                <div class="ef-cgrid" id="efBreakdown"></div>
                <div class="ef-note"><i class="ti ti-info-circle"></i> Registration totals are lifetime totals; active status is rolling 12 months because the business rule is activity-based.</div>

                <div class="ef-slabel">Performance Charts</div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#fef3c7;color:#d97706;"><i class="ti ti-crown"></i></span><span>Top 10 customers<small>Ranked by sales order value, with state</small></span></span><span class="ef-badge" data-badge></span></div>
                        <div class="ef-stchips" id="sTopStates"></div>
                        <div class="ef-empty" data-empty="sTop">No customer sales found for this period</div>
                        <div class="ef-lb" id="sTop"></div>
                    </div>
                    <div class="ef-stack">
                        <div class="ef-card ef-chart ef-reveal" style="--d:.12s">
                            <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon"><i class="ti ti-target"></i></span><span>Sales vs target<small>Month-wise achievement against target</small></span></span><span class="ef-badge" data-badge></span></div>
                            <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(#3b82f6,#1d4ed8);"></span>Achievement</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                            <div class="ef-empty" data-empty="sTarget">No sales or target found for this period</div>
                            <div class="ef-canvas"><canvas id="sTarget"></canvas></div>
                        </div>
                        <div class="ef-card ef-chart ef-reveal" style="--d:.19s">
                            <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#ecfeff;color:#0891b2;"><i class="ti ti-chart-line"></i></span><span>Total sales trend<small>Secondary order value over the period</small></span></span><span class="ef-badge" data-badge></span></div>
                            <div class="ef-empty" data-empty="sTrend">No sales trend found for this period</div>
                            <div class="ef-canvas"><canvas id="sTrend"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti ti-calendar-stats"></i></span><span>Year-on-year growth<small>Monthly sales across financial years</small></span></span><span class="ef-badge purple">FY comparison</span></div>
                        <div class="ef-legend" id="sYoyLegend"></div>
                        <div class="ef-empty" data-empty="sYoy">No year-on-year sales found</div>
                        <div class="ef-canvas tall"><canvas id="sYoy"></canvas></div>
                    </div>
                    <div class="ef-card ef-chart ef-reveal" style="--d:.12s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#f0fdf4;color:#16a34a;"><i class="ti ti-map-2"></i></span><span>Zone-wise sales<small>Sorted by achievement, % of target shown</small></span></span><span class="ef-badge" data-badge></span></div>
                        <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(90deg,#86efac,#16a34a);"></span>Achievement</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                        <div class="ef-empty" data-empty="sRegion">No zone-wise sales found for this period</div>
                        <div class="ef-canvas tall"><canvas id="sRegion"></canvas></div>
                    </div>
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
                root.classList.toggle('is-loading', !!msg && !isError);
                loading.style.display = 'none';
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
            // ---- Chart.js global look & feel ----
            Chart.defaults.font.family = 'Inter, sans-serif';
            Chart.defaults.color = '#94a3b8';
            Object.assign(Chart.defaults.plugins.tooltip, {
                backgroundColor: 'rgba(15,30,53,.94)', titleColor: '#fff', bodyColor: '#cbd5e1',
                titleFont: { size: 12, weight: '600' }, bodyFont: { size: 11 }, padding: 11, cornerRadius: 10,
                caretSize: 6, boxPadding: 4, usePointStyle: true, borderColor: 'rgba(147,197,253,.25)', borderWidth: 1
            });
            const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const staggered = reduceMotion ? false : {
                duration: 900, easing: 'easeOutQuart',
                delay: ctx => (ctx.type === 'data' && ctx.mode === 'default') ? ctx.dataIndex * 55 + ctx.datasetIndex * 140 : 0
            };
            const grad = (from, to, horizontal = false) => (ctx) => {
                const { chart } = ctx;
                const area = chart.chartArea;
                if (!area) return to;
                const g = horizontal
                    ? chart.ctx.createLinearGradient(area.left, 0, area.right, 0)
                    : chart.ctx.createLinearGradient(0, area.bottom, 0, area.top);
                g.addColorStop(0, from);
                g.addColorStop(1, to);
                return g;
            };
            const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
            const countUp = (el, to, fmt, duration = 1000) => {
                if (!el) return;
                to = Number(to || 0);
                if (reduceMotion) { el.textContent = fmt(to); return; }
                const start = performance.now();
                const step = (now) => {
                    const p = Math.min(1, (now - start) / duration);
                    el.textContent = fmt(to * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            };
            // Draws a small label at the end of each bar of dataset 0
            const endLabels = (getText) => ({
                id: 'efEndLabels',
                afterDatasetsDraw(chart) {
                    const meta = chart.getDatasetMeta(0);
                    if (!meta || meta.hidden) return;
                    const { ctx } = chart;
                    const horizontal = chart.options.indexAxis === 'y';
                    ctx.save();
                    ctx.font = '600 10px Inter, sans-serif';
                    ctx.fillStyle = '#475569';
                    ctx.textBaseline = 'middle';
                    meta.data.forEach((el, i) => {
                        const text = getText(i);
                        if (!text) return;
                        const p = el.getProps(['x', 'y', 'base'], true);
                        if (horizontal) {
                            const ds1 = chart.getDatasetMeta(1)?.data?.[i];
                            const x = Math.max(p.x, ds1 ? ds1.getProps(['x'], true).x : 0);
                            ctx.textAlign = 'left';
                            ctx.fillText(text, x + 6, p.y);
                        } else {
                            ctx.textAlign = 'center';
                            ctx.fillText(text, p.x, p.y - 9);
                        }
                    });
                    ctx.restore();
                }
            });
            const withPad = (cfg, padding) => { cfg.options.layout = { padding }; return cfg; };
            const stateTones = [['#eff6ff','#1d4ed8'],['#f0fdf4','#15803d'],['#faf5ff','#6d28d9'],['#fff7ed','#c2410c'],['#ecfeff','#0e7490'],['#fdf2f8','#be185d'],['#fefce8','#a16207'],['#f1f5f9','#334155']];

            const baseOptions = (indexAxis = 'x') => ({
                responsive: true,
                maintainAspectRatio: false,
                animation: staggered,
                interaction: { mode: 'index', intersect: false },
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
                charts[id] = new Chart(document.getElementById(id), { type: 'bar', data: { labels, datasets }, options, plugins: opts.plugins || [] });
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
                    Retailers: ['ti-building-store', '#eff6ff', '#2563eb', '#93c5fd'],
                    Mechanics: ['ti-tool', '#faf5ff', '#7c3aed', '#c4b5fd'],
                    Garages: ['ti-car', '#fff7ed', '#ea580c', '#fdba74'],
                    Workshops: ['ti-hammer', '#f0fdf4', '#16a34a', '#86efac']
                };
                const wrap = document.getElementById('efBreakdown');
                wrap.innerHTML = rows.map((row, i) => {
                    const m = meta[row.type] || ['ti-user', '#eff6ff', '#2563eb', '#93c5fd'];
                    return `<div class="ef-card ef-ccard ef-reveal" style="--d:${(i * .07 + .05).toFixed(2)}s">
                        <div class="ef-ctop"><div class="ef-cicon" style="background:${m[1]};color:${m[2]};"><i class="ti ${m[0]}"></i></div><span class="ef-ctag" style="background:${m[1]};color:${m[2]};">${esc(row.type)}</span></div>
                        <div class="ef-ctotal" data-count="${Number(row.total || 0)}">0</div><div class="ef-clbl">Total Registered</div>
                        <div class="ef-csplit"><div class="ef-csb active"><div class="ef-csnum" data-count="${Number(row.active || 0)}">0</div><div class="ef-cslbl">Active</div></div><div class="ef-csb inactive"><div class="ef-csnum" data-count="${Number(row.inactive || 0)}">0</div><div class="ef-cslbl">Inactive</div></div></div>
                        <div class="ef-cbar-wrap"><div class="ef-track"><div class="ef-fill" style="width:${Math.max(Number(row.activePercent || 0), 1)}%;background:linear-gradient(90deg,${m[3]},${m[2]});"></div></div><div class="ef-cpct">${esc(row.activePercent)}% active</div></div>
                    </div>`;
                }).join('');
                wrap.querySelectorAll('[data-count]').forEach(el => countUp(el, el.dataset.count, compact));
            };
            const renderTopCustomers = (top) => {
                const list = document.getElementById('sTop');
                const chips = document.getElementById('sTopStates');
                const show = hasValues(top);
                setVisible(document.querySelector('[data-empty="sTop"]'), !show);
                list.style.display = show ? 'flex' : 'none';
                chips.style.display = show ? 'flex' : 'none';
                if (!show) { list.innerHTML = ''; chips.innerHTML = ''; return { orders: 0, states: 0 }; }

                const max = Math.max(...top.map(r => Number(r.value || 0)), 1);
                const total = top.reduce((s, r) => s + Number(r.value || 0), 0);
                // Group by state for colour consistency + summary chips
                const byState = {};
                top.forEach(r => {
                    const st = r.state && r.state !== '-' ? r.state : 'Unknown';
                    byState[st] = byState[st] || { count: 0, value: 0 };
                    byState[st].count++;
                    byState[st].value += Number(r.value || 0);
                });
                const stateOrder = Object.keys(byState).sort((a, b) => byState[b].value - byState[a].value);
                const tone = (st) => stateTones[stateOrder.indexOf(st) % stateTones.length];

                chips.innerHTML = stateOrder.map(st => {
                    const t = tone(st);
                    return `<span class="ef-st" style="--st-bg:${t[0]};--st-fg:${t[1]};" title="${esc(money(byState[st].value))}"><i class="ti ti-map-pin"></i>${esc(st)}<b>${byState[st].count}</b></span>`;
                }).join('');

                list.innerHTML = top.map((r, i) => {
                    const st = r.state && r.state !== '-' ? r.state : 'Unknown';
                    const t = tone(st);
                    const value = Number(r.value || 0);
                    const share = total ? (value / total * 100).toFixed(1) : '0.0';
                    const orders = Number(r.orders || 0);
                    return `<div class="ef-lbrow" style="--i:${i};" title="${esc(r.label)} - ${esc(st)}${r.city ? ', ' + esc(r.city) : ''}">
                        <span class="ef-rank ${i < 3 ? 'r' + (i + 1) : ''}">${i + 1}</span>
                        <div class="ef-lbmain">
                            <div class="ef-lbhead"><span class="ef-lbname">${esc(r.label)}</span><span class="ef-lbval">${esc(scaledMoney(lakh(value), 'L'))}</span></div>
                            <div class="ef-lbmeta">
                                <span class="ef-st" style="--st-bg:${t[0]};--st-fg:${t[1]};"><i class="ti ti-map-pin"></i>${esc(st)}</span>
                                ${r.city ? `<span>${esc(r.city)}</span>` : ''}
                                ${orders ? `<span>&middot; ${compact(orders)} order${orders > 1 ? 's' : ''}</span>` : ''}
                                <span class="ef-lbshare">${share}%</span>
                            </div>
                            <div class="ef-lbtrack"><div class="ef-lbfill" style="--w:${Math.max(value / max * 100, 2).toFixed(1)}%;"></div></div>
                        </div>
                    </div>`;
                }).join('');
                return { orders: top.reduce((s, r) => s + Number(r.orders || 0), 0), states: stateOrder.filter(s => s !== 'Unknown').length };
            };
            const renderSecondary = (data) => {
                updateBadges(data.periodLabel);
                const k = data.kpis;
                const pct = Number(k.overallAchievementPercent || 0);
                const pctFmt = v => Number(v).toFixed(1).replace(/\.0$/, '') + '%';
                countUp(document.querySelector('[data-kpi="totalRegistrations"]'), k.totalRegistrations, compact);
                countUp(document.querySelector('[data-kpi="activeCustomers"]'), k.activeCustomers, compact);
                countUp(document.querySelector('[data-kpi="secondarySalesAchievement"]'), k.secondarySalesAchievement, money);
                countUp(document.querySelector('[data-kpi="secondarySalesTarget"]'), k.secondarySalesTarget, money);
                countUp(document.querySelector('[data-kpi="overallAchievementPercent"]'), pct, pctFmt, 1200);
                document.querySelector('[data-kpi="targetSub"]').textContent = 'vs ' + money(k.secondarySalesTarget) + ' target';
                const activeShare = Number(k.totalRegistrations) ? (Number(k.activeCustomers) / Number(k.totalRegistrations) * 100).toFixed(1) : 0;
                document.querySelector('[data-kpi="activeShare"]').textContent = activeShare + '% of registered base';

                const target = Number(k.secondarySalesTarget || 0);
                const ach = Number(k.secondarySalesAchievement || 0);
                document.querySelector('[data-kpi="targetGap"]').innerHTML = !target
                    ? `<span class="ef-pill warn"><i class="ti ti-alert-circle"></i>No target set for this period</span>`
                    : (ach >= target
                        ? `<span class="ef-pill ok"><i class="ti ti-circle-check"></i>Target exceeded by ${esc(money(ach - target))}</span>`
                        : `<span class="ef-pill warn"><i class="ti ti-trending-down"></i>${esc(money(target - ach))} short &middot; ${(100 - pct).toFixed(1)}% gap</span>`);

                // Achievement ring
                const card = document.querySelector('[data-kpi="achCard"]');
                const tone = pct >= 100 ? 'green' : pct >= 75 ? '' : pct >= 50 ? 'amber' : 'purple';
                card.classList.remove('green', 'amber', 'purple');
                if (tone) card.classList.add(tone);
                const ring = document.querySelector('[data-kpi="ring"]');
                const circ = 2 * Math.PI * 31;
                ring.style.strokeDashoffset = circ;
                requestAnimationFrame(() => requestAnimationFrame(() => { ring.style.strokeDashoffset = circ * (1 - Math.min(pct, 100) / 100); }));

                renderBreakdown(data.customerBreakdown || []);

                const top = data.charts.topCustomers || [];
                const topInfo = renderTopCustomers(top);
                countUp(document.querySelector('[data-hero="sales"]'), ach, money);
                countUp(document.querySelector('[data-hero="orders"]'), topInfo.orders, compact);
                countUp(document.querySelector('[data-hero="states"]'), topInfo.states, compact);

                const targetRows = data.charts.salesVsTarget || [];
                if (!empty('sTarget', targetRows, ['achievement', 'target'])) bar('sTarget', targetRows.map(r => r.label), [
                    { label: 'Achievement', data: targetRows.map(r => lakh(r.achievement)), backgroundColor: grad('#60a5fa', '#1d4ed8'), borderRadius: 6, borderSkipped: false, maxBarThickness: 26, categoryPercentage: .6, barPercentage: .85 },
                    { label: 'Target', data: targetRows.map(r => lakh(r.target)), backgroundColor: '#e2e8f0', hoverBackgroundColor: '#cbd5e1', borderRadius: 6, borderSkipped: false, maxBarThickness: 26, categoryPercentage: .6, barPercentage: .85 }
                ], Object.assign(withPad(scaledChartOptions('L', 'x'), { top: 16 }), {
                    plugins: [endLabels(i => {
                        const r = targetRows[i];
                        return r && Number(r.target) > 0 ? Math.round(r.achievement / r.target * 100) + '%' : '';
                    })]
                }));

                const trend = data.charts.salesTrend || [];
                if (!empty('sTrend', trend)) {
                    if (trend.length < 3) {
                        // Too few points for a line - show as bars instead
                        bar('sTrend', trend.map(r => r.label), [{ label: 'Sales', data: trend.map(r => crore(r.value)), backgroundColor: grad('#67e8f9', '#0891b2'), borderRadius: 8, borderSkipped: false, maxBarThickness: 46 }], scaledChartOptions('Cr', 'x'));
                    } else {
                        line('sTrend', trend.map(r => r.label), [{
                            label: 'Sales', data: trend.map(r => crore(r.value)), borderColor: '#0891b2',
                            backgroundColor: (ctx) => {
                                const a = ctx.chart.chartArea; if (!a) return 'rgba(8,145,178,.1)';
                                const g = ctx.chart.ctx.createLinearGradient(0, a.top, 0, a.bottom);
                                g.addColorStop(0, 'rgba(8,145,178,.28)'); g.addColorStop(1, 'rgba(8,145,178,0)'); return g;
                            },
                            fill: true, tension: .4, pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderColor: '#0891b2', pointBorderWidth: 2, borderWidth: 2.5
                        }], scaledChartOptions('Cr', 'x'));
                    }
                }

                const yoy = data.charts.yoyGrowth || [];
                const yoyRows = yoy.flatMap(y => y.data || []);
                const yoyColors = [['#e0e7ff', '#c7d2fe'], ['#93c5fd', '#3b82f6'], ['#3b5bdb', '#1e3a8a']];
                document.getElementById('sYoyLegend').innerHTML = yoy.map((s, i) => {
                    const c = yoyColors[i] || [colors[i], colors[i]];
                    return `<span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(${c[0]},${c[1]});"></span>${esc(s.label)}</span>`;
                }).join('');
                if (!empty('sYoy', yoyRows)) bar('sYoy', (yoy[0]?.data || []).map(r => r.label), yoy.map((series, i) => {
                    const c = yoyColors[i] || [colors[i], colors[i]];
                    return { label: series.label, data: (series.data || []).map(r => crore(r.value)), backgroundColor: grad(c[0], c[1]), borderRadius: 4, borderSkipped: false, maxBarThickness: 12, categoryPercentage: .75, barPercentage: .9 };
                }), scaledChartOptions('Cr', 'x'));

                const region = (data.charts.regionWiseSales || []).slice().sort((a, b) => Number(b.achievement || 0) - Number(a.achievement || 0));
                if (!empty('sRegion', region, ['achievement', 'target'])) bar('sRegion', region.map(r => r.label), [
                    { label: 'Achievement', data: region.map(r => lakh(r.achievement)), backgroundColor: grad('#86efac', '#16a34a', true), borderRadius: 6, borderSkipped: false, maxBarThickness: 14, categoryPercentage: .75, barPercentage: .9 },
                    { label: 'Target', data: region.map(r => lakh(r.target)), backgroundColor: '#e2e8f0', hoverBackgroundColor: '#cbd5e1', borderRadius: 6, borderSkipped: false, maxBarThickness: 14, categoryPercentage: .75, barPercentage: .9 }
                ], Object.assign(withPad(scaledChartOptions('L', 'y', { font: { size: 10, weight: '600', family: 'Inter' }, color: '#475569' }), { right: 44 }), {
                    plugins: [endLabels(i => {
                        const r = region[i];
                        if (!r) return '';
                        return Number(r.target) > 0 ? Math.round(r.achievement / r.target * 100) + '%' : (Number(r.achievement) > 0 ? scaledMoney(lakh(r.achievement), 'L') : '');
                    })]
                }));
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
