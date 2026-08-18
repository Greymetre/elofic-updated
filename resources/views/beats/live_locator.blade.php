<x-app-layout>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18" type="text/javascript"></script>
    <style>
        .live-locator-shell { display:flex; height:calc(100vh - 260px); min-height:520px; overflow:hidden; border:1px solid #e3e8f0; border-radius:8px; background:#fff; }
        .live-users-panel { flex:0 0 330px; width:330px; overflow:hidden; border-right:1px solid #e3e8f0; background:#fbfcfe; }
        .live-users-head { padding:14px 16px; border-bottom:1px solid #e3e8f0; }
        .live-users-title-row { display:flex; align-items:center; justify-content:space-between; gap:10px; }
        .live-users-title { margin:0; color:#3860a4; font-size:12px; font-weight:700; letter-spacing:.6px; text-transform:uppercase; }
        .live-users-count { padding:4px 8px; border-radius:6px; background:linear-gradient(45deg,#3860a4 0%,#3694cc 100%); color:#fff; font-size:10px; font-weight:600; white-space:nowrap; }
        .live-users-search-wrap { position:relative; margin-top:12px; }
        .live-users-search-wrap .material-icons { position:absolute; z-index:2; top:50%; left:9px; transform:translateY(-50%); color:#8b9ab3; font-size:16px; pointer-events:none; }
        .live-locator-shell .live-users-search, .live-locator-shell .live-users-zone-filter { width:100%; height:36px; padding:0 8px; border:1px solid #dbe2ec; border-radius:6px; background:#fff; color:#3c4858; font-size:12px; }
        .live-locator-shell .live-users-search { padding-left:30px; }
        .live-locator-shell .live-users-search:focus, .live-locator-shell .live-users-zone-filter:focus { border-color:#3694cc; outline:0; }
        .live-users-zone-wrap { margin-top:8px; }
        .live-users-filters { display:flex; flex-wrap:wrap; gap:5px; margin-top:10px; }
        /* Material's global .btn rules win on colour, so the chips state their own. */
        .live-locator-shell .live-users-filters .btn.live-user-filter {
            margin:0 !important; padding:5px 11px !important; border:1px solid #d3dced !important; border-radius:20px !important;
            background:#fff !important; background-image:none !important; color:#55637d !important;
            font-size:10px !important; font-weight:600 !important; line-height:1.6 !important; text-transform:none !important;
            letter-spacing:.2px !important; box-shadow:none !important; opacity:1 !important; transition:.18s ease;
        }
        .live-locator-shell .live-users-filters .btn.live-user-filter:hover,
        .live-locator-shell .live-users-filters .btn.live-user-filter:focus {
            border-color:#9dbfe0 !important; background:#eef4fb !important; background-image:none !important; color:#2f4f83 !important; box-shadow:none !important;
        }
        .live-locator-shell .live-users-filters .btn.live-user-filter.active,
        .live-locator-shell .live-users-filters .btn.live-user-filter.active:hover,
        .live-locator-shell .live-users-filters .btn.live-user-filter.active:focus {
            border-color:transparent !important;
            background:linear-gradient(45deg,#3860a4 0%,#3694cc 100%) !important;
            color:#fff !important; box-shadow:0 2px 5px rgba(56,96,164,.28) !important;
        }
        .live-users-list { height:calc(100% - 158px); padding:8px; overflow-y:auto; }
        .live-user-row { display:flex; align-items:flex-start; gap:10px; margin-bottom:5px; padding:9px 10px; border:1px solid transparent; border-radius:6px; cursor:pointer; transition:.18s ease; }
        .live-user-row:hover, .live-user-row.active { border-color:#cfe0f2; background:#eef4fb; }
        .live-user-avatar { display:grid; place-items:center; flex:0 0 34px; width:34px; height:34px; border-radius:50%; background:linear-gradient(45deg,#3860a4 0%,#3694cc 100%); color:#fff; font-size:11px; font-weight:600; }
        .live-user-copy { flex:1; min-width:0; }
        .live-user-name { overflow:hidden; color:#3c4858; font-size:12px; font-weight:600; line-height:1.3; text-overflow:ellipsis; white-space:nowrap; }
        .live-user-role, .live-user-distance { overflow:hidden; margin-top:2px; color:#8b9ab3; font-size:10px; line-height:1.3; text-overflow:ellipsis; white-space:nowrap; }
        .live-user-status { display:inline-block; margin-top:5px; padding:2px 7px; border-radius:20px; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; }
        .live-user-status.is-online { background:#e3f7ea; color:#1b8a4b; }
        .live-user-status.is-offline { background:#fdeaee; color:#c62a48; }
        .live-user-status.is-gps-off { background:#eef0f4; color:#77839a; }
        .live-users-empty, .live-map-state { padding:40px 12px; color:#8b9ab3; font-size:12px; text-align:center; }
        .live-map-state { display:grid; place-items:center; height:100%; }
        .live-map-column { flex:1 1 auto; min-width:0; }
        #liveLocatorMap { width:100%; height:100%; background:#eaeef4; }
        .live-user-popup { box-sizing:border-box; width:290px; padding:4px 26px 4px 4px; text-align:left; }
        .live-user-popup-head { display:flex; align-items:center; gap:10px; }
        .live-user-popup-avatar { display:grid; place-items:center; flex:0 0 36px; width:36px; height:36px; border-radius:50%; background:linear-gradient(45deg,#3860a4 0%,#3694cc 100%); color:#fff; font-size:12px; font-weight:600; }
        .live-user-popup-name { color:#3c4858; font-size:13px; font-weight:700; line-height:1.3; }
        .live-user-popup-role { margin-top:1px; color:#8b9ab3; font-size:11px; }
        .live-user-popup-status { display:inline-block; margin-top:4px; padding:2px 7px; border-radius:20px; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; }
        .live-user-popup-status.is-online { background:#e3f7ea; color:#1b8a4b; }
        .live-user-popup-status.is-offline { background:#fdeaee; color:#c62a48; }
        .live-user-popup-status.is-gps-off { background:#eef0f4; color:#77839a; }
        .live-user-popup-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:12px; padding-top:10px; border-top:1px solid #e8ecf3; }
        .live-user-popup-label { color:#8b9ab3; font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
        .live-user-popup-value { margin-top:2px; color:#3c4858; font-size:11px; font-weight:700; }
        .live-user-popup-value.is-cyan { color:#3694cc; }
        .live-user-popup-value.is-green { color:#1b8a4b; }
        .live-user-popup-foot { margin-top:10px; padding-top:8px; border-top:1px solid #e8ecf3; color:#5b6b83; font-size:10px; line-height:1.55; overflow-wrap:anywhere; }
        @media(max-width:991px){
            .live-locator-shell { height:auto; min-height:0; flex-wrap:wrap; }
            .live-users-panel { flex:0 0 100%; width:100%; height:380px; border-right:0; border-bottom:1px solid #e3e8f0; }
            .live-map-column { flex:0 0 100%; height:420px; }
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon card-header-theme">
                    <div class="card-icon">
                        <i class="material-icons">share_location</i>
                    </div>
                    <h4 class="card-title">User Live Locator</h4>
                </div>
                <div class="card-body">
                    <section class="live-locator-shell">
                        <aside class="live-users-panel">
                            <div class="live-users-head">
                                <div class="live-users-title-row">
                                    <h3 class="live-users-title">Field team</h3>
                                    <span class="live-users-count" id="liveUsersCount">0 records</span>
                                </div>
                                <div class="live-users-search-wrap">
                                    <i class="material-icons">search</i>
                                    <input type="search" class="live-users-search" id="liveUsersSearch" placeholder="Search employee..." autocomplete="off">
                                </div>
                                <div class="live-users-zone-wrap">
                                    <select class="live-users-zone-filter" id="liveUsersZoneFilter" aria-label="Filter employees by division">
                                        <option value="all">All divisions</option>
                                    </select>
                                </div>
                                <div class="live-users-filters">
                                    <button type="button" class="btn live-user-filter active" data-status="all">All</button>
                                    <button type="button" class="btn live-user-filter" data-status="Online">Online</button>
                                    <button type="button" class="btn live-user-filter" data-status="Offline">Offline</button>
                                    <button type="button" class="btn live-user-filter" data-status="GPS Off">GPS Off</button>
                                </div>
                            </div>
                            <div class="live-users-list" id="liveUsersList"></div>
                        </aside>
                        <div class="live-map-column">
                            <div id="liveLocatorMap"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var liveUsersMap = null;
        var liveUsersInfoWindow = null;
        var liveUserLocations = [];
        var liveUserStatusFilter = 'all';
        var liveUserZoneFilter = 'all';

        $(document).ready(function () {
            $('#loader').hide();
            $('#liveUsersSearch').on('input', applyLiveUserFilters);
            $('#liveUsersZoneFilter').on('change', function () {
                liveUserZoneFilter = $(this).val() || 'all';
                applyLiveUserFilters();
            });
            $('.live-user-filter').on('click', function () {
                liveUserStatusFilter = $(this).data('status');
                $('.live-user-filter').removeClass('active');
                $(this).addClass('active');
                applyLiveUserFilters();
            });
            getAllUsersLiveLocations();
        });

        function getAllUsersLiveLocations() {
            $.ajax({
                url: "{{ route('livelocation.all-users') }}",
                dataType: 'json',
                type: 'GET',
                success: function (response) {
                    renderAllUsersMap(response.locations || []);
                },
                error: function () {
                    $('#liveLocatorMap').html('<div class="live-map-state">Unable to load live locations. Please try again.</div>');
                }
            });
        }

        function renderAllUsersMap(locations) {
            liveUserLocations = locations;
            populateLiveUserZoneFilter(locations);

            if (!locations.length) {
                $('#liveLocatorMap').html('<div class="live-map-state">No user locations have been reported today.</div>');
                $('#liveUsersList').html('<div class="live-users-empty">No employees have reported a location today.</div>');
                $('#liveUsersCount').text('0 records');
                return;
            }

            var mappedLocations = locations.filter(function (location) {
                var lat = parseFloat(location.latitude);
                var lng = parseFloat(location.longitude);
                return isFinite(lat) && isFinite(lng);
            });

            liveUsersMap = new google.maps.Map(document.getElementById('liveLocatorMap'), {
                zoom: 5,
                center: { lat: 20.5937, lng: 78.9629 },
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: false,
                streetViewControl: false
            });
            liveUsersInfoWindow = new google.maps.InfoWindow();
            var bounds = new google.maps.LatLngBounds();

            mappedLocations.forEach(function (location) {
                var position = { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) };
                bounds.extend(position);
                location.marker = new google.maps.Marker({
                    map: liveUsersMap,
                    position: position,
                    title: location.name || 'User',
                    icon: makeLiveUserMarkerIcon(location.status)
                });
                location.marker.addListener('click', function () {
                    focusLiveUser(location.user_id, false);
                });
            });

            renderLiveUsersList(liveUserLocations);
            if (!mappedLocations.length) {
                $('#liveLocatorMap').html('<div class="live-map-state">No valid user locations are available.</div>');
            } else if (mappedLocations.length === 1) {
                liveUsersMap.setCenter(bounds.getCenter());
                liveUsersMap.setZoom(14);
            } else {
                liveUsersMap.fitBounds(bounds, 50);
            }
        }

        function makeLiveUserMarkerIcon(status) {
            var online = status === 'Online';
            var color = online ? '#22a55b' : (status === 'Offline' ? '#e2566f' : '#98a3b6');
            var stroke = online ? '#15803d' : (status === 'Offline' ? '#be123c' : '#6b7688');
            var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="40" viewBox="0 0 30 40">' +
                '<defs><filter id="s" x="-35%" y="-20%" width="170%" height="165%"><feDropShadow dx="1" dy="2" stdDeviation="1.2" flood-color="#1b2b45" flood-opacity=".42"/></filter></defs>' +
                '<path filter="url(#s)" d="M15 1.25A13.25 13.25 0 0 0 1.75 14.5C1.75 24.1 15 38.5 15 38.5S28.25 24.1 28.25 14.5A13.25 13.25 0 0 0 15 1.25Z" fill="' + color + '" stroke="' + stroke + '" stroke-width="1.5"/>' +
                '<circle cx="15" cy="14.3" r="4.6" fill="#fff" stroke="' + stroke + '" stroke-width="1.2"/></svg>';
            return {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(22, 29),
                anchor: new google.maps.Point(11, 28)
            };
        }

        function populateLiveUserZoneFilter(locations) {
            var currentZone = $('#liveUsersZoneFilter').val() || 'all';
            var zones = locations.map(function (location) { return (location.division || '').trim(); })
                .filter(Boolean)
                .filter(function (zone, index, values) { return values.indexOf(zone) === index; })
                .sort(function (first, second) { return first.localeCompare(second); });
            var $zoneFilter = $('#liveUsersZoneFilter').empty().append($('<option>', { value: 'all', text: 'All divisions' }));
            zones.forEach(function (zone) {
                $zoneFilter.append($('<option>', { value: zone, text: zone }));
            });
            liveUserZoneFilter = zones.indexOf(currentZone) !== -1 ? currentZone : 'all';
            $zoneFilter.val(liveUserZoneFilter);
        }

        function getLiveUserInitials(name) {
            return (name || 'User').split(/\s+/).slice(0, 2).map(function (part) {
                return part.charAt(0).toUpperCase();
            }).join('');
        }

        function liveStatusClass(status) {
            return 'is-' + String(status || 'GPS Off').toLowerCase().replace(/\s+/g, '-');
        }

        function renderLiveUsersList(locations) {
            var $list = $('#liveUsersList').empty();
            $('#liveUsersCount').text(locations.length + (locations.length === 1 ? ' record' : ' records'));
            if (!locations.length) {
                $list.append('<div class="live-users-empty">No employees match the current filter.</div>');
                return;
            }

            locations.forEach(function (location) {
                var $row = $('<div>', { class: 'live-user-row', 'data-user-id': location.user_id });
                var $copy = $('<div>', { class: 'live-user-copy' }).append(
                    $('<div>', { class: 'live-user-name', text: location.name || 'Unknown user' }),
                    $('<div>', { class: 'live-user-role', text: location.designation || 'Field employee' }),
                    $('<div>', { class: 'live-user-distance', text: Number(location.distance_km || 0).toFixed(1) + ' km travelled today' }),
                    $('<div>', { class: 'live-user-status ' + liveStatusClass(location.status), text: location.status || 'GPS Off' })
                );
                $row.append($('<div>', { class: 'live-user-avatar', text: getLiveUserInitials(location.name) }), $copy)
                    .on('click', function () { focusLiveUser(location.user_id, true); });
                $list.append($row);
            });
        }

        function focusLiveUser(userId, moveMap) {
            var location = liveUserLocations.find(function (item) {
                return String(item.user_id) === String(userId);
            });
            if (!location) return;

            $('.live-user-row').removeClass('active');
            $('.live-user-row[data-user-id="' + userId + '"]').addClass('active');
            if (!location.marker || !liveUsersMap) {
                showLiveLocationAlert('GPS location is not available for this user.');
                return;
            }
            if (moveMap) {
                liveUsersMap.panTo(location.marker.getPosition());
                liveUsersMap.setZoom(14);
            }
            liveUsersInfoWindow.setContent(buildLiveUserPopup(location));
            liveUsersInfoWindow.open(liveUsersMap, location.marker);
        }

        function showLiveLocationAlert(message) {
            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({ icon: 'warning', title: 'Location unavailable', text: message, confirmButtonText: 'OK' });
            } else {
                window.alert(message);
            }
        }

        function buildLiveUserPopup(location) {
            var popup = $('<div>', { class: 'live-user-popup' });
            var head = $('<div>', { class: 'live-user-popup-head' });
            head.append($('<div>', { class: 'live-user-popup-avatar', text: getLiveUserInitials(location.name) }));
            var identity = $('<div>');
            identity.append($('<div>', {
                class: 'live-user-popup-name',
                text: (location.name || 'User') + (location.employee_code ? ' (' + location.employee_code + ')' : '')
            }));
            identity.append($('<div>', { class: 'live-user-popup-role', text: location.designation || 'Field employee' }));
            identity.append($('<div>', { class: 'live-user-popup-status ' + liveStatusClass(location.status), text: location.status || 'GPS Off' }));
            head.append(identity);
            popup.append(head);

            var grid = $('<div>', { class: 'live-user-popup-grid' });
            appendPopupMetric(grid, "Today's Plan", location.today_plan || 'No plan assigned', '');
            appendPopupMetric(grid, 'Total KM Run', Number(location.distance_km || 0).toFixed(1) + ' km', 'is-cyan');
            appendPopupMetric(grid, 'Customer Visits', String(location.visits_today || 0), '');
            appendPopupMetric(grid, "Today's Order Value", '₹' + Number(location.order_value || 0).toLocaleString('en-IN'), 'is-green');
            popup.append(grid);

            var foot = $('<div>', { class: 'live-user-popup-foot' });
            foot.append($('<div>', { text: 'Last update: ' + (location.time || 'Unknown') }));
            if (location.mobile) foot.append($('<div>', { text: location.mobile }));
            foot.append($('<div>', { text: location.address || 'Address unavailable' }));
            popup.append(foot);
            return popup.get(0);
        }

        function appendPopupMetric(container, label, value, valueClass) {
            var metric = $('<div>', { class: 'live-user-popup-metric' });
            metric.append($('<div>', { class: 'live-user-popup-label', text: label }));
            metric.append($('<div>', { class: 'live-user-popup-value ' + valueClass, text: value }));
            container.append(metric);
        }

        function applyLiveUserFilters() {
            var query = ($('#liveUsersSearch').val() || '').toLowerCase().trim();
            var visible = liveUserLocations.filter(function (location) {
                var matchesStatus = liveUserStatusFilter === 'all' || location.status === liveUserStatusFilter;
                var matchesZone = liveUserZoneFilter === 'all' || location.division === liveUserZoneFilter;
                var haystack = [location.name, location.employee_code, location.designation, location.branch, location.division, location.address]
                    .filter(Boolean).join(' ').toLowerCase();
                return matchesStatus && matchesZone && (!query || haystack.indexOf(query) !== -1);
            });

            var visibleIds = visible.map(function (location) { return String(location.user_id); });
            liveUserLocations.forEach(function (location) {
                if (location.marker) {
                    location.marker.setMap(visibleIds.indexOf(String(location.user_id)) !== -1 ? liveUsersMap : null);
                }
            });

            renderLiveUsersList(visible);
            if (liveUsersInfoWindow) liveUsersInfoWindow.close();

            var visibleMapped = visible.filter(function (location) { return location.marker; });
            if (liveUsersMap && visibleMapped.length === 1) {
                liveUsersMap.setCenter(visibleMapped[0].marker.getPosition());
                liveUsersMap.setZoom(14);
            } else if (liveUsersMap && visibleMapped.length > 1) {
                var filteredBounds = new google.maps.LatLngBounds();
                visibleMapped.forEach(function (location) { filteredBounds.extend(location.marker.getPosition()); });
                liveUsersMap.fitBounds(filteredBounds, 50);
            }
        }
    </script>
</x-app-layout>
