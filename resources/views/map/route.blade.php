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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaQE4KPeMlUtOToahksBb7k7TUNx7MISo"></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18"></script> -->
</head>

<body>
    <div id="map"></div>
    <script>
        function initMap() {
            var coordinates = @json($coordinates);

            if (coordinates.length === 0) {
                console.error('No coordinates found');
                return;
            }

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: {
                    lat: parseFloat(coordinates[0].latitude),
                    lng: parseFloat(coordinates[0].longitude)
                }
            });

            var directionsService = new google.maps.DirectionsService();
            var directionsRenderer = new google.maps.DirectionsRenderer({
                map: map
            });

            var waypoints = [];
            for (var i = 1; i < coordinates.length - 1; i++) {
                waypoints.push({
                    location: new google.maps.LatLng(parseFloat(coordinates[i].latitude), parseFloat(coordinates[i].longitude)),
                    stopover: true
                });
            }

            var request = {
                origin: new google.maps.LatLng(parseFloat(coordinates[0].latitude), parseFloat(coordinates[0].longitude)),
                destination: new google.maps.LatLng(parseFloat(coordinates[coordinates.length - 1].latitude), parseFloat(coordinates[coordinates.length - 1].longitude)),
                waypoints: waypoints,
                travelMode: google.maps.TravelMode.DRIVING // or 'WALKING', 'BICYCLING', 'TRANSIT'
            };

            directionsService.route(request, function(response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(response);
                } else {
                    console.error('Directions request failed due to ' + status);
                }
            });

            coordinates.forEach(function(coord) {
                var latLng = {
                    lat: parseFloat(coord.latitude),
                    lng: parseFloat(coord.longitude)
                };
                var clr;
                if (coord.name == 'Punch In') {
                    clr = 'green';
                } else if (coord.name == 'Punch Out') {
                    clr = 'yellow';
                } else {
                    clr = 'red';
                }

                const svgMarker = {
                    path: "M-1.547 12l6.563-6.609-1.406-1.406-5.156 5.203-2.063-2.109-1.406 1.406zM0 0q2.906 0 4.945 2.039t2.039 4.945q0 1.453-0.727 3.328t-1.758 3.516-2.039 3.070-1.711 2.273l-0.75 0.797q-0.281-0.328-0.75-0.867t-1.688-2.156-2.133-3.141-1.664-3.445-0.75-3.375q0-2.906 2.039-4.945t4.945-2.039z",
                    fillColor: clr,
                    fillOpacity: 0.8,
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
        }

        window.onload = initMap;
    </script>

</body>

</html>