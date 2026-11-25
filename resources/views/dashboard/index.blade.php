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
        .ef-hero.green { background: linear-gradient(120deg, #0b3b2a 0%, #166534 45%, #22a35a 100%); }
        .ef-hero.green p, .ef-hero.green .ef-hero-stat span { color: #bbf7d0; }
        .ef-badge.green { color: #15803d; background: #f0fdf4; }
        .ef-lb.green .ef-lbfill { background: linear-gradient(90deg, #86efac, #16a34a); }
        .ef-lb.green .ef-lbval { color: #15803d; }
        .ef-hero.purple { background: linear-gradient(120deg, #2e1065 0%, #5b21b6 45%, #8b5cf6 100%); }
        .ef-hero.purple p, .ef-hero.purple .ef-hero-stat span { color: #ddd6fe; }
        .ef-lb.purple .ef-lbfill { background: linear-gradient(90deg, #c4b5fd, #7c3aed); }
        .ef-lb.purple .ef-lbval { color: #6d28d9; }
        .ef-lb.orange .ef-lbfill { background: linear-gradient(90deg, #fed7aa, #ea580c); }
        .ef-lb.orange .ef-lbval { color: #c2410c; }
        .ef-lb.orange .ef-rank { background: #fff7ed; color: #c2410c; }
        .ef-lb.scroll { max-height: 470px; overflow-y: auto; padding-right: 4px; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .ef-lbcode { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 9.5px; color: var(--txt2); background: var(--surface3); padding: 1px 5px; border-radius: 4px; }
        .ef-kval-text { font-size: 18px !important; line-height: 1.25 !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ef-donut { position: relative; width: 190px; height: 190px; flex-shrink: 0; }
        .ef-donut canvas { width: 190px !important; height: 190px !important; }
        .ef-donut-c { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; }
        .ef-donut-c b { font-size: 16px; font-weight: 700; color: var(--txt1); }
        .ef-donut-c span { font-size: 9px; font-weight: 700; color: var(--txt3); text-transform: uppercase; letter-spacing: .08em; }
        .ef-seglist { flex: 1; width: 100%; min-width: 0; max-height: 260px; overflow-y: auto; scrollbar-width: thin; }
        .ef-segrow { padding: 6px 8px; border-radius: 8px; cursor: default; transition: background .2s; animation: efUp .4s both; animation-delay: calc(var(--i) * 45ms + .1s); }
        .ef-segrow:hover, .ef-segrow.hl { background: var(--surface2); }
        .ef-segtop { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 11px; }
        .ef-segname { display: flex; align-items: center; gap: 6px; min-width: 0; color: var(--txt2); font-weight: 600; }
        .ef-segname span:last-child { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ef-segval { font-weight: 700; color: var(--txt1); white-space: nowrap; font-variant-numeric: tabular-nums; }
        .ef-segval small { color: var(--txt3); font-weight: 600; margin-left: 4px; }
        .ef-segbar { height: 4px; border-radius: 4px; background: var(--surface3); margin-top: 4px; overflow: hidden; }
        .ef-segbar div { height: 100%; border-radius: 4px; animation: efGrow .9s cubic-bezier(.2,.7,.2,1) both; animation-delay: calc(var(--i) * 45ms + .25s); }
        .ef-legend-click .ef-legitem { cursor: pointer; padding: 2px 7px; border-radius: 20px; border: 1px solid var(--border); background: var(--surface); transition: opacity .2s, background .2s; user-select: none; }
        .ef-legend-click .ef-legitem:hover { background: var(--surface2); }
        .ef-legend-click .ef-legitem.off { opacity: .4; text-decoration: line-through; }
        .ef-kcard.red-t { --tone: #dc2626; --tone-bg: #fef2f2; }
        .ef-mini { height: 6px; border-radius: 6px; background: var(--surface3); overflow: hidden; margin-top: 10px; }
        .ef-mini > div { height: 100%; width: 0; border-radius: 6px; background: linear-gradient(90deg, color-mix(in srgb, var(--tone) 45%, #fff), var(--tone)); transition: width 1.1s cubic-bezier(.2,.7,.2,1); }
        .ef-sortbar { display: inline-flex; gap: 4px; background: var(--surface3); padding: 3px; border-radius: 8px; }
        .ef-chip { border: 0; background: transparent; font-size: 10px; font-weight: 600; color: var(--txt2); padding: 4px 10px; border-radius: 6px; cursor: pointer; transition: background .2s, color .2s; }
        .ef-chip:hover { color: var(--txt1); }
        .ef-chip.active { background: var(--surface); color: var(--brand); box-shadow: 0 1px 3px rgba(15,30,53,.12); }
        .ef-pct { font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 20px; }
        .ef-lbsub { font-size: 10px; color: var(--txt3); font-weight: 500; white-space: nowrap; font-variant-numeric: tabular-nums; }
        .ef-attn { margin-top: 12px; padding: 10px 12px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca; font-size: 11px; color: #991b1b; font-weight: 500; display: none; }
        .ef-attn b { font-weight: 700; }
        .ef-attn .names { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 6px; }
        .ef-attn .names span { background: #fff; border: 1px solid #fecaca; border-radius: 20px; padding: 1px 8px; font-size: 10px; font-weight: 600; }
        .ef-emp-summary .ef-kcard .ef-kval { font-size: 24px; }
        .ef-npd { display: inline-flex; align-items: center; gap: 3px; font-size: 9px; font-weight: 700; letter-spacing: .04em; padding: 2px 7px; border-radius: 20px; color: #fff; background: linear-gradient(90deg, #db2777, #7c3aed); text-transform: uppercase; }
        .ef-slabel .ef-npd { text-transform: none; letter-spacing: 0; font-weight: 600; }
        .ef-npdgrid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 12px; }
        .ef-npdtile { --tone: #db2777; --tone-bg: #fdf2f8; padding: 14px 16px; display: flex; align-items: center; gap: 12px; position: relative; overflow: hidden; }
        .ef-npdtile::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: linear-gradient(#db2777, #7c3aed); }
        .ef-npdtile:nth-child(2) { --tone: #0891b2; --tone-bg: #ecfeff; }
        .ef-npdtile:nth-child(2)::before { background: linear-gradient(#06b6d4, #0e7490); }
        .ef-npdtile:nth-child(3) { --tone: #7c3aed; --tone-bg: #f5f3ff; }
        .ef-npdtile:nth-child(3)::before { background: linear-gradient(#a78bfa, #6d28d9); }
        .ef-npdval { font-size: 20px; font-weight: 700; color: var(--txt1); line-height: 1.1; font-variant-numeric: tabular-nums; }
        .ef-npdlbl { font-size: 10px; font-weight: 700; color: var(--txt3); text-transform: uppercase; margin-top: 3px; }
        .ef-npdshare { margin-left: auto; text-align: right; min-width: 92px; }
        .ef-npdshare b { display: block; font-size: 15px; color: var(--tone); font-weight: 700; }
        .ef-npdshare span { font-size: 9.5px; color: var(--txt3); font-weight: 600; }
        .ef-npdshare .ef-mini { margin-top: 5px; }
        @media (max-width: 1000px) { .ef-npdgrid { grid-template-columns: 1fr; } }
        .ef-visit-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-bottom: 12px; }
        .ef-vstat { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); position: relative; }
        .ef-vstat b { display: block; font-size: 20px; font-weight: 700; color: var(--txt1); line-height: 1.1; font-variant-numeric: tabular-nums; }
        .ef-vstat span:not(.ef-kicon) { font-size: 10px; font-weight: 700; color: var(--txt3); text-transform: uppercase; }
        .ef-vstat em { margin-left: auto; font-style: normal; font-size: 10px; font-weight: 700; color: var(--tone); background: var(--tone-bg); padding: 3px 8px; border-radius: 20px; white-space: nowrap; }
        .ef-vstat em:empty { display: none; }
        @media (max-width: 900px) { .ef-visit-stats { grid-template-columns: 1fr; } }
        .ef-evctl { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; padding: 8px 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px; }
        .ef-evctl .lbl { font-size: 10px; font-weight: 700; color: var(--txt3); text-transform: uppercase; letter-spacing: .04em; }
        .ef-evctl .grow { flex: 1; }
        .ef-evctl .ef-date.on { border-color: var(--brand); box-shadow: 0 0 0 2px rgba(79,70,229,.12); }
        .ef-evscroll { max-height: 560px; overflow-y: auto; padding-right: 4px; }
        .ef-evinner { position: relative; height: 240px; }
        #evCard { position: relative; }
        #evCard.ev-busy .ef-evscroll { opacity: .45; transition: opacity .2s; }
        .ef-evidle { margin-top: 12px; padding: 10px 12px; border-radius: 10px; background: #fffbeb; border: 1px solid #fde68a; font-size: 11px; color: #92400e; font-weight: 500; display: none; }
        .ef-evidle .names { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 6px; }
        .ef-evidle .names span { background: #fff; border: 1px solid #fde68a; border-radius: 20px; padding: 1px 8px; font-size: 10px; font-weight: 600; }
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

    <div class="ef-dashboard" data-secondary-url="{{ route('dashboard.secondary-sales') }}" data-product-url="{{ route('dashboard.products') }}" data-primary-url="{{ route('dashboard.primary-sales') }}" data-employees-url="{{ route('dashboard.employees') }}" data-emp-visits-url="{{ route('dashboard.employee-visits') }}">
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

            <div id="ef-primary" class="ef-panel ef-tone-green">
                <div class="ef-hero green ef-reveal">
                    <div style="position:relative;z-index:1;">
                        <h2>Primary Sales Overview</h2>
                        <p>Dealer invoicing &amp; branch performance for <span data-badge>-</span></p>
                    </div>
                    <div class="ef-hero-stat">
                        <div><b data-pr-hero="sales">-</b><span>Net Sales</span></div>
                        <div><b data-pr-hero="invoices">-</b><span>Invoices</span></div>
                        <div><b data-pr-hero="states">-</b><span>States (Top 10)</span></div>
                    </div>
                </div>

                <div class="ef-slabel">Key Metrics</div>
                <div class="ef-kgrid">
                    <div class="ef-card ef-kcard green ef-reveal" style="--d:.05s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-coin-rupee"></i></span><span class="ef-klabel">Primary Sales</span></div>
                        <div class="ef-target-row">
                            <div class="ef-target-box">
                                <div class="ef-target-lbl">Achievement</div>
                                <div class="ef-target-val" data-pr-kpi="achievement">-</div>
                            </div>
                            <div class="ef-target-box">
                                <div class="ef-target-lbl">Target</div>
                                <div class="ef-target-val" data-pr-kpi="target">-</div>
                            </div>
                        </div>
                        <div data-pr-kpi="targetGap"></div>
                    </div>
                    <div class="ef-card ef-kcard ef-reveal" style="--d:.12s" data-pr-kpi="achCard">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-target-arrow"></i></span><span class="ef-klabel">Overall Achievement</span></div>
                        <div class="ef-ach">
                            <div>
                                <div class="ef-kval" data-pr-kpi="percent">-</div>
                                <div class="ef-ksub" data-pr-kpi="targetSub">vs target</div>
                            </div>
                            <div class="ef-ring">
                                <svg width="74" height="74" viewBox="0 0 74 74"><circle class="bg" cx="37" cy="37" r="31"/><circle class="fg" data-pr-kpi="ring" cx="37" cy="37" r="31" stroke-dasharray="194.8" stroke-dashoffset="194.8"/></svg>
                                <i class="ti ti-trophy"></i>
                            </div>
                        </div>
                        <div class="ef-trend"><i class="ti ti-refresh"></i>Target recalculates by period</div>
                    </div>
                    <div class="ef-card ef-kcard amber ef-reveal" style="--d:.19s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-file-invoice"></i></span><span class="ef-klabel">Total Invoices</span></div>
                        <div class="ef-kval" data-pr-kpi="invoices">-</div>
                        <div class="ef-ksub">Unique invoice numbers in period</div>
                        <div class="ef-trend"><i class="ti ti-receipt"></i><span data-pr-kpi="avgInvoice">Avg invoice value</span></div>
                    </div>
                    <div class="ef-card ef-kcard purple ef-reveal" style="--d:.26s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-packages"></i></span><span class="ef-klabel">Total Quantity</span></div>
                        <div class="ef-kval" data-pr-kpi="qty">-</div>
                        <div class="ef-ksub">Units invoiced (Nos)</div>
                        <div class="ef-trend"><i class="ti ti-box"></i><span data-pr-kpi="avgQty">Per invoice</span></div>
                    </div>
                </div>

                <div class="ef-slabel">Performance Charts</div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#fef3c7;color:#d97706;"><i class="ti ti-crown"></i></span><span>Top 10 dealers<small>Ranked by net invoice value, with state</small></span></span><span class="ef-badge green" data-badge></span></div>
                        <div class="ef-stchips" id="prTopStates"></div>
                        <div class="ef-empty" data-empty="prTop">No dealer sales found for this period</div>
                        <div class="ef-lb green" id="prTop"></div>
                    </div>
                    <div class="ef-stack">
                        <div class="ef-card ef-chart ef-reveal" style="--d:.12s">
                            <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#f0fdf4;color:#16a34a;"><i class="ti ti-target"></i></span><span>Sales vs target<small>Month-wise achievement against target</small></span></span><span class="ef-badge green" data-badge></span></div>
                            <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(#4ade80,#15803d);"></span>Achievement</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                            <div class="ef-empty" data-empty="prTarget">No primary sales or target found for this period</div>
                            <div class="ef-canvas"><canvas id="prTarget"></canvas></div>
                        </div>
                        <div class="ef-card ef-chart ef-reveal" style="--d:.19s">
                            <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#ecfdf5;color:#059669;"><i class="ti ti-chart-line"></i></span><span>Total sales trend<small>Primary net value over the period</small></span></span><span class="ef-badge green" data-badge></span></div>
                            <div class="ef-empty" data-empty="prTrend">No primary sales trend found for this period</div>
                            <div class="ef-canvas"><canvas id="prTrend"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti ti-calendar-stats"></i></span><span>Year-on-year growth<small>Monthly sales across financial years</small></span></span><span class="ef-badge purple">FY comparison</span></div>
                        <div class="ef-legend" id="prYoyLegend"></div>
                        <div class="ef-empty" data-empty="prYoy">No primary year-on-year sales found</div>
                        <div class="ef-canvas tall"><canvas id="prYoy"></canvas></div>
                    </div>
                    <div class="ef-card ef-chart ef-reveal" style="--d:.12s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon"><i class="ti ti-map-2"></i></span><span>Zone-wise sales<small>Sorted by achievement, % of target shown</small></span></span><span class="ef-badge" data-badge></span></div>
                        <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(90deg,#93c5fd,#2563eb);"></span>Achievement</span><span class="ef-legitem"><span class="ef-legsq" style="background:#e2e8f0;border:1px solid #cbd5e1;"></span>Target</span></div>
                        <div class="ef-empty" data-empty="prRegion">No primary zone-wise sales found for this period</div>
                        <div class="ef-canvas tall"><canvas id="prRegion"></canvas></div>
                    </div>
                </div>
            </div>

            <div id="ef-product" class="ef-panel">
                <div class="ef-hero purple ef-reveal">
                    <div style="position:relative;z-index:1;">
                        <h2>Product Analytics</h2>
                        <p>SKU, segment &amp; movement insights from secondary orders for <span data-badge>-</span></p>
                    </div>
                    <div class="ef-hero-stat">
                        <div><b data-pd-hero="sales">-</b><span>Product Sales</span></div>
                        <div><b data-pd-hero="qty">-</b><span>Units Sold</span></div>
                        <div><b data-pd-hero="sold">-</b><span>SKUs Sold</span></div>
                    </div>
                </div>

                <div class="ef-slabel">Key Metrics</div>
                <div class="ef-kgrid">
                    <div class="ef-card ef-kcard purple ef-reveal" style="--d:.05s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-coin-rupee"></i></span><span class="ef-klabel">Product Sales</span></div>
                        <div class="ef-kval" data-pd-kpi="sales">-</div>
                        <div class="ef-ksub">Order value across all SKUs</div>
                        <div class="ef-trend"><i class="ti ti-receipt"></i><span data-pd-kpi="avgPrice">Avg value per unit</span></div>
                    </div>
                    <div class="ef-card ef-kcard ef-reveal" style="--d:.12s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-packages"></i></span><span class="ef-klabel">Units Sold</span></div>
                        <div class="ef-kval" data-pd-kpi="qty">-</div>
                        <div class="ef-ksub">Total ordered quantity (Nos)</div>
                        <div class="ef-trend"><i class="ti ti-box"></i><span data-pd-kpi="avgQty">Per SKU</span></div>
                    </div>
                    <div class="ef-card ef-kcard green ef-reveal" style="--d:.19s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-barcode"></i></span><span class="ef-klabel">SKU Coverage</span></div>
                        <div class="ef-ach">
                            <div>
                                <div class="ef-kval" data-pd-kpi="sold">-</div>
                                <div class="ef-ksub" data-pd-kpi="catalogSub">of active catalogue</div>
                            </div>
                            <div class="ef-ring">
                                <svg width="74" height="74" viewBox="0 0 74 74"><circle class="bg" cx="37" cy="37" r="31"/><circle class="fg" data-pd-kpi="ring" cx="37" cy="37" r="31" stroke-dasharray="194.8" stroke-dashoffset="194.8"/></svg>
                                <i class="ti ti-barcode"></i>
                            </div>
                        </div>
                        <div class="ef-trend"><i class="ti ti-chart-pie"></i><span data-pd-kpi="coverage">Catalogue moving</span></div>
                    </div>
                    <div class="ef-card ef-kcard amber ef-reveal" style="--d:.26s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-category"></i></span><span class="ef-klabel">Top Segment</span></div>
                        <div class="ef-kval ef-kval-text" data-pd-kpi="topSegment">-</div>
                        <div class="ef-ksub" data-pd-kpi="topSegmentSub">Share of product sales</div>
                        <div class="ef-trend"><i class="ti ti-layers-subtract"></i><span data-pd-kpi="segments">Segments</span></div>
                    </div>
                </div>

                <div class="ef-slabel">NPD Performance <span class="ef-npd" style="margin-left:2px;">New products launched in last 12 months</span></div>
                <div class="ef-npdgrid">
                    <div class="ef-card ef-npdtile ef-reveal" style="--d:.05s">
                        <span class="ef-kicon"><i class="ti ti-sparkles"></i></span>
                        <div><div class="ef-npdval" data-pd-npd="sales">-</div><div class="ef-npdlbl">NPD Sales</div></div>
                        <div class="ef-npdshare"><b data-pd-npd="salesShare">-</b><span>of product sales</span><div class="ef-mini"><div data-pd-npd="salesBar"></div></div></div>
                    </div>
                    <div class="ef-card ef-npdtile ef-reveal" style="--d:.12s">
                        <span class="ef-kicon"><i class="ti ti-packages"></i></span>
                        <div><div class="ef-npdval" data-pd-npd="qty">-</div><div class="ef-npdlbl">NPD Units Sold</div></div>
                        <div class="ef-npdshare"><b data-pd-npd="qtyShare">-</b><span>of units sold</span><div class="ef-mini"><div data-pd-npd="qtyBar"></div></div></div>
                    </div>
                    <div class="ef-card ef-npdtile ef-reveal" style="--d:.19s">
                        <span class="ef-kicon"><i class="ti ti-rocket"></i></span>
                        <div><div class="ef-npdval" data-pd-npd="skus">-</div><div class="ef-npdlbl">NPD SKUs Sold</div></div>
                        <div class="ef-npdshare"><b data-pd-npd="skuShare">-</b><span data-pd-npd="skuSub">of NPD catalogue</span><div class="ef-mini"><div data-pd-npd="skuBar"></div></div></div>
                    </div>
                </div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#fdf2f8;color:#db2777;"><i class="ti ti-trending-up"></i></span><span><span data-npd-title="top">Top 10 NPD Sale &ndash; Value wise</span><small data-npd-sub="top">Best selling new products for selected period &amp; zone</small></span></span><span class="ef-sortbar"><button type="button" class="ef-chip active" data-npd-chart="top" data-metric="value"><i class="ti ti-currency-rupee"></i> Value</button><button type="button" class="ef-chip" data-npd-chart="top" data-metric="qty"><i class="ti ti-packages"></i> Quantity</button></span></div>
                        <div class="ef-empty" data-empty="pNpd_top">No NPD sales found for this period</div>
                        <div class="ef-canvas tall"><canvas id="pNpd_top"></canvas></div>
                    </div>
                    <div class="ef-card ef-chart ef-reveal" style="--d:.12s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#fff7ed;color:#ea580c;"><i class="ti ti-trending-down"></i></span><span><span data-npd-title="bottom">Bottom 10 NPD Sale &ndash; Value wise</span><small data-npd-sub="bottom">Weakest selling new products (with at least one sale)</small></span></span><span class="ef-sortbar"><button type="button" class="ef-chip active" data-npd-chart="bottom" data-metric="value"><i class="ti ti-currency-rupee"></i> Value</button><button type="button" class="ef-chip" data-npd-chart="bottom" data-metric="qty"><i class="ti ti-packages"></i> Quantity</button></span></div>
                        <div class="ef-empty" data-empty="pNpd_bottom">No NPD sales found for this period</div>
                        <div class="ef-canvas tall"><canvas id="pNpd_bottom"></canvas></div>
                    </div>
                </div>

                <div class="ef-slabel">Product Performance</div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#f5f3ff;color:#7c3aed;"><i class="ti ti-rocket"></i></span><span>Top products<small data-pd-topsub>Top 23 by order value, with segment &amp; units</small></span></span><span class="ef-sortbar"><button type="button" class="ef-chip active" data-pd-metric="value"><i class="ti ti-currency-rupee"></i> Value</button><button type="button" class="ef-chip" data-pd-metric="qty"><i class="ti ti-packages"></i> Quantity</button></span></div>
                        <div class="ef-empty" data-empty="pTop">No product sales found for this period</div>
                        <div class="ef-lb purple scroll" id="pTop"></div>
                    </div>
                    <div class="ef-card ef-chart ef-reveal" style="--d:.12s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#fff7ed;color:#ea580c;"><i class="ti ti-hourglass-low"></i></span><span>Slow moving products<small>Lowest order value among SKUs that sold</small></span></span><span class="ef-badge orange" data-badge></span></div>
                        <div class="ef-empty" data-empty="pSlow">No slow moving product data found</div>
                        <div class="ef-lb orange scroll" id="pSlow"></div>
                    </div>
                </div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#ecfeff;color:#0891b2;"><i class="ti ti-chart-donut-3"></i></span><span>Segment-wise sales<small>Category share of order value</small></span></span><span class="ef-badge purple" data-badge></span></div>
                        <div class="ef-empty" data-empty="pSegment">No segment sales found</div>
                        <div class="ef-cat-wrap" id="pSegmentWrap">
                            <div class="ef-donut"><canvas id="pSegment"></canvas><div class="ef-donut-c"><b data-pd-kpi="segTotal">-</b><span>Total</span></div></div>
                            <div class="ef-seglist" id="pSegmentLegend"></div>
                        </div>
                    </div>
                    <div class="ef-card ef-chart ef-reveal" style="--d:.12s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#fdf2f8;color:#db2777;"><i class="ti ti-chart-line"></i></span><span>Top 10 SKU trend<small>Click a legend item to show / hide a SKU</small></span></span><span class="ef-badge purple" data-badge></span></div>
                        <div id="pSkuLegend" class="ef-legend ef-legend-click"></div>
                        <div class="ef-empty" data-empty="pSku">No SKU trend found</div>
                        <div class="ef-canvas tall"><canvas id="pSku"></canvas></div>
                    </div>
                </div>
            </div>

            <div id="ef-employees" class="ef-panel">
                <div class="ef-hero ef-reveal">
                    <div style="position:relative;z-index:1;">
                        <h2>Team Performance</h2>
                        <p>Field attendance today &amp; employee target achievement for <span data-badge>-</span></p>
                    </div>
                    <div class="ef-hero-stat">
                        <div><b data-emp-hero="market">-</b><span>On Market Today</span></div>
                        <div><b data-emp-hero="value">-</b><span>Team Order Value</span></div>
                        <div><b data-emp-hero="onTarget">-</b><span>On Target (90%+)</span></div>
                    </div>
                </div>

                <div class="ef-slabel">Today's Snapshot</div>
                <div class="ef-kgrid">
                    <div class="ef-card ef-kcard ef-reveal" style="--d:.05s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-users"></i></span><span class="ef-klabel">Total Employees</span></div>
                        <div class="ef-kval" data-emp-kpi="totalEmployees">-</div>
                        <div class="ef-ksub">All active users in selected zone</div>
                        <div class="ef-trend"><i class="ti ti-building"></i>Active strength</div>
                    </div>
                    <div class="ef-card ef-kcard green ef-reveal" style="--d:.12s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-map-pin"></i></span><span class="ef-klabel">Today on Market</span></div>
                        <div class="ef-ach">
                            <div>
                                <div class="ef-kval" data-emp-kpi="todayOnMarket">-</div>
                                <div class="ef-ksub" data-emp-kpi="marketSub">of active strength</div>
                            </div>
                            <div class="ef-ring">
                                <svg width="74" height="74" viewBox="0 0 74 74"><circle class="bg" cx="37" cy="37" r="31"/><circle class="fg" data-emp-kpi="ring" cx="37" cy="37" r="31" stroke-dasharray="194.8" stroke-dashoffset="194.8"/></svg>
                                <i class="ti ti-walk"></i>
                            </div>
                        </div>
                        <div class="ef-trend"><i class="ti ti-circle-check"></i>Punched in, field presence</div>
                    </div>
                    <div class="ef-card ef-kcard amber ef-reveal" style="--d:.19s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-calendar-off"></i></span><span class="ef-klabel">Today on Leave</span></div>
                        <div class="ef-kval" data-emp-kpi="todayOnLeave">-</div>
                        <div class="ef-ksub" data-emp-kpi="leaveSub">of active strength</div>
                        <div class="ef-mini"><div data-emp-kpi="leaveBar"></div></div>
                    </div>
                    <div class="ef-card ef-kcard red-t ef-reveal" style="--d:.26s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-clock-exclamation"></i></span><span class="ef-klabel">MIS Punch</span></div>
                        <div class="ef-kval" data-emp-kpi="misPunch">-</div>
                        <div class="ef-ksub" data-emp-kpi="misSub">Not punched in today</div>
                        <div class="ef-mini"><div data-emp-kpi="misBar"></div></div>
                    </div>
                </div>

                <div class="ef-slabel">Team Order Summary</div>
                <div class="ef-emp-summary">
                    <div class="ef-card ef-kcard ef-reveal" style="--d:.05s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-shopping-cart"></i></span><span class="ef-klabel">Total Order Qty (Nos)</span></div>
                        <div class="ef-kval" data-emp-summary="totalOrderQty">-</div>
                        <div class="ef-ksub">Secondary units booked by team</div>
                    </div>
                    <div class="ef-card ef-kcard green ef-reveal" style="--d:.12s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-coin-rupee"></i></span><span class="ef-klabel">Total Order Value</span></div>
                        <div class="ef-kval" data-emp-summary="totalOrderValue">-</div>
                        <div class="ef-ksub">Secondary + primary value</div>
                    </div>
                    <div class="ef-card ef-kcard purple ef-reveal" style="--d:.19s">
                        <div class="ef-khead"><span class="ef-kicon"><i class="ti ti-user-check"></i></span><span class="ef-klabel">Unique Active Customers</span></div>
                        <div class="ef-kval" data-emp-summary="uniqueActiveCustomers">-</div>
                        <div class="ef-ksub">Customers who ordered via team</div>
                    </div>
                </div>

                <div class="ef-slabel">Field Visits</div>
                <div class="ef-card ef-chart ef-reveal" style="--d:.05s;margin-bottom:12px;">
                    <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#eef2ff;color:#4f46e5;"><i class="ti ti-route"></i></span><span>Total Visits Done<small data-visit-sub>Check-ins at retailers, mechanics, garages &amp; workshops</small></span></span>
                        <span class="ef-sortbar" style="flex-wrap:wrap;">
                            <button type="button" class="ef-chip active" data-visit-type="ALL">All</button>
                            <button type="button" class="ef-chip" data-visit-type="RETAILER">Retailer</button>
                            <button type="button" class="ef-chip" data-visit-type="MECHANIC">Mechanic</button>
                            <button type="button" class="ef-chip" data-visit-type="GARAGE">Garage</button>
                            <button type="button" class="ef-chip" data-visit-type="WORKSHOP">Workshop</button>
                        </span>
                    </div>
                    <div class="ef-visit-stats">
                        <div class="ef-vstat" style="--tone:#4f46e5;--tone-bg:#eef2ff;"><span class="ef-kicon"><i class="ti ti-map-pin-check"></i></span><div><b data-visit="visits">-</b><span>Total Visits</span></div></div>
                        <div class="ef-vstat" style="--tone:#0891b2;--tone-bg:#ecfeff;"><span class="ef-kicon"><i class="ti ti-users"></i></span><div><b data-visit="unique">-</b><span>Unique Customers Visited</span></div><em data-visit="repeat"></em></div>
                        <div class="ef-vstat" style="--tone:#db2777;--tone-bg:#fdf2f8;"><span class="ef-kicon"><i class="ti ti-user-plus"></i></span><div><b data-visit="new">-</b><span>Unique Visits &middot; New Customers</span></div><em data-visit="newShare"></em></div>
                    </div>
                    <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(#a5b4fc,#4f46e5);"></span>Total visits</span><span class="ef-legitem"><span class="ef-legsq" style="background:#0891b2;border-radius:50%;"></span>Unique customers visited</span><span class="ef-legitem"><span class="ef-legsq" style="background:#db2777;border-radius:50%;"></span>Unique visits (new customers added)</span></div>
                    <div class="ef-empty" data-empty="empVisits">No visits found for this period</div>
                    <div class="ef-canvas tall"><canvas id="empVisits"></canvas></div>
                    <div class="ef-note" style="margin-top:8px;"><i class="ti ti-info-circle"></i> New customer = customer created in the selected period by the same employee who visited.</div>
                </div>

                <div class="ef-card ef-chart ef-reveal" id="evCard" style="--d:.08s;margin-bottom:12px;">
                    <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#ecfeff;color:#0891b2;"><i class="ti ti-users-group"></i></span><span>Employee-wise Visits<small data-ev-sub>Visits by each employee</small></span></span>
                        <span class="ef-sortbar" style="flex-wrap:wrap;">
                            <button type="button" class="ef-chip active" data-ev-type="ALL">All</button>
                            <button type="button" class="ef-chip" data-ev-type="RETAILER">Retailer</button>
                            <button type="button" class="ef-chip" data-ev-type="MECHANIC">Mechanic</button>
                            <button type="button" class="ef-chip" data-ev-type="GARAGE">Garage</button>
                            <button type="button" class="ef-chip" data-ev-type="WORKSHOP">Workshop</button>
                        </span>
                    </div>
                    <div class="ef-evctl">
                        <span class="lbl"><i class="ti ti-calendar"></i> Date</span>
                        <span class="ef-sortbar">
                            <button type="button" class="ef-chip active" data-ev-range="today">Today</button>
                            <button type="button" class="ef-chip" data-ev-range="yesterday">Yesterday</button>
                            <button type="button" class="ef-chip" data-ev-range="period">Selected period</button>
                        </span>
                        <input type="date" id="evFrom" class="ef-date" title="From date" max="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}">
                        <span style="font-size:11px;color:var(--txt3);">to</span>
                        <input type="date" id="evTo" class="ef-date" title="To date (keep same as From for a single day)" max="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}">
                        <span class="grow"></span>
                        <span class="lbl">Sort</span>
                        <span class="ef-sortbar">
                            <button type="button" class="ef-chip active" data-ev-sort="visits">Visits</button>
                            <button type="button" class="ef-chip" data-ev-sort="new">New customers</button>
                            <button type="button" class="ef-chip" data-ev-sort="name">A - Z</button>
                        </span>
                    </div>
                    <div class="ef-visit-stats">
                        <div class="ef-vstat" style="--tone:#0891b2;--tone-bg:#ecfeff;"><span class="ef-kicon"><i class="ti ti-user-check"></i></span><div><b data-ev="active">-</b><span>Employees on visits</span></div><em data-ev="activeShare"></em></div>
                        <div class="ef-vstat" style="--tone:#4f46e5;--tone-bg:#eef2ff;"><span class="ef-kicon"><i class="ti ti-map-pin-check"></i></span><div><b data-ev="visits">-</b><span>Total Visits</span></div><em data-ev="avg"></em></div>
                        <div class="ef-vstat" style="--tone:#db2777;--tone-bg:#fdf2f8;"><span class="ef-kicon"><i class="ti ti-user-plus"></i></span><div><b data-ev="new">-</b><span>New Customers Visited</span></div><em data-ev="newShare"></em></div>
                    </div>
                    <div class="ef-legend"><span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(90deg,#c7d2fe,#4f46e5);"></span>Total visits</span><span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(90deg,#a5f3fc,#0891b2);"></span>Unique customers visited</span><span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(90deg,#fbcfe8,#db2777);"></span>Unique visits (new customers)</span></div>
                    <div class="ef-empty" data-empty="empVisitsEmp">No visits found for this date</div>
                    <div class="ef-evscroll"><div class="ef-evinner"><canvas id="empVisitsEmp"></canvas></div></div>
                    <div class="ef-evidle" id="evIdle"></div>
                </div>

                <div class="ef-slabel">Performance</div>
                <div class="ef-g2">
                    <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#fef3c7;color:#d97706;"><i class="ti ti-award"></i></span><span>Top 10 performers<small>Ranked by achievement % of target</small></span></span><span class="ef-badge" data-badge></span></div>
                        <div class="ef-empty" data-empty="empTop">No employee achievement found for this period</div>
                        <div class="ef-lb" id="empTop"></div>
                    </div>
                    <div class="ef-card ef-chart ef-reveal" style="--d:.12s">
                        <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon" style="background:#ecfeff;color:#0891b2;"><i class="ti ti-chart-donut-3"></i></span><span>Achievement distribution<small>Employees grouped by % of target achieved</small></span></span><span class="ef-badge" data-badge></span></div>
                        <div class="ef-empty" data-empty="empDist">No employee targets found for this period</div>
                        <div class="ef-cat-wrap" id="empDistWrap">
                            <div class="ef-donut"><canvas id="empDist"></canvas><div class="ef-donut-c"><b data-emp-kpi="distTotal">-</b><span>Employees</span></div></div>
                            <div class="ef-seglist" id="empDistLegend"></div>
                        </div>
                        <div class="ef-attn" id="empAttn"></div>
                    </div>
                </div>

                <div class="ef-slabel">Target vs Achievement - all employees</div>
                <div class="ef-card ef-chart ef-reveal" style="--d:.05s">
                    <div class="ef-chtitle"><span class="ef-tleft"><span class="ef-chicon"><i class="ti ti-chart-bar"></i></span><span>Employee target vs achievement<small>Scroll sideways to see everyone &middot; bar colour = achievement band</small></span></span>
                        <span class="ef-sortbar"><button type="button" class="ef-chip active" data-emp-sort="pct">Achievement %</button><button type="button" class="ef-chip" data-emp-sort="value">Value</button><button type="button" class="ef-chip" data-emp-sort="name">A - Z</button></span>
                    </div>
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
            const urls = { secondary: root.dataset.secondaryUrl, product: root.dataset.productUrl, primary: root.dataset.primaryUrl, employees: root.dataset.employeesUrl, empVisits: root.dataset.empVisitsUrl };
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
                            const ds1 = chart.data.datasets.length > 1 ? chart.getDatasetMeta(1)?.data?.[i] : null;
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
            const doughnut = (id, rows, onHover, palette = colors) => {
                destroy(id);
                charts[id] = new Chart(document.getElementById(id), {
                    type: 'doughnut',
                    data: { labels: rows.map(r => r.label), datasets: [{ data: rows.map(r => Number(r.value || 0)), backgroundColor: rows.map((r, i) => palette[i % palette.length]), borderWidth: 2, borderColor: '#fff', hoverOffset: 10, borderRadius: 4, spacing: 1 }] },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '70%', layout: { padding: 8 },
                        animation: reduceMotion ? false : { animateRotate: true, animateScale: true, duration: 1100, easing: 'easeOutQuart' },
                        onHover: (evt, els) => onHover && onHover(els.length ? els[0].index : -1),
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            return ` ${ctx.label}: ${money(ctx.parsed)} (${total ? (ctx.parsed / total * 100).toFixed(1) : 0}%)`;
                        } } } }
                    }
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
            const renderTopCustomers = (top, id = 'sTop') => {
                const list = document.getElementById(id);
                const chips = document.getElementById(id + 'States');
                const show = hasValues(top);
                setVisible(document.querySelector(`[data-empty="${id}"]`), !show);
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
            // Target gap pill + animated achievement ring (shared by Secondary & Primary)
            const renderAchievement = (attr, ach, target, pct) => {
                const q = key => document.querySelector(`[${attr}="${key}"]`);
                q('targetGap').innerHTML = !target
                    ? `<span class="ef-pill warn"><i class="ti ti-alert-circle"></i>No target set for this period</span>`
                    : (ach >= target
                        ? `<span class="ef-pill ok"><i class="ti ti-circle-check"></i>Target exceeded by ${esc(money(ach - target))}</span>`
                        : `<span class="ef-pill warn"><i class="ti ti-trending-down"></i>${esc(money(target - ach))} short &middot; ${(100 - pct).toFixed(1)}% gap</span>`);
                const card = q('achCard');
                const tone = pct >= 100 ? 'green' : pct >= 75 ? '' : pct >= 50 ? 'amber' : 'purple';
                card.classList.remove('green', 'amber', 'purple');
                if (tone) card.classList.add(tone);
                const ring = q('ring');
                const circ = 2 * Math.PI * 31;
                ring.style.strokeDashoffset = circ;
                requestAnimationFrame(() => requestAnimationFrame(() => { ring.style.strokeDashoffset = circ * (1 - Math.min(pct, 100) / 100); }));
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
                renderAchievement('data-kpi', ach, target, pct);

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
            const setRing = (el, pct) => {
                if (!el) return;
                const circ = 2 * Math.PI * 31;
                el.style.strokeDashoffset = circ;
                requestAnimationFrame(() => requestAnimationFrame(() => { el.style.strokeDashoffset = circ * (1 - Math.min(Math.max(pct, 0), 100) / 100); }));
            };
            // Ranked product list (top sellers / slow movers)
            const renderProductList = (rows, id, segColor, slow = false, metric = 'value') => {
                const list = document.getElementById(id);
                const show = hasValues(rows);
                setVisible(document.querySelector(`[data-empty="${id}"]`), !show);
                list.style.display = show ? 'flex' : 'none';
                if (!show) { list.innerHTML = ''; return; }
                const byQty = metric === 'qty';
                const m = r => Number((byQty ? r.qty : r.value) || 0);
                const max = Math.max(...rows.map(m), 1);
                const total = rows.reduce((s, r) => s + m(r), 0);
                list.innerHTML = rows.map((r, i) => {
                    const value = Number(r.value || 0);
                    const qty = Number(r.qty || 0);
                    const mv = m(r);
                    const t = r.segment ? segColor(r.segment) : null;
                    const rank = slow ? `<span class="ef-rank"><i class="ti ti-arrow-down-right" style="font-size:13px;"></i></span>` : `<span class="ef-rank ${i < 3 ? 'r' + (i + 1) : ''}">${i + 1}</span>`;
                    return `<div class="ef-lbrow" style="--i:${Math.min(i, 14)};" title="${esc(r.label)}${r.code ? ' (' + esc(r.code) + ')' : ''}">
                        ${rank}
                        <div class="ef-lbmain">
                            <div class="ef-lbhead"><span class="ef-lbname">${esc(r.label)}</span><span class="ef-lbval">${byQty ? compact(qty) + ' units' : esc(money(value).replace('Rs. ', '₹'))}</span></div>
                            <div class="ef-lbmeta">
                                ${Number(r.is_npd) === 1 ? `<span class="ef-npd" title="New product (launched in last 12 months)"><i class="ti ti-sparkles"></i>NPD</span>` : ''}
                                ${r.code ? `<span class="ef-lbcode">${esc(r.code)}</span>` : ''}
                                ${t ? `<span class="ef-st" style="--st-bg:${t}1a;--st-fg:${t};"><i class="ti ti-category"></i>${esc(r.segment)}</span>` : ''}
                                ${byQty ? `<span>${esc(money(value).replace('Rs. ', '₹'))}</span>` : (qty ? `<span>${compact(qty)} units</span>` : '')}
                                <span class="ef-lbshare" title="Share of ${byQty ? 'units' : 'value'} in this list">${total ? (mv / total * 100).toFixed(1) : '0.0'}%</span>
                            </div>
                            <div class="ef-lbtrack"><div class="ef-lbfill" style="--w:${Math.max(mv / max * 100, 2).toFixed(1)}%;"></div></div>
                        </div>
                    </div>`;
                }).join('');
            };
            const shortLabel = (txt, n = 24) => { txt = String(txt ?? ''); return txt.length > n ? txt.slice(0, n - 1) + '…' : txt; };
            // Top / Bottom 10 NPD charts, each with its own Value / Quantity toggle
            let npdData = {};
            const npdMetric = { top: 'value', bottom: 'value' };
            const npdMeta = {
                top: { title: 'Top 10 NPD Sale', value: ['topNpdByValue', '#f9a8d4', '#be185d'], qty: ['topNpdByQty', '#67e8f9', '#0e7490'] },
                bottom: { title: 'Bottom 10 NPD Sale', value: ['bottomNpdByValue', '#fdba74', '#c2410c'], qty: ['bottomNpdByQty', '#a5b4fc', '#4338ca'] }
            };
            const moneyScale = (max) => max >= 10000000 ? { div: 10000000, unit: 'Cr' } : max >= 100000 ? { div: 100000, unit: 'L' } : max >= 1000 ? { div: 1000, unit: 'K' } : { div: 1, unit: '' };
            const renderNpdChart = (kind) => {
                const metric = npdMetric[kind];
                const byQty = metric === 'qty';
                const [key, from, to] = npdMeta[kind][metric];
                const rows = npdData[key] || [];
                const id = 'pNpd_' + kind;
                document.querySelector(`[data-npd-title="${kind}"]`).textContent = `${npdMeta[kind].title} – ${byQty ? 'Quantity' : 'Value'} wise`;
                if (empty(id, rows, [metric])) return;
                const sc = moneyScale(Math.max(...rows.map(r => Number(r.value || 0)), 0));
                const scaled = v => Number((Number(v || 0) / sc.div).toFixed(2));
                const fmt = r => byQty ? compact(r.qty) + ' units' : scaledMoney(scaled(r.value), sc.unit);
                const cfg = withPad(scaledChartOptions(sc.unit, 'y', { font: { size: 10, weight: '600', family: 'Inter' }, color: '#475569' }), { right: 70 });
                if (byQty) cfg.options.scales.x.ticks.callback = v => compact(v);
                cfg.options.plugins.tooltip.callbacks = {
                    title: items => rows[items[0]?.dataIndex]?.label || '',
                    label: ctx => {
                        const r = rows[ctx.dataIndex] || {};
                        return [` Value: ${money(r.value)}`, ` Quantity: ${compact(r.qty)} units`];
                    },
                    afterBody: items => {
                        const r = rows[items[0]?.dataIndex] || {};
                        const out = [];
                        if (r.code) out.push('Code: ' + r.code);
                        if (r.segment) out.push('Segment: ' + r.segment);
                        if (r.launch_date) out.push('Launched: ' + new Date(String(r.launch_date).replace(' ', 'T')).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }));
                        return out;
                    }
                };
                bar(id, rows.map(r => shortLabel(r.label)), [{
                    label: byQty ? 'Quantity' : 'Value',
                    data: rows.map(r => byQty ? Number(r.qty || 0) : scaled(r.value)),
                    backgroundColor: grad(from, to, true), borderRadius: 6, borderSkipped: false, maxBarThickness: 18, categoryPercentage: .8, barPercentage: .9
                }], Object.assign(cfg, { plugins: [endLabels(i => rows[i] ? fmt(rows[i]) : '')] }));
            };
            document.querySelectorAll('[data-npd-chart]').forEach(btn => btn.addEventListener('click', () => {
                const kind = btn.dataset.npdChart;
                document.querySelectorAll(`[data-npd-chart="${kind}"]`).forEach(b => b.classList.toggle('active', b === btn));
                npdMetric[kind] = btn.dataset.metric;
                renderNpdChart(kind);
            }));
            const renderNpd = (data, totalSales, totalQty) => {
                const k = data.kpis || {};
                const c = data.charts || {};
                const q = key => document.querySelector(`[data-pd-npd="${key}"]`);
                const npdSales = Number(k.npdSales || 0);
                const npdQty = Number(k.npdQuantity || 0);
                const skus = Number(k.npdSkusSold || 0);
                const catalog = Number(k.npdCatalog || 0);
                const pct = (a, b) => b ? a / b * 100 : 0;
                countUp(q('sales'), npdSales, money);
                countUp(q('qty'), npdQty, compact);
                countUp(q('skus'), skus, compact);
                q('salesShare').textContent = pct(npdSales, totalSales).toFixed(1) + '%';
                q('qtyShare').textContent = pct(npdQty, totalQty).toFixed(1) + '%';
                q('skuShare').textContent = pct(skus, catalog).toFixed(1) + '%';
                q('skuSub').textContent = 'of ' + compact(catalog) + ' NPD SKUs';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    q('salesBar').style.width = Math.min(pct(npdSales, totalSales), 100) + '%';
                    q('qtyBar').style.width = Math.min(pct(npdQty, totalQty), 100) + '%';
                    q('skuBar').style.width = Math.min(pct(skus, catalog), 100) + '%';
                }));

                npdData = c;
                renderNpdChart('top');
                renderNpdChart('bottom');
            };
            let pdTop = null;
            let pdMetric = 'value';
            const renderTopProducts = () => {
                if (!pdTop) return;
                const c = pdTop.data.charts;
                const rows = pdMetric === 'qty' ? (c.topProductsByQty || (c.topProducts || []).slice().sort((a, b) => Number(b.qty || 0) - Number(a.qty || 0))) : (c.topProducts || []);
                document.querySelector('[data-pd-topsub]').textContent = pdMetric === 'qty' ? 'Top 23 by units sold, with segment & value' : 'Top 23 by order value, with segment & units';
                renderProductList(rows, 'pTop', pdTop.segColor, false, pdMetric);
                document.getElementById('pTop').scrollTop = 0;
            };
            document.querySelectorAll('[data-pd-metric]').forEach(btn => btn.addEventListener('click', () => {
                document.querySelectorAll('[data-pd-metric]').forEach(b => b.classList.toggle('active', b === btn));
                pdMetric = btn.dataset.pdMetric;
                renderTopProducts();
            }));
            const renderProduct = (data) => {
                updateBadges(data.periodLabel);
                const k = data.kpis || {};
                const q = key => document.querySelector(`[data-pd-kpi="${key}"]`);
                const sales = Number(k.productSales || 0);
                const qty = Number(k.totalQuantity || 0);
                const sold = Number(k.productsSold || 0);
                const catalog = Number(k.catalogProducts || 0);
                const coverage = catalog ? sold / catalog * 100 : 0;
                const seg = data.charts.segmentWiseSales || [];
                const segTotal = seg.reduce((sum, row) => sum + Number(row.value || 0), 0);

                countUp(q('sales'), sales, money);
                countUp(q('qty'), qty, compact);
                countUp(q('sold'), sold, compact);
                countUp(document.querySelector('[data-pd-hero="sales"]'), sales, money);
                countUp(document.querySelector('[data-pd-hero="qty"]'), qty, compact);
                countUp(document.querySelector('[data-pd-hero="sold"]'), sold, compact);
                q('avgPrice').textContent = qty ? 'Avg ' + money(sales / qty) + ' per unit' : 'No units in period';
                q('avgQty').textContent = sold ? compact(qty / sold) + ' units per SKU' : 'No SKUs in period';
                q('catalogSub').textContent = 'of ' + compact(catalog) + ' active SKUs';
                q('coverage').textContent = coverage.toFixed(1) + '% of catalogue moving';
                setRing(q('ring'), coverage);
                q('topSegment').textContent = k.topSegment || '-';
                q('topSegment').title = k.topSegment || '';
                q('topSegmentSub').textContent = seg.length && segTotal ? (Number(seg[0].value) / segTotal * 100).toFixed(1) + '% share · ' + money(seg[0].value) : 'No segment sales';
                q('segments').textContent = compact(k.segments ?? seg.length) + ' segments with sales';

                const segIndex = {};
                seg.forEach((r, i) => { segIndex[r.label] = i; });
                const segColor = (name) => segIndex[name] !== undefined ? colors[segIndex[name] % colors.length] : '#64748b';
                pdTop = { data, segColor };
                renderTopProducts();
                renderNpd(data, sales, qty);
                renderProductList(data.charts.slowMovingProducts || [], 'pSlow', segColor, true);

                const legend = document.getElementById('pSegmentLegend');
                const wrap = document.getElementById('pSegmentWrap');
                if (!hasValues(seg)) {
                    setVisible(document.querySelector('[data-empty="pSegment"]'), true);
                    wrap.style.display = 'none';
                    destroy('pSegment');
                    legend.innerHTML = '';
                } else {
                    setVisible(document.querySelector('[data-empty="pSegment"]'), false);
                    wrap.style.display = 'flex';
                    q('segTotal').textContent = money(segTotal).replace('Rs. ', '₹');
                    const segMax = Math.max(...seg.map(r => Number(r.value || 0)), 1);
                    legend.innerHTML = seg.map((row, i) => {
                        const c = colors[i % colors.length];
                        const v = Number(row.value || 0);
                        return `<div class="ef-segrow" data-seg="${i}" style="--i:${Math.min(i, 12)};">
                            <div class="ef-segtop"><span class="ef-segname"><span class="ef-legsq" style="background:${c};"></span><span>${esc(row.label)}</span></span><span class="ef-segval">${segTotal ? (v / segTotal * 100).toFixed(1) : 0}%<small>${esc(money(v).replace('Rs. ', '₹'))}</small></span></div>
                            <div class="ef-segbar"><div style="width:${Math.max(v / segMax * 100, 1).toFixed(1)}%;background:${c};"></div></div>
                        </div>`;
                    }).join('');
                    const highlight = (idx) => legend.querySelectorAll('.ef-segrow').forEach(el => el.classList.toggle('hl', Number(el.dataset.seg) === idx));
                    doughnut('pSegment', seg, highlight);
                    legend.querySelectorAll('.ef-segrow').forEach(el => {
                        el.addEventListener('mouseenter', () => {
                            const chart = charts.pSegment; if (!chart) return;
                            const idx = Number(el.dataset.seg);
                            chart.setActiveElements([{ datasetIndex: 0, index: idx }]);
                            chart.tooltip.setActiveElements([{ datasetIndex: 0, index: idx }], { x: chart.width / 2, y: chart.height / 2 });
                            chart.update();
                        });
                        el.addEventListener('mouseleave', () => {
                            const chart = charts.pSegment; if (!chart) return;
                            chart.setActiveElements([]); chart.tooltip.setActiveElements([], { x: 0, y: 0 }); chart.update();
                        });
                    });
                }

                const sku = data.charts.topSkuTrend || [];
                const skuRows = sku.flatMap(s => s.data || []);
                const skuLegend = document.getElementById('pSkuLegend');
                if (!empty('pSku', skuRows)) {
                    const labels = (sku[0]?.data || []).map(r => r.label);
                    skuLegend.innerHTML = sku.map((s, i) => `<span class="ef-legitem" data-sku="${i}" title="${esc(s.label)}"><span class="ef-legsq" style="background:${colors[i % colors.length]};"></span><span style="font-size:9.5px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(s.label)}</span></span>`).join('');
                    if (labels.length < 3) {
                        // Single / two-month period: compare SKUs side by side as bars
                        const totals = sku.map(s => (s.data || []).reduce((a, r) => a + Number(r.value || 0), 0));
                        bar('pSku', sku.map(s => s.label.length > 18 ? s.label.slice(0, 17) + '…' : s.label), [{
                            label: labels.join(' - ') || 'Sales', data: totals.map(lakh),
                            backgroundColor: sku.map((s, i) => colors[i % colors.length]), borderRadius: 6, borderSkipped: false, maxBarThickness: 16
                        }], withPad(scaledChartOptions('L', 'y', { font: { size: 9.5, weight: '600', family: 'Inter' }, color: '#475569' }), { right: 8 }));
                    } else {
                        line('pSku', labels, sku.map((series, i) => ({
                            label: series.label, data: (series.data || []).map(r => lakh(r.value)), borderColor: colors[i % colors.length],
                            backgroundColor: colors[i % colors.length], tension: .4, pointRadius: 0, pointHoverRadius: 5, pointHoverBorderWidth: 2, pointHoverBorderColor: '#fff', borderWidth: 2
                        })), scaledChartOptions('L', 'x'));
                    }
                    skuLegend.querySelectorAll('[data-sku]').forEach(el => el.addEventListener('click', () => {
                        const chart = charts.pSku; if (!chart) return;
                        const idx = Number(el.dataset.sku);
                        if (chart.config.type === 'line') {
                            const visible = chart.isDatasetVisible(idx);
                            chart.setDatasetVisibility(idx, !visible);
                            el.classList.toggle('off', visible);
                        } else {
                            const ds = chart.data.datasets[0];
                            ds._orig = ds._orig || ds.data.slice();
                            const hidden = ds.data[idx] === null;
                            ds.data[idx] = hidden ? ds._orig[idx] : null;
                            el.classList.toggle('off', !hidden);
                        }
                        chart.update();
                    }));
                } else {
                    skuLegend.innerHTML = '';
                }
            };
            const renderPrimary = (data) => {
                updateBadges(data.periodLabel);
                const k = data.kpis || {};
                const ach = Number(k.primarySalesAchievement || 0);
                const target = Number(k.primarySalesTarget || 0);
                const pct = Number(k.overallAchievementPercent || 0);
                const invoices = Number(k.totalInvoices || 0);
                const qty = Number(k.totalQuantity || 0);
                const q = key => document.querySelector(`[data-pr-kpi="${key}"]`);
                countUp(q('achievement'), ach, money);
                countUp(q('target'), target, money);
                countUp(q('percent'), pct, v => Number(v).toFixed(1).replace(/\.0$/, '') + '%', 1200);
                countUp(q('invoices'), invoices, compact);
                countUp(q('qty'), qty, compact);
                q('targetSub').textContent = 'vs ' + money(target) + ' target';
                q('avgInvoice').textContent = invoices ? 'Avg ' + money(ach / invoices) + ' per invoice' : 'No invoices in period';
                q('avgQty').textContent = invoices ? compact(qty / invoices) + ' units per invoice' : 'No invoices in period';
                renderAchievement('data-pr-kpi', ach, target, pct);

                const topInfo = renderTopCustomers(data.charts.topDealers || [], 'prTop');
                countUp(document.querySelector('[data-pr-hero="sales"]'), ach, money);
                countUp(document.querySelector('[data-pr-hero="invoices"]'), invoices, compact);
                countUp(document.querySelector('[data-pr-hero="states"]'), topInfo.states, compact);

                const targetRows = data.charts.salesVsTarget || [];
                if (!empty('prTarget', targetRows, ['achievement', 'target'])) bar('prTarget', targetRows.map(r => r.label), [
                    { label: 'Achievement', data: targetRows.map(r => lakh(r.achievement)), backgroundColor: grad('#4ade80', '#15803d'), borderRadius: 6, borderSkipped: false, maxBarThickness: 26, categoryPercentage: .6, barPercentage: .85 },
                    { label: 'Target', data: targetRows.map(r => lakh(r.target)), backgroundColor: '#e2e8f0', hoverBackgroundColor: '#cbd5e1', borderRadius: 6, borderSkipped: false, maxBarThickness: 26, categoryPercentage: .6, barPercentage: .85 }
                ], Object.assign(withPad(scaledChartOptions('L', 'x'), { top: 16 }), {
                    plugins: [endLabels(i => {
                        const r = targetRows[i];
                        return r && Number(r.target) > 0 ? Math.round(r.achievement / r.target * 100) + '%' : '';
                    })]
                }));

                const trend = data.charts.salesTrend || [];
                if (!empty('prTrend', trend)) {
                    if (trend.length < 3) {
                        bar('prTrend', trend.map(r => r.label), [{ label: 'Sales', data: trend.map(r => crore(r.value)), backgroundColor: grad('#6ee7b7', '#059669'), borderRadius: 8, borderSkipped: false, maxBarThickness: 46 }], scaledChartOptions('Cr', 'x'));
                    } else {
                        line('prTrend', trend.map(r => r.label), [{
                            label: 'Sales', data: trend.map(r => crore(r.value)), borderColor: '#059669',
                            backgroundColor: (ctx) => {
                                const a = ctx.chart.chartArea; if (!a) return 'rgba(5,150,105,.1)';
                                const g = ctx.chart.ctx.createLinearGradient(0, a.top, 0, a.bottom);
                                g.addColorStop(0, 'rgba(5,150,105,.28)'); g.addColorStop(1, 'rgba(5,150,105,0)'); return g;
                            },
                            fill: true, tension: .4, pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderColor: '#059669', pointBorderWidth: 2, borderWidth: 2.5
                        }], scaledChartOptions('Cr', 'x'));
                    }
                }

                const yoy = data.charts.yoyGrowth || [];
                const yoyRows = yoy.flatMap(y => y.data || []);
                const yoyColors = [['#dcfce7', '#bbf7d0'], ['#4ade80', '#16a34a'], ['#15803d', '#14532d']];
                document.getElementById('prYoyLegend').innerHTML = yoy.map((s, i) => {
                    const c = yoyColors[i] || [colors[i], colors[i]];
                    return `<span class="ef-legitem"><span class="ef-legsq" style="background:linear-gradient(${c[0]},${c[1]});"></span>${esc(s.label)}</span>`;
                }).join('');
                if (!empty('prYoy', yoyRows)) bar('prYoy', (yoy[0]?.data || []).map(r => r.label), yoy.map((series, i) => {
                    const c = yoyColors[i] || [colors[i], colors[i]];
                    return { label: series.label, data: (series.data || []).map(r => crore(r.value)), backgroundColor: grad(c[0], c[1]), borderRadius: 4, borderSkipped: false, maxBarThickness: 12, categoryPercentage: .75, barPercentage: .9 };
                }), scaledChartOptions('Cr', 'x'));

                const region = (data.charts.regionWiseSales || []).slice().sort((a, b) => Number(b.achievement || 0) - Number(a.achievement || 0));
                if (!empty('prRegion', region, ['achievement', 'target'])) bar('prRegion', region.map(r => r.label), [
                    { label: 'Achievement', data: region.map(r => lakh(r.achievement)), backgroundColor: grad('#93c5fd', '#2563eb', true), borderRadius: 6, borderSkipped: false, maxBarThickness: 14, categoryPercentage: .75, barPercentage: .9 },
                    { label: 'Target', data: region.map(r => lakh(r.target)), backgroundColor: '#e2e8f0', hoverBackgroundColor: '#cbd5e1', borderRadius: 6, borderSkipped: false, maxBarThickness: 14, categoryPercentage: .75, barPercentage: .9 }
                ], Object.assign(withPad(scaledChartOptions('L', 'y', { font: { size: 10, weight: '600', family: 'Inter' }, color: '#475569' }), { right: 44 }), {
                    plugins: [endLabels(i => {
                        const r = region[i];
                        if (!r) return '';
                        return Number(r.target) > 0 ? Math.round(r.achievement / r.target * 100) + '%' : (Number(r.achievement) > 0 ? scaledMoney(lakh(r.achievement), 'L') : '');
                    })]
                }));
            };
            // Achievement bands used across the Employees tab
            const empBands = [
                { key: 'g', label: '90%+ (on target)', min: 90, color: '#16a34a', light: '#86efac' },
                { key: 'b', label: '75 - 89%', min: 75, color: '#3b82f6', light: '#93c5fd' },
                { key: 'a', label: '60 - 74%', min: 60, color: '#d97706', light: '#fcd34d' },
                { key: 'r', label: 'Below 60%', min: -Infinity, color: '#dc2626', light: '#fca5a5' }
            ];
            const empBand = (row) => empBands.find(b => Number(row.achievementPercent || 0) >= b.min);
            const pctLabel = v => Number(v || 0).toFixed(1).replace(/\.0$/, '') + '%';
            let empRows = [];
            let empSort = 'pct';
            const renderEmpChart = () => {
                const rows = empRows.slice();
                if (empSort === 'pct') rows.sort((a, b) => Number(b.achievementPercent || 0) - Number(a.achievementPercent || 0) || Number(b.achievement || 0) - Number(a.achievement || 0));
                else if (empSort === 'value') rows.sort((a, b) => Number(b.achievement || 0) - Number(a.achievement || 0));
                else rows.sort((a, b) => String(a.label).localeCompare(String(b.label), undefined, { numeric: true, sensitivity: 'base' }));
                if (empty('empTarget', rows, ['achievement', 'target'])) return;
                const inner = document.querySelector('.ef-emp-chart-inner');
                if (inner) inner.style.minWidth = Math.max(900, rows.length * 46 + 120) + 'px';
                const bandOf = rows.map(empBand);
                bar('empTarget', rows.map(r => String(r.label).length > 16 ? String(r.label).slice(0, 15) + '…' : r.label), [
                    { label: 'Achievement', data: rows.map(r => lakh(r.achievement)), backgroundColor: ctx => grad(bandOf[ctx.dataIndex]?.light || '#93c5fd', bandOf[ctx.dataIndex]?.color || '#3b82f6')(ctx), borderRadius: 5, borderSkipped: false, maxBarThickness: 16, categoryPercentage: .7, barPercentage: .9 },
                    { label: 'Target', data: rows.map(r => lakh(r.target)), backgroundColor: '#e2e8f0', hoverBackgroundColor: '#cbd5e1', borderRadius: 5, borderSkipped: false, maxBarThickness: 16, categoryPercentage: .7, barPercentage: .9 }
                ], {
                    plugins: [endLabels(i => Number(rows[i]?.target) > 0 ? Math.round(rows[i].achievementPercent) + '%' : '')],
                    options: {
                        layout: { padding: { top: 16 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    title: items => {
                                        const row = rows[items[0]?.dataIndex] || {};
                                        return row.name + (row.code ? ` (${row.code})` : '');
                                    },
                                    label: ctx => ` ${ctx.dataset.label}: ${scaledMoney(ctx.parsed.y, 'L')}`,
                                    afterBody: items => {
                                        const row = rows[items[0]?.dataIndex] || {};
                                        return Number(row.target) > 0 ? `Achieved: ${pctLabel(row.achievementPercent)}` : 'No target set';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { ticks: { font: { size: 9, family: 'Inter', weight: '600' }, color: '#64748b', autoSkip: false, maxRotation: 45, minRotation: 45 }, grid: { display: false }, border: { display: false } },
                            y: { ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8', callback: value => scaledMoney(value, 'L') }, grid: { color: 'rgba(0,0,0,.04)' }, border: { display: false } }
                        }
                    }
                });
            };
            let visitData = null;
            let visitType = 'ALL';
            const visitTypeLabel = { ALL: 'All customers', RETAILER: 'Retailers', MECHANIC: 'Mechanics', GARAGE: 'Garages', WORKSHOP: 'Workshops' };
            const renderVisits = () => {
                const v = visitData || {};
                const t = (v.totals || {})[visitType] || { visits: 0, unique: 0, new: 0 };
                const q = key => document.querySelector(`[data-visit="${key}"]`);
                countUp(q('visits'), t.visits, compact);
                countUp(q('unique'), t.unique, compact);
                countUp(q('new'), t.new, compact);
                q('repeat').textContent = t.unique ? (t.visits / t.unique).toFixed(1) + ' visits / customer' : '';
                q('newShare').textContent = t.unique ? (t.new / t.unique * 100).toFixed(1) + '% of visited' : '';
                document.querySelector('[data-visit-sub]').textContent = `${visitTypeLabel[visitType]} · ${v.granularity === 'month' ? 'month-wise' : 'day-wise'} check-ins by the team`;

                const rows = (v.buckets || []).map(b => Object.assign({ label: b.label }, b[visitType] || { visits: 0, unique: 0, new: 0 }));
                if (empty('empVisits', rows, ['visits'])) return;
                const many = rows.length > 16;
                bar('empVisits', rows.map(r => r.label), [
                    { type: 'line', label: 'Unique visits (new customers)', data: rows.map(r => r.new), borderColor: '#db2777', backgroundColor: '#db2777', borderWidth: 2.5, tension: .35, pointRadius: many ? 2 : 4, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderWidth: 2, order: 0 },
                    { type: 'line', label: 'Unique customers visited', data: rows.map(r => r.unique), borderColor: '#0891b2', backgroundColor: '#0891b2', borderWidth: 2, borderDash: [5, 4], tension: .35, pointRadius: many ? 0 : 3, pointHoverRadius: 5, pointBackgroundColor: '#fff', pointBorderWidth: 2, order: 1 },
                    { label: 'Total visits', data: rows.map(r => r.visits), backgroundColor: grad('#c7d2fe', '#4f46e5'), borderRadius: 5, borderSkipped: false, maxBarThickness: 28, order: 2 }
                ], {
                    options: {
                        layout: { padding: { top: 8 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: {
                                label: ctx => ` ${ctx.dataset.label}: ${compact(ctx.parsed.y)}`,
                                afterBody: items => {
                                    const r = rows[items[0]?.dataIndex];
                                    return r && r.unique ? `New customers: ${(r.new / r.unique * 100).toFixed(0)}% of visited` : '';
                                }
                            } }
                        },
                        scales: {
                            x: { ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8', autoSkip: true, maxTicksLimit: many ? 16 : 31, maxRotation: 0 }, grid: { display: false }, border: { display: false } },
                            y: { beginAtZero: true, ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8', precision: 0, callback: val => compact(val) }, grid: { color: 'rgba(0,0,0,.04)' }, border: { display: false } }
                        }
                    }
                });
            };
            document.querySelectorAll('[data-visit-type]').forEach(btn => btn.addEventListener('click', () => {
                document.querySelectorAll('[data-visit-type]').forEach(b => b.classList.toggle('active', b === btn));
                visitType = btn.dataset.visitType;
                if (visitData) renderVisits();
            }));
            // ---- Employee-wise visits (own date filter, single day allowed) ----
            const ymd = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
            const evFrom = document.getElementById('evFrom');
            const evTo = document.getElementById('evTo');
            const ev = { mode: 'today', from: ymd(new Date()), to: ymd(new Date()), type: 'ALL', sort: 'visits', data: null, period: null, seq: 0 };
            const setEvRange = (mode) => {
                const d = new Date();
                if (mode === 'today') { ev.from = ev.to = ymd(d); }
                else if (mode === 'yesterday') { d.setDate(d.getDate() - 1); ev.from = ev.to = ymd(d); }
                else if (mode === 'period' && ev.period) { ev.from = ev.period.from; ev.to = ev.period.to; }
                ev.mode = mode;
                evFrom.value = ev.from;
                evTo.value = ev.to;
                document.querySelectorAll('[data-ev-range]').forEach(b => b.classList.toggle('active', b.dataset.evRange === mode));
                [evFrom, evTo].forEach(el => el.classList.toggle('on', mode === 'custom'));
            };
            const renderEmpVisits = () => {
                const d = ev.data || {};
                const zero = { visits: 0, unique: 0, new: 0 };
                const all = (d.employees || []).map(e => Object.assign({ name: e.name, code: e.code }, e[ev.type] || zero));
                const byName = (a, b) => String(a.name).localeCompare(String(b.name), undefined, { sensitivity: 'base', numeric: true });
                const sorters = {
                    visits: (a, b) => b.visits - a.visits || b.unique - a.unique || byName(a, b),
                    new: (a, b) => b.new - a.new || b.visits - a.visits || byName(a, b),
                    name: byName
                };
                const active = all.filter(r => r.visits > 0).sort(sorters[ev.sort]);
                const idle = all.filter(r => r.visits === 0).sort(byName);
                const sum = key => active.reduce((t, r) => t + Number(r[key] || 0), 0);
                const visits = sum('visits'), uniq = sum('unique'), fresh = sum('new');
                const q = key => document.querySelector(`[data-ev="${key}"]`);

                document.querySelector('[data-ev-sub]').textContent = `${visitTypeLabel[ev.type]} · ${d.periodLabel || '-'}`;
                q('active').textContent = all.length ? `${active.length} / ${all.length}` : '-';
                q('activeShare').textContent = all.length ? (active.length / all.length * 100).toFixed(0) + '% of team' : '';
                countUp(q('visits'), visits, compact);
                q('avg').textContent = active.length ? (visits / active.length).toFixed(1) + ' per employee' : '';
                countUp(q('new'), fresh, compact);
                q('newShare').textContent = uniq ? (fresh / uniq * 100).toFixed(1) + '% of visited' : '';

                const idleBox = document.getElementById('evIdle');
                idleBox.style.display = idle.length && all.length ? 'block' : 'none';
                idleBox.innerHTML = idle.length ? `<i class="ti ti-alert-triangle"></i> <b>${idle.length}</b> employee${idle.length > 1 ? 's have' : ' has'} no ${ev.type === 'ALL' ? '' : esc(visitTypeLabel[ev.type].toLowerCase()) + ' '}visits on ${esc(d.periodLabel || 'this date')}<div class="names">${idle.slice(0, 20).map(r => `<span title="${esc(r.code)}">${esc(r.name)}</span>`).join('')}${idle.length > 20 ? `<span>+${idle.length - 20} more</span>` : ''}</div>` : '';

                const emptyEl = document.querySelector('[data-empty="empVisitsEmp"]');
                emptyEl.textContent = d.error ? 'Could not load visits (' + d.error + ')' : `No visits found for ${d.periodLabel || 'this date'}`;
                if (empty('empVisitsEmp', active, ['visits'])) return;
                document.querySelector('.ef-evinner').style.height = Math.max(200, active.length * 34 + 48) + 'px';
                const thin = { borderRadius: 4, borderSkipped: false, barPercentage: .92, categoryPercentage: .78, maxBarThickness: 12 };
                bar('empVisitsEmp', active.map(r => shortLabel(r.name, 22)), [
                    Object.assign({ label: 'Total visits', data: active.map(r => r.visits), backgroundColor: grad('#c7d2fe', '#4f46e5', true) }, thin),
                    Object.assign({ label: 'Unique customers visited', data: active.map(r => r.unique), backgroundColor: grad('#a5f3fc', '#0891b2', true) }, thin),
                    Object.assign({ label: 'Unique visits (new customers)', data: active.map(r => r.new), backgroundColor: grad('#fbcfe8', '#db2777', true) }, thin)
                ], {
                    indexAxis: 'y',
                    options: {
                        animation: reduceMotion ? false : { duration: 700, easing: 'easeOutQuart', delay: c => (c.type === 'data' && c.mode === 'default') ? Math.min(c.dataIndex * 30, 600) + c.datasetIndex * 90 : 0 },
                        interaction: { mode: 'index', intersect: false, axis: 'y' },
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: {
                                title: items => { const r = active[items[0]?.dataIndex]; return r ? r.name + (r.code ? ' (' + r.code + ')' : '') : ''; },
                                label: c => ` ${c.dataset.label}: ${compact(c.parsed.x)}`,
                                afterBody: items => { const r = active[items[0]?.dataIndex]; return r && r.unique ? `${(r.visits / r.unique).toFixed(1)} visits / customer` : ''; }
                            } }
                        },
                        scales: {
                            x: { beginAtZero: true, position: 'top', ticks: { font: { size: 9, family: 'Inter' }, color: '#94a3b8', precision: 0, callback: v => compact(v) }, grid: { color: 'rgba(0,0,0,.04)' }, border: { display: false } },
                            y: { ticks: { font: { size: 10, family: 'Inter', weight: '600' }, color: '#475569', autoSkip: false }, grid: { display: false }, border: { display: false } }
                        }
                    }
                });
            };
            const loadEmpVisits = async () => {
                const seq = ++ev.seq;
                const query = new URLSearchParams({ fromDate: ev.from, toDate: ev.to });
                if (state.zoneId) query.set('zoneId', state.zoneId);
                const card = document.getElementById('evCard');
                card.classList.add('ev-busy');
                try {
                    const res = await fetch(urls.empVisits + '?' + query.toString(), { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();
                    if (seq !== ev.seq) return;
                    ev.data = data;
                } catch (e) {
                    if (seq !== ev.seq) return;
                    ev.data = { employees: [], error: e.message || 'request failed' };
                } finally {
                    if (seq === ev.seq) card.classList.remove('ev-busy');
                }
                renderEmpVisits();
            };
            document.querySelectorAll('[data-ev-range]').forEach(btn => btn.addEventListener('click', () => { setEvRange(btn.dataset.evRange); loadEmpVisits(); }));
            [evFrom, evTo].forEach(el => el.addEventListener('change', () => {
                if (!evFrom.value || !evTo.value) return;
                if (el === evFrom && evFrom.value > evTo.value) evTo.value = evFrom.value; // picking one day = from & to same
                if (el === evTo && evTo.value < evFrom.value) evFrom.value = evTo.value;
                ev.from = evFrom.value;
                ev.to = evTo.value;
                setEvRange('custom');
                loadEmpVisits();
            }));
            document.querySelectorAll('[data-ev-type]').forEach(btn => btn.addEventListener('click', () => {
                document.querySelectorAll('[data-ev-type]').forEach(b => b.classList.toggle('active', b === btn));
                ev.type = btn.dataset.evType;
                if (ev.data) renderEmpVisits();
            }));
            document.querySelectorAll('[data-ev-sort]').forEach(btn => btn.addEventListener('click', () => {
                document.querySelectorAll('[data-ev-sort]').forEach(b => b.classList.toggle('active', b === btn));
                ev.sort = btn.dataset.evSort;
                if (ev.data) renderEmpVisits();
            }));
            const renderEmployees = (data) => {
                updateBadges(data.periodLabel);
                const k = data.kpis || {};
                const s = data.summary || {};
                const q = key => document.querySelector(`[data-emp-kpi="${key}"]`);
                const total = Number(k.totalEmployees || 0);
                const market = Number(k.todayOnMarket || 0);
                const leave = Number(k.todayOnLeave || 0);
                const mis = Number(k.misPunch || 0);
                const share = v => total ? v / total * 100 : 0;

                countUp(q('totalEmployees'), total, compact);
                countUp(q('todayOnMarket'), market, compact);
                countUp(q('todayOnLeave'), leave, compact);
                countUp(q('misPunch'), mis, compact);
                q('marketSub').textContent = share(market).toFixed(1) + '% of ' + compact(total) + ' active';
                q('leaveSub').textContent = share(leave).toFixed(1) + '% of active strength';
                q('misSub').textContent = share(mis).toFixed(1) + '% not punched in today';
                setRing(q('ring'), share(market));
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    q('leaveBar').style.width = Math.min(share(leave), 100) + '%';
                    q('misBar').style.width = Math.min(share(mis), 100) + '%';
                }));
                countUp(document.querySelector('[data-emp-summary="totalOrderQty"]'), s.totalOrderQty, compact);
                countUp(document.querySelector('[data-emp-summary="totalOrderValue"]'), s.totalOrderValue, money);
                countUp(document.querySelector('[data-emp-summary="uniqueActiveCustomers"]'), s.uniqueActiveCustomers, compact);

                empRows = data.charts.employeeTargetVsAchievement || [];
                const withTarget = empRows.filter(r => Number(r.target || 0) > 0);
                const onTarget = withTarget.filter(r => Number(r.achievementPercent || 0) >= 90).length;
                countUp(document.querySelector('[data-emp-hero="market"]'), market, compact);
                countUp(document.querySelector('[data-emp-hero="value"]'), s.totalOrderValue, money);
                document.querySelector('[data-emp-hero="onTarget"]').textContent = withTarget.length ? `${onTarget} / ${withTarget.length}` : '-';

                // Top 10 performers: by % where targets exist, otherwise by achievement value
                const list = document.getElementById('empTop');
                const ranked = (withTarget.length ? withTarget.slice().sort((a, b) => b.achievementPercent - a.achievementPercent) : empRows.slice().sort((a, b) => b.achievement - a.achievement)).filter(r => Number(r.achievement || 0) > 0).slice(0, 10);
                setVisible(document.querySelector('[data-empty="empTop"]'), !ranked.length);
                list.style.display = ranked.length ? 'flex' : 'none';
                const maxAch = Math.max(...ranked.map(r => Number(r.achievement || 0)), 1);
                list.innerHTML = ranked.map((r, i) => {
                    const band = empBand(r);
                    const hasT = Number(r.target || 0) > 0;
                    const w = hasT ? Math.min(Number(r.achievementPercent || 0), 100) : Number(r.achievement || 0) / maxAch * 100;
                    return `<div class="ef-lbrow" style="--i:${i};" title="${esc(r.name)}${r.code ? ' (' + esc(r.code) + ')' : ''}">
                        <span class="ef-rank ${i < 3 ? 'r' + (i + 1) : ''}">${i + 1}</span>
                        <div class="ef-lbmain">
                            <div class="ef-lbhead"><span class="ef-lbname">${esc(r.name)}</span>${hasT ? `<span class="ef-pct" style="background:${band.color}1a;color:${band.color};">${pctLabel(r.achievementPercent)}</span>` : `<span class="ef-lbval">${esc(scaledMoney(lakh(r.achievement), 'L'))}</span>`}</div>
                            <div class="ef-lbmeta">
                                ${r.code ? `<span class="ef-lbcode">${esc(r.code)}</span>` : ''}
                                <span class="ef-lbsub">${esc(scaledMoney(lakh(r.achievement), 'L'))}${hasT ? ' of ' + esc(scaledMoney(lakh(r.target), 'L')) : ''}</span>
                            </div>
                            <div class="ef-lbtrack"><div class="ef-lbfill" style="--w:${Math.max(w, 2).toFixed(1)}%;background:linear-gradient(90deg,${band.light},${band.color});"></div></div>
                        </div>
                    </div>`;
                }).join('');

                // Distribution donut
                const wrap = document.getElementById('empDistWrap');
                const legend = document.getElementById('empDistLegend');
                const attn = document.getElementById('empAttn');
                const dist = empBands.map(b => ({ label: b.label, value: withTarget.filter(r => empBand(r) === b).length }));
                if (!withTarget.length) {
                    setVisible(document.querySelector('[data-empty="empDist"]'), true);
                    wrap.style.display = 'none'; attn.style.display = 'none';
                    destroy('empDist'); legend.innerHTML = '';
                } else {
                    setVisible(document.querySelector('[data-empty="empDist"]'), false);
                    wrap.style.display = 'flex';
                    q('distTotal').textContent = compact(withTarget.length);
                    const maxD = Math.max(...dist.map(d => d.value), 1);
                    legend.innerHTML = dist.map((d, i) => `<div class="ef-segrow" data-seg="${i}" style="--i:${i};">
                        <div class="ef-segtop"><span class="ef-segname"><span class="ef-legsq" style="background:${empBands[i].color};"></span><span>${esc(d.label)}</span></span><span class="ef-segval">${d.value}<small>${(d.value / withTarget.length * 100).toFixed(0)}%</small></span></div>
                        <div class="ef-segbar"><div style="width:${Math.max(d.value / maxD * 100, 1).toFixed(1)}%;background:${empBands[i].color};"></div></div>
                    </div>`).join('');
                    const highlight = idx => legend.querySelectorAll('.ef-segrow').forEach(el => el.classList.toggle('hl', Number(el.dataset.seg) === idx));
                    doughnut('empDist', dist, highlight, empBands.map(b => b.color));
                    charts.empDist.options.plugins.tooltip.callbacks.label = ctx => ` ${ctx.label}: ${ctx.parsed} employee${ctx.parsed === 1 ? '' : 's'}`;
                    charts.empDist.update('none');
                    const low = withTarget.filter(r => Number(r.achievementPercent || 0) < 60).sort((a, b) => a.achievementPercent - b.achievementPercent);
                    attn.style.display = low.length ? 'block' : 'none';
                    attn.innerHTML = low.length ? `<i class="ti ti-alert-triangle"></i> <b>${low.length}</b> employee${low.length > 1 ? 's are' : ' is'} below 60% of target<div class="names">${low.slice(0, 8).map(r => `<span title="${esc(pctLabel(r.achievementPercent))}">${esc(r.name)} &middot; ${esc(pctLabel(r.achievementPercent))}</span>`).join('')}${low.length > 8 ? `<span>+${low.length - 8} more</span>` : ''}</div>` : '';
                }

                renderEmpChart();
                visitData = (data.charts || {}).visits || null;
                renderVisits();
                // Employee-wise visits: keeps its own date, follows zone; "Selected period" tracks the top filter
                const f = data.filter || {};
                if (f.fromdate && f.todate) ev.period = { from: f.fromdate, to: f.todate };
                if (ev.mode === 'period') setEvRange('period');
                loadEmpVisits();
            };
            document.querySelectorAll('[data-emp-sort]').forEach(btn => btn.addEventListener('click', () => {
                document.querySelectorAll('[data-emp-sort]').forEach(b => b.classList.toggle('active', b === btn));
                empSort = btn.dataset.empSort;
                if (empRows.length) renderEmpChart();
            }));
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
