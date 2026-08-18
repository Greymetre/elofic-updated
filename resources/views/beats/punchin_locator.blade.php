<x-app-layout>
    @php($isCustomerLocator = ($locatorMode ?? 'punch') === 'customer')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18" type="text/javascript"></script>
    <style>
        .punch-locator-shell { display:flex; height:calc(100vh - 260px); min-height:520px; overflow:hidden; border:1px solid #e3e8f0; border-radius:8px; background:#fff; }
        .punch-locator-sidebar { flex:0 0 320px; width:320px; overflow:hidden; border-right:1px solid #e3e8f0; background:#fbfcfe; }
        .punch-locator-head { padding:14px 16px; border-bottom:1px solid #e3e8f0; }
        .punch-locator-title-row { display:flex; align-items:center; justify-content:space-between; gap:10px; }
        .punch-locator-title { margin:0; color:#3860a4; font-size:12px; font-weight:700; letter-spacing:.6px; text-transform:uppercase; }
        .punch-locator-count { padding:4px 8px; border-radius:6px; background:linear-gradient(45deg,#3860a4 0%,#3694cc 100%); color:#fff; font-size:10px; font-weight:600; white-space:nowrap; }
        .punch-filter-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:12px; }
        .punch-search-wrap { position:relative; }
        .punch-search-wrap .material-icons { position:absolute; z-index:2; top:50%; left:9px; transform:translateY(-50%); color:#8b9ab3; font-size:16px; pointer-events:none; }
        .punch-locator-shell .punch-search, .punch-locator-shell .punch-zone { width:100%; height:36px; padding:0 8px; border:1px solid #dbe2ec; border-radius:6px; background:#fff; color:#3c4858; font-size:12px; }
        .punch-locator-shell .punch-search { padding-left:30px; }
        .punch-locator-shell .punch-search:focus, .punch-locator-shell .punch-zone:focus { border-color:#3694cc; outline:0; }
        .punch-list { height:calc(100% - 100px); padding:8px; overflow-y:auto; }
        .punch-user { display:flex; align-items:flex-start; gap:10px; margin-bottom:5px; padding:9px 10px; border:1px solid transparent; border-radius:6px; cursor:pointer; transition:.18s ease; }
        .punch-user:hover, .punch-user.active { border-color:#cfe0f2; background:#eef4fb; }
        .punch-avatar { display:grid; place-items:center; flex:0 0 34px; width:34px; height:34px; border-radius:50%; background:linear-gradient(45deg,#3860a4 0%,#3694cc 100%); color:#fff; font-size:11px; font-weight:600; }
        .punch-copy { flex:1; min-width:0; }
        .punch-name { overflow:hidden; color:#3c4858; font-size:12px; font-weight:600; line-height:1.3; text-overflow:ellipsis; white-space:nowrap; }
        .punch-role, .punch-address { overflow:hidden; margin-top:2px; color:#8b9ab3; font-size:10px; line-height:1.3; text-overflow:ellipsis; white-space:nowrap; }
        .punch-time { margin-top:3px; color:#3694cc; font-size:10px; font-weight:600; }
        .punch-empty { padding:40px 12px; color:#8b9ab3; font-size:12px; text-align:center; }
        .punch-map-wrap { flex:1 1 auto; min-width:0; }
        #punchInMap { width:100%; height:100%; background:#eaeef4; }
        .punch-popup { box-sizing:border-box; width:250px; padding:4px 26px 4px 4px; text-align:left; }
        .punch-popup-name { color:#3c4858; font-size:13px; font-weight:700; line-height:1.3; }
        .punch-popup-role { margin-top:2px; color:#8b9ab3; font-size:11px; }
        .punch-popup-address { margin-top:8px; color:#5b6b83; font-size:11px; line-height:1.45; overflow-wrap:anywhere; }
        .punch-popup-time { margin-top:6px; color:#3694cc; font-size:11px; font-weight:700; }
        @media(max-width:991px){
            .punch-locator-shell { height:auto; min-height:0; flex-wrap:wrap; }
            .punch-locator-sidebar { flex:0 0 100%; width:100%; height:340px; border-right:0; border-bottom:1px solid #e3e8f0; }
            .punch-map-wrap { flex:0 0 100%; height:420px; }
        }
        @media(max-width:575px){
            .punch-filter-grid { grid-template-columns:1fr; }
            .punch-list { height:calc(100% - 144px); }
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon card-header-theme">
                    <div class="card-icon">
                        <i class="material-icons">{{ $isCustomerLocator ? 'pin_drop' : 'where_to_vote' }}</i>
                    </div>
                    <h4 class="card-title">{{ $isCustomerLocator ? 'Customer Locator' : 'User Punch-In Locator' }}</h4>
                </div>
                <div class="card-body">
                    <section class="punch-locator-shell">
                        <aside class="punch-locator-sidebar">
                            <div class="punch-locator-head">
                                <div class="punch-locator-title-row">
                                    <h2 class="punch-locator-title">{{ $isCustomerLocator ? 'Check-ins today' : 'Punch-ins today' }}</h2>
                                    <span class="punch-locator-count" id="punchCount">0 records</span>
                                </div>
                                <div class="punch-filter-grid">
                                    <div class="punch-search-wrap">
                                        <i class="material-icons">search</i>
                                        <input type="search" class="punch-search" id="punchSearch" placeholder="{{ $isCustomerLocator ? 'Search customer...' : 'Search user...' }}" autocomplete="off">
                                    </div>
                                    <select class="punch-zone" id="punchZone" aria-label="Filter records by division">
                                        <option value="all">All divisions</option>
                                    </select>
                                </div>
                            </div>
                            <div class="punch-list" id="punchList"></div>
                        </aside>
                        <div class="punch-map-wrap">
                            <div id="punchInMap"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script>
        var punchIns = @json($punchIns);
        var isCustomerLocator = @json($isCustomerLocator);
        var punchMap;
        var punchInfoWindow;

        $(document).ready(function () {
            populatePunchZones();
            $('#punchSearch').on('input', applyPunchFilters);
            $('#punchZone').on('change', applyPunchFilters);
            initialisePunchMap();
        });

        function initials(name) {
            return (name || 'User').split(/\s+/).slice(0, 2).map(function (part) {
                return part.charAt(0).toUpperCase();
            }).join('');
        }

        function populatePunchZones() {
            var zones = punchIns.map(function (item) {
                return (item.zone || '').trim();
            }).filter(Boolean).filter(function (zone, index, all) {
                return all.indexOf(zone) === index;
            }).sort();
            var $zone = $('#punchZone');
            zones.forEach(function (zone) {
                $zone.append($('<option>', { value: zone, text: zone }));
            });
        }

        function punchMarkerIcon() {
            var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="40" viewBox="0 0 30 40"><defs><filter id="s" x="-35%" y="-20%" width="170%" height="165%"><feDropShadow dx="1" dy="2" stdDeviation="1.2" flood-color="#1b2b45" flood-opacity=".4"/></filter></defs><path filter="url(#s)" d="M15 1.25A13.25 13.25 0 0 0 1.75 14.5C1.75 24.1 15 38.5 15 38.5S28.25 24.1 28.25 14.5A13.25 13.25 0 0 0 15 1.25Z" fill="#3694cc" stroke="#3860a4" stroke-width="1.5"/><circle cx="15" cy="14.3" r="4.6" fill="#fff" stroke="#3860a4" stroke-width="1.2"/></svg>';
            return {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(24, 32),
                anchor: new google.maps.Point(12, 31)
            };
        }

        function initialisePunchMap() {
            punchMap = new google.maps.Map(document.getElementById('punchInMap'), {
                zoom: 5,
                center: { lat: 20.5937, lng: 78.9629 },
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true
            });
            punchInfoWindow = new google.maps.InfoWindow();
            var bounds = new google.maps.LatLngBounds();
            punchIns.forEach(function (item) {
                var position = { lat: Number(item.latitude), lng: Number(item.longitude) };
                item.marker = new google.maps.Marker({
                    map: punchMap,
                    position: position,
                    title: item.name,
                    icon: punchMarkerIcon()
                });
                bounds.extend(position);
                item.marker.addListener('click', function () {
                    focusPunch(item.attendance_id, false);
                });
            });
            renderPunchList(punchIns);
            fitPunchBounds(punchIns, bounds);
        }

        function fitPunchBounds(items, suppliedBounds) {
            if (!punchMap || !items.length) return;
            if (items.length === 1) {
                punchMap.setCenter(items[0].marker.getPosition());
                punchMap.setZoom(14);
                return;
            }
            var bounds = suppliedBounds || new google.maps.LatLngBounds();
            if (!suppliedBounds) {
                items.forEach(function (item) {
                    if (item.marker) bounds.extend(item.marker.getPosition());
                });
            }
            punchMap.fitBounds(bounds, 45);
        }

        function renderPunchList(items) {
            var $list = $('#punchList').empty();
            $('#punchCount').text(items.length + (items.length === 1 ? ' record' : ' records'));
            if (!items.length) {
                $list.append($('<div>', { class: 'punch-empty', text: isCustomerLocator ? 'No customer check-ins match the current filters.' : 'No punch-ins match the current filters.' }));
                return;
            }
            items.forEach(function (item) {
                var $row = $('<div>', { class: 'punch-user', 'data-id': item.attendance_id });
                var $copy = $('<div>', { class: 'punch-copy' }).append(
                    $('<div>', { class: 'punch-name', text: item.name }),
                    $('<div>', { class: 'punch-role', text: item.designation }),
                    $('<div>', { class: 'punch-address', text: item.address }),
                    $('<div>', { class: 'punch-time', text: (isCustomerLocator ? 'Checked in ' : 'Punched in ') + item.time })
                );
                if (isCustomerLocator && item.representative) {
                    $copy.append($('<div>', { class: 'punch-role', text: item.representative + ' \u00b7 ' + item.representative_role }));
                }
                $row.append($('<div>', { class: 'punch-avatar', text: initials(item.name) }), $copy)
                    .on('click', function () { focusPunch(item.attendance_id, true); });
                $list.append($row);
            });
        }

        function focusPunch(id, moveMap) {
            var item = punchIns.find(function (record) {
                return String(record.attendance_id) === String(id);
            });
            if (!item || !item.marker) return;
            $('.punch-user').removeClass('active');
            $('.punch-user[data-id="' + id + '"]').addClass('active');
            if (moveMap) {
                punchMap.panTo(item.marker.getPosition());
                punchMap.setZoom(14);
            }
            var popup = $('<div>', { class: 'punch-popup' }).append(
                $('<div>', { class: 'punch-popup-name', text: item.name }),
                $('<div>', { class: 'punch-popup-role', text: item.designation }),
                $('<div>', { class: 'punch-popup-address', text: item.address }),
                $('<div>', { class: 'punch-popup-time', text: (isCustomerLocator ? 'Checked in at ' : 'Punched in at ') + item.time })
            );
            if (isCustomerLocator && item.representative) {
                popup.append($('<div>', { class: 'punch-popup-role', text: item.representative + ' \u00b7 ' + item.representative_role }));
            }
            punchInfoWindow.setContent(popup.get(0));
            punchInfoWindow.open(punchMap, item.marker);
        }

        function applyPunchFilters() {
            var query = ($('#punchSearch').val() || '').toLowerCase().trim();
            var zone = $('#punchZone').val() || 'all';
            var visible = punchIns.filter(function (item) {
                var text = [item.name, item.employee_code, item.designation, item.address, item.zone].filter(Boolean).join(' ').toLowerCase();
                return (zone === 'all' || item.zone === zone) && (!query || text.indexOf(query) !== -1);
            });
            var ids = visible.map(function (item) { return String(item.attendance_id); });
            punchIns.forEach(function (item) {
                item.marker.setMap(ids.indexOf(String(item.attendance_id)) !== -1 ? punchMap : null);
            });
            punchInfoWindow.close();
            renderPunchList(visible);
            fitPunchBounds(visible);
        }
    </script>
</x-app-layout>
