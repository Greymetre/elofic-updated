<!DOCTYPE html>
<html>

<head>
    <title>Route Map</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18"></script>
</head>

<body>
    <div id="map"></div>
    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 4,
                center: {
                    lat: 20.5937,
                    lng: 78.9629
                }
            });

            var coordinates = @json($coordinates);

            if (coordinates.length === 0) {
                console.error('No coordinates found');
                return;
            }

            var routePathCoordinates = [];
            coordinates.forEach(function(coord) {
                var latLng = {
                    lat: parseFloat(coord.latitude),
                    lng: parseFloat(coord.longitude)
                };
                console.log(coord.name);
                if (coord.name == 'Punch In') {
                    var clr = 'green';
                } else if (coord.name == 'Punch Out') {
                    var clr = 'yellow';
                } else {
                    var clr = 'red';
                }
                routePathCoordinates.push(latLng);
                const svgMarker = {
                    path: "M-1.547 12l6.563-6.609-1.406-1.406-5.156 5.203-2.063-2.109-1.406 1.406zM0 0q2.906 0 4.945 2.039t2.039 4.945q0 1.453-0.727 3.328t-1.758 3.516-2.039 3.070-1.711 2.273l-0.75 0.797q-0.281-0.328-0.75-0.867t-1.688-2.156-2.133-3.141-1.664-3.445-0.75-3.375q0-2.906 2.039-4.945t4.945-2.039z",
                    fillColor: clr,
                    fillOpacity: 0.6,
                    strokeWeight: 0,
                    rotation: 0,
                    scale: 2,
                    anchor: new google.maps.Point(0, 20),
                };

                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: coord.name,
                    icon: svgMarker
                });
            });

            var routePath = new google.maps.Polyline({
                path: routePathCoordinates,
                geodesic: true,
                strokeColor: '#3a7ae3',
                strokeOpacity: 1.0,
                strokeWeight: 4
            });

            routePath.setMap(map);
        }

        window.onload = initMap;
    </script>
</body>

</html>