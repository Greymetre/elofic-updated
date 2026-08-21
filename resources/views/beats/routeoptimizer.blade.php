<x-app-layout>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18" type="text/javascript"></script>
    <style>
        .beat-route-page .route-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }
        .beat-route-page .route-title { margin: 0; font-size: 20px; font-weight: 500; color: #3c4858; }
        .beat-route-page .route-subtitle { margin: 6px 0 0; color: #8a97a8; font-size: 12px; }

        .beat-route-page .route-stats { display: flex; gap: 10px; flex-wrap: wrap; }
        .beat-route-page .route-stat {
            min-width: 104px;
            padding: 10px 16px;
            border: 1px solid rgba(0, 0, 0, .09);
            border-radius: 12px;
            background: #fff;
            text-align: center;
        }
        .beat-route-page .route-stat b {
            display: block;
            color: #3c4858;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.2;
        }
        .beat-route-page .route-stat span {
            display: block;
            margin-top: 2px;
            color: #8a97a8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .6px;
        }

        .beat-route-page .route-filter-form {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2fr) auto;
            align-items: end;
            gap: 12px;
            margin-bottom: 18px;
        }
        @media (max-width: 767px) {
            .beat-route-page .route-filter-form { grid-template-columns: minmax(0, 1fr); }
        }
        .beat-route-page .route-filter-form .form-group { margin: 0 !important; padding: 0 !important; }
        .beat-route-page .route-filter-form label {
            display: block;
            margin-bottom: 6px;
            color: #8a97a8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
        }
        .beat-route-page .route-filter-form .form-control,
        .beat-route-page .route-filter-form .select2-container--default .select2-selection--single {
            height: 42px !important;
            padding: 0 14px !important;
            border: 1px solid rgba(0, 0, 0, .16) !important;
            border-radius: 8px !important;
            background: #fff !important;
            color: #3c4858 !important;
            font-size: 13px !important;
            line-height: 42px !important;
            box-shadow: none !important;
        }
        .beat-route-page .route-filter-form .select2-container { width: 100% !important; }
        .beat-route-page .route-filter-form .select2-container--default .select2-selection--single .select2-selection__rendered {
            height: 42px !important;
            padding: 0 !important;
            color: #3c4858 !important;
            font-size: 13px !important;
            line-height: 42px !important;
        }
        .beat-route-page .route-filter-form .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px !important; }
        .beat-route-page .route-build-btn { height: 42px; margin: 0; padding: 0 22px; white-space: nowrap; }
        .beat-route-page .route-build-btn:disabled { opacity: .6; }

        .beat-route-page .route-workspace {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            gap: 16px;
            height: calc(100vh - 360px);
            min-height: 520px;
        }
        @media (max-width: 991px) {
            .beat-route-page .route-workspace { grid-template-columns: minmax(0, 1fr); height: auto; }
            .beat-route-page .route-sequence, .beat-route-page .route-map-wrap { height: 460px; }
        }

        .beat-route-page .route-sequence,
        .beat-route-page .route-map-wrap {
            border: 1px solid rgba(0, 0, 0, .09);
            border-radius: 12px;
            background: #fbfcfe;
            overflow: hidden;
        }
        .beat-route-page .route-sequence { display: flex; flex-direction: column; }
        .beat-route-page .route-sequence-head {
            padding: 16px 18px;
            border-bottom: 1px solid rgba(0, 0, 0, .09);
            background: #fff;
        }
        .beat-route-page .route-sequence-head h3 { margin: 0; color: #3c4858; font-size: 15px; font-weight: 700; }
        .beat-route-page .route-sequence-head p { margin: 4px 0 0; color: #8a97a8; font-size: 11px; line-height: 1.5; }
        .beat-route-page .route-sequence-list { flex: 1 1 auto; padding: 12px; overflow-y: auto; }

        .beat-route-page .route-stop {
            display: flex;
            gap: 12px;
            width: 100%;
            margin-bottom: 10px;
            padding: 12px;
            border: 1px solid rgba(0, 0, 0, .09);
            border-radius: 10px;
            background: #fff;
            text-align: left;
            cursor: pointer;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .beat-route-page .route-stop:hover,
        .beat-route-page .route-stop.is-active {
            border-color: #00acc1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }
        .beat-route-page .route-stop-index {
            flex: 0 0 28px;
            height: 28px;
            border-radius: 50%;
            border: 1px solid rgba(0, 0, 0, .14);
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            line-height: 26px;
            text-align: center;
        }
        .beat-route-page .route-stop-body { flex: 1 1 auto; min-width: 0; }
        .beat-route-page .route-stop-title {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
        }
        .beat-route-page .route-stop-name {
            color: #3c4858;
            font-size: 13px;
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .beat-route-page .route-stop-km { flex: 0 0 auto; color: #6b7280; font-size: 11px; white-space: nowrap; }
        .beat-route-page .route-stop-meta {
            margin: 4px 0 0;
            color: #8a97a8;
            font-size: 11px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .beat-route-page .route-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 10px;
            border: 1px solid transparent;
            border-radius: 6px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .6px;
        }
        .beat-route-page .route-badge.is-visited { border-color: rgba(22, 163, 74, .35); background: rgba(22, 163, 74, .10); color: #15803d; }
        .beat-route-page .route-badge.is-overdue { border-color: rgba(220, 38, 38, .35); background: rgba(220, 38, 38, .10); color: #b91c1c; }
        .beat-route-page .route-badge.is-followup { border-color: rgba(217, 119, 6, .35); background: rgba(217, 119, 6, .10); color: #b45309; }
        .beat-route-page .route-badge.is-new { border-color: rgba(8, 145, 178, .35); background: rgba(8, 145, 178, .10); color: #0e7490; }
        .beat-route-page .route-badge.is-routine { border-color: rgba(0, 0, 0, .14); background: rgba(0, 0, 0, .04); color: #6b7280; }

        .beat-route-page .route-map-wrap { position: relative; }
        .beat-route-page #beatRouteMap { width: 100%; height: 100%; }
        .beat-route-page .route-state {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 30px;
            color: #8a97a8;
            font-size: 13px;
            text-align: center;
        }
        .beat-route-page .route-legend {
            position: absolute;
            left: 14px;
            bottom: 14px;
            z-index: 3;
            padding: 10px 12px;
            border: 1px solid rgba(0, 0, 0, .09);
            border-radius: 10px;
            background: rgba(255, 255, 255, .94);
            color: #1f2937;
            font-size: 10px;
            pointer-events: none;
        }
        .beat-route-page .route-legend span { display: flex; align-items: center; margin-top: 4px; }
        .beat-route-page .route-legend span:first-of-type { margin-top: 0; }
        .beat-route-page .route-legend i {
            width: 10px; height: 10px; margin-right: 7px;
            border: 2px solid #fff; border-radius: 50%;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, .25);
        }

        .beat-route-page .gm-style .gm-style-iw-c { padding: 12px !important; border-radius: 10px !important; }
        .beat-route-page .route-info { min-width: 190px; font-size: 12px; color: #1f2937; line-height: 1.55; }
        .beat-route-page .route-info b { display: block; font-size: 13px; }
        .beat-route-page .route-info small { display: block; color: #6b7280; text-transform: uppercase; letter-spacing: .4px; }
        .beat-route-page .route-info span { display: block; }
    </style>

    <div class="row mt-4 beat-route-page">
        <div class="col-lg-12">
            <div class="card mt-4" data-animation="true">
                <div class="card-body">
                    <div class="route-head">
                        <div>
                            <h5 class="route-title">Beat Route Optimized</h5>
                            <p class="route-subtitle" id="routeSubtitle">Select a user and date to build the optimized visit sequence.</p>
                        </div>
                        <div class="route-stats">
                            <div class="route-stat"><b id="statStops">--</b><span>STOPS</span></div>
                            <div class="route-stat"><b id="statKm">--</b><span>ROUTE KM</span></div>
                            <div class="route-stat"><b id="statFinish">--</b><span>EST. FINISH</span></div>
                        </div>
                    </div>

                    <form class="route-filter-form mt-4" id="routeFilterForm" onsubmit="return false;">
                        <div class="form-group">
                            <label for="route_user_id">Beat User</label>
                            <select class="form-control select2" id="route_user_id" name="user_id">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="route_date">Date</label>
                            <input type="date" class="form-control" id="route_date" name="date"
                                value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-info route-build-btn" id="buildRouteBtn">Show Optimized Route</button>
                        </div>
                    </form>

                    <div class="route-workspace">
                        <aside class="route-sequence">
                            <div class="route-sequence-head">
                                <h3>Route Sequence</h3>
                                <p id="routeSequenceHint">Nearest-neighbour order from the user's day start · tap a stop or a map pin for counter details</p>
                            </div>
                            <div class="route-sequence-list" id="routeSequenceList">
                                <div class="route-state">No route yet. Choose a beat user and date above.</div>
                            </div>
                        </aside>
                        <div class="route-map-wrap">
                            <div id="beatRouteMap"><div class="route-state">The optimized beat route will appear here.</div></div>
                            <div class="route-legend" id="routeLegend" style="display:none;">
                                <span><i style="background:#22d3ee"></i> Pending stop</span>
                                <span><i style="background:#22c55e"></i> Visited</span>
                                <span><i style="background:#f87171"></i> Overdue</span>
                                <span><i style="background:#fbbf24"></i> Follow-up due</span>
                                <span><i style="background:#a855f7"></i> Day start</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var beatRouteMap = null;
        var beatRouteMarkers = [];
        var beatRoutePolyline = null;
        var beatRouteDirections = [];
        var beatRouteInfoWindow = null;

        var PRIORITY_COLORS = {
            visited: '#22c55e',
            overdue: '#f87171',
            followup: '#fbbf24',
            new: '#22d3ee',
            routine: '#22d3ee'
        };

        var PRIORITY_STROKES = {
            visited: '#15803d',
            overdue: '#be123c',
            followup: '#b45309',
            new: '#0e7490',
            routine: '#0e7490'
        };

        // teardrop pin sized so the stop number fits inside the white dot
        function makeRouteMarkerIcon(color, stroke) {
            var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="40" viewBox="0 0 30 40">' +
                '<defs><filter id="s" x="-35%" y="-20%" width="170%" height="165%"><feDropShadow dx="1" dy="2" stdDeviation="1.2" flood-color="#020617" flood-opacity=".42"/></filter></defs>' +
                '<path filter="url(#s)" d="M15 1.25A13.25 13.25 0 0 0 1.75 14.5C1.75 24.1 15 38.5 15 38.5S28.25 24.1 28.25 14.5A13.25 13.25 0 0 0 15 1.25Z" fill="' + color + '" stroke="' + stroke + '" stroke-width="1.5"/>' +
                '<circle cx="15" cy="14.3" r="7.2" fill="#fff" stroke="' + stroke + '" stroke-width="1.2"/></svg>';

            return {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                scaledSize: new google.maps.Size(30, 40),
                anchor: new google.maps.Point(15, 39),
                labelOrigin: new google.maps.Point(15, 14.3)
            };
        }

        // the layout initialises every .select2 on ready, so this page only wires
        // up its own controls here
        $(document).ready(function() {
            $('#buildRouteBtn').on('click', buildOptimizedRoute);
            $('#route_user_id, #route_date').on('change', function() {
                $('#routeSubtitle').text('Select a user and date to build the optimized visit sequence.');
            });
        });

        function setRouteState(listMessage, mapMessage) {
            $('#routeSequenceList').html('<div class="route-state"></div>').find('.route-state').text(listMessage);
            $('#beatRouteMap').html('<div class="route-state"></div>').find('.route-state').text(mapMessage);
            $('#routeLegend').hide();
            $('#statStops, #statKm, #statFinish').text('--');
            beatRouteMap = null;
            beatRouteMarkers = [];
        }

        function buildOptimizedRoute() {
            var userId = $('#route_user_id').val();
            var date = $('#route_date').val();

            if (!userId) {
                setRouteState('Select a beat user to continue.', 'Select a beat user to continue.');
                return;
            }
            if (!date) {
                setRouteState('Select a date to continue.', 'Select a date to continue.');
                return;
            }

            $('#buildRouteBtn').prop('disabled', true).text('Building…');
            $('#routeSequenceList').html('<div class="route-state">Building the optimized route…</div>');
            $('#beatRouteMap').html('<div class="route-state">Building the optimized route…</div>');

            $.ajax({
                url: "{{ route('beats.routeOptimizedData') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: userId,
                    date: date
                },
                success: function(response) {
                    if (!response || response.status !== 'success') {
                        var message = (response && response.message) ? response.message : 'Unable to build the route.';
                        setRouteState(message, message);
                        $('#routeSubtitle').text(message);
                        return;
                    }
                    renderRoute(response);
                },
                error: function() {
                    setRouteState('Unable to build the route. Please try again.', 'Unable to build the route. Please try again.');
                },
                complete: function() {
                    $('#buildRouteBtn').prop('disabled', false).text('Show Optimized Route');
                }
            });
        }

        function renderRoute(data) {
            var summary = data.summary || {};

            $('#statStops').text(summary.stops || 0);
            $('#statKm').text((summary.route_km || 0).toFixed ? summary.route_km.toFixed(1) : summary.route_km);
            $('#statFinish').text(summary.finish_time || '--');

            var planNote = data.plan_source === 'assigned'
                ? 'No beat scheduled for this date — showing the beat assigned to the user'
                : 'Beat scheduled for this date';
            $('#routeSubtitle').text(
                data.beat_name + ' · ' + data.user.name + ', ' + data.user.designation +
                ' · ' + data.date + ' · ' + planNote
            );
            $('#routeSequenceHint').text(
                'Optimized from ' + (data.start && data.start.latitude ? data.start.label.toLowerCase() : 'the first counter') +
                ' · ' + (summary.visited || 0) + ' of ' + (summary.stops || 0) + ' already visited · tap a stop or a map pin for counter details'
            );

            renderSequenceList(data.stops || []);
            renderRouteMap(data);
        }

        function renderSequenceList(stops) {
            var $list = $('#routeSequenceList').empty();

            if (!stops.length) {
                $list.html('<div class="route-state">No counter is aligned to this beat.</div>');
                return;
            }

            stops.forEach(function(stop, index) {
                var $item = $('<button>', {
                    type: 'button',
                    class: 'route-stop',
                    'data-index': index
                });

                $item.append($('<span>', { class: 'route-stop-index', text: stop.sequence }));

                var $body = $('<div>', { class: 'route-stop-body' });
                var $title = $('<div>', { class: 'route-stop-title' });
                $title.append($('<span>', { class: 'route-stop-name', text: stop.name }));
                $title.append($('<span>', {
                    class: 'route-stop-km',
                    text: stop.leg_km !== null && stop.leg_km !== undefined ? stop.leg_km + ' km' : 'No GPS'
                }));
                $body.append($title);

                var metaParts = [];
                if (stop.code) metaParts.push(stop.code);
                if (stop.eta) metaParts.push('ETA ' + stop.eta);
                if (stop.visited_at) metaParts.push('Visited ' + stop.visited_at);
                else if (stop.last_visit) metaParts.push('Last visit ' + stop.last_visit);
                $body.append($('<p>', { class: 'route-stop-meta', text: metaParts.join(' · ') || stop.category }));

                $body.append($('<span>', {
                    class: 'route-badge is-' + stop.priority,
                    text: stop.priority_label
                }));

                $item.append($body);
                $item.on('click', function() {
                    focusStop(index);
                });

                $list.append($item);
            });
        }

        function renderRouteMap(data) {
            var stops = (data.stops || []).filter(function(stop) {
                return stop.latitude !== null && stop.longitude !== null;
            });

            if (!stops.length) {
                $('#beatRouteMap').html('<div class="route-state">None of the counters on this beat has GPS coordinates saved, so the route cannot be mapped.</div>');
                $('#routeLegend').hide();
                return;
            }

            $('#beatRouteMap').empty();
            beatRouteMarkers = [];
            beatRouteDirections = [];

            beatRouteMap = new google.maps.Map(document.getElementById('beatRouteMap'), {
                zoom: 12,
                center: { lat: parseFloat(stops[0].latitude), lng: parseFloat(stops[0].longitude) },
                mapTypeId: google.maps.MapTypeId.HYBRID,
                mapTypeControl: false,
                streetViewControl: false
            });
            beatRouteInfoWindow = new google.maps.InfoWindow();

            var bounds = new google.maps.LatLngBounds();
            var path = [];

            // day start marker
            if (data.start && data.start.latitude !== null && data.start.longitude !== null) {
                var startPosition = { lat: parseFloat(data.start.latitude), lng: parseFloat(data.start.longitude) };
                bounds.extend(startPosition);
                path.push(startPosition);

                var startMarker = new google.maps.Marker({
                    position: startPosition,
                    map: beatRouteMap,
                    title: data.start.label,
                    zIndex: 300,
                    icon: makeRouteMarkerIcon('#a855f7', '#6b21a8')
                });
                startMarker.addListener('click', function() {
                    var content = document.createElement('div');
                    content.className = 'route-info';
                    var title = document.createElement('b');
                    title.textContent = data.start.label;
                    content.appendChild(title);
                    if (data.start.address) {
                        var address = document.createElement('span');
                        address.textContent = data.start.address;
                        content.appendChild(address);
                    }
                    beatRouteInfoWindow.setContent(content);
                    beatRouteInfoWindow.open(beatRouteMap, startMarker);
                });
            }

            // route stops, numbered in visit order
            (data.stops || []).forEach(function(stop, index) {
                if (stop.latitude === null || stop.longitude === null) return;

                var position = { lat: parseFloat(stop.latitude), lng: parseFloat(stop.longitude) };
                bounds.extend(position);
                path.push(position);

                var marker = new google.maps.Marker({
                    position: position,
                    map: beatRouteMap,
                    title: stop.sequence + '. ' + stop.name,
                    zIndex: 200,
                    label: {
                        text: String(stop.sequence),
                        color: PRIORITY_STROKES[stop.priority] || '#0e7490',
                        fontSize: '11px',
                        fontWeight: '700'
                    },
                    icon: makeRouteMarkerIcon(
                        PRIORITY_COLORS[stop.priority] || '#22d3ee',
                        PRIORITY_STROKES[stop.priority] || '#0e7490'
                    )
                });

                marker.addListener('click', function() {
                    openStopInfo(index, stop, marker);
                });

                beatRouteMarkers[index] = marker;
            });

            if (path.length > 1) {
                beatRoutePolyline = new google.maps.Polyline({
                    path: path,
                    map: beatRouteMap,
                    strokeColor: '#22d3ee',
                    strokeOpacity: 0,
                    icons: [{
                        icon: { path: 'M 0,-1 0,1', strokeOpacity: .85, scale: 3 },
                        offset: '0',
                        repeat: '14px'
                    }]
                });
            }

            if (!bounds.isEmpty()) {
                if (path.length === 1) {
                    beatRouteMap.setCenter(bounds.getCenter());
                    beatRouteMap.setZoom(14);
                } else {
                    beatRouteMap.fitBounds(bounds, 60);
                }
            }
            $('#routeLegend').show();

            drawRoadRoute(path);
        }

        // Road-snapped overlay on top of the straight sequence line. The
        // Directions API takes at most 25 waypoints per request, so long beats
        // are drawn in chunks and keep the order computed on the server.
        function drawRoadRoute(path) {
            if (path.length < 2) return;

            var directionsService = new google.maps.DirectionsService();
            var chunkSize = 23;

            for (var i = 0; i < path.length - 1; i += chunkSize) {
                var chunk = path.slice(i, i + chunkSize + 1);
                if (chunk.length < 2) continue;

                var renderer = new google.maps.DirectionsRenderer({
                    map: beatRouteMap,
                    suppressMarkers: true,
                    preserveViewport: true,
                    polylineOptions: { strokeColor: '#22d3ee', strokeOpacity: .85, strokeWeight: 4 }
                });
                beatRouteDirections.push(renderer);

                directionsService.route({
                    origin: chunk[0],
                    destination: chunk[chunk.length - 1],
                    waypoints: chunk.slice(1, -1).map(function(point) {
                        return { location: point, stopover: true };
                    }),
                    optimizeWaypoints: false,
                    travelMode: google.maps.TravelMode.DRIVING
                }, (function(renderer) {
                    return function(response, status) {
                        if (status === 'OK') {
                            renderer.setDirections(response);
                        }
                    };
                })(renderer));
            }
        }

        function openStopInfo(index, stop, marker) {
            var content = document.createElement('div');
            content.className = 'route-info';

            var title = document.createElement('b');
            title.textContent = 'Stop ' + stop.sequence + ' · ' + stop.name;
            content.appendChild(title);

            var subtitle = document.createElement('small');
            subtitle.textContent = [stop.code, stop.category].filter(Boolean).join(' · ');
            content.appendChild(subtitle);

            [
                ['Status', stop.visited ? ('Visited ' + (stop.visited_at || '')) : stop.priority_label],
                ['ETA', stop.eta],
                ['From previous', stop.leg_km !== null && stop.leg_km !== undefined ? stop.leg_km + ' km' : null],
                ['Mobile', stop.mobile],
                ['Address', stop.address],
                ['Beat', stop.beat_name],
                ['Last visit', stop.visited ? null : stop.last_visit]
            ].forEach(function(row) {
                if (!row[1]) return;
                var line = document.createElement('span');
                var label = document.createElement('strong');
                label.textContent = row[0] + ': ';
                line.appendChild(label);
                line.appendChild(document.createTextNode(row[1]));
                content.appendChild(line);
            });

            beatRouteInfoWindow.setContent(content);
            beatRouteInfoWindow.open(beatRouteMap, marker);

            $('.route-stop').removeClass('is-active');
            $('.route-stop[data-index="' + index + '"]').addClass('is-active');
        }

        function focusStop(index) {
            var marker = beatRouteMarkers[index];
            if (!marker || !beatRouteMap) return;

            beatRouteMap.panTo(marker.getPosition());
            if (beatRouteMap.getZoom() < 14) beatRouteMap.setZoom(14);
            google.maps.event.trigger(marker, 'click');
        }
    </script>
</x-app-layout>
