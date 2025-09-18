<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon card-header-theme">
                    <div class="card-icon">
                        <i class="material-icons">perm_identity</i>
                    </div>
                    <h4 class="card-title ">Complete Map </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div id="map"></div>
                        </div>
                        <div class="col-md-4">
                            <div id="sidebar">
                                <h2 class="text-dark">Customer Details</h2>
                                <div class="text-dark" id="customer-info">Click on a marker</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18"></script>

    <script>
        let map;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 20.5937,
                    lng: 78.9629
                }, // center of India or wherever
                zoom: 5
            });

            // fetch customers
            fetch("{{ route('customer.data') }}")
                .then(response => response.json())
                .then(customers => {
                    customers.forEach(cust => {
                        if (!cust.latitude || !cust.longitude) return;
                        
                        let iconUrl;
                        switch (cust.customertype) {
                            case 1:
                                iconUrl = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
                                break;
                            case 2:
                                iconUrl = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                                break;
                            case 3:
                                iconUrl = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
                                break;
                            default:
                                iconUrl = "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
                        }
                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(cust.latitude),
                                lng: parseFloat(cust.longitude)
                            },
                            map: map,
                            title: cust.name,
                            icon: iconUrl
                        });

                        marker.addListener('click', () => {
                            // show in sidebar
                            document.getElementById('customer-info').innerHTML = `
                                <p class="text-dark"><strong>${cust.name}</strong> - ${cust.customertypes.customertype_name}</p>
                                <p class="text-dark">Mobile: ${cust.mobile}</p>
                                <p class="text-dark">Address: ${cust.customeraddress.address1}</p>
                                <p class="text-dark">City: ${cust.customeraddress.cityname.city_name}</p>
                                <p class="text-dark">District: ${cust.customeraddress.districtname.district_name}</p>
                                <p class="text-dark">State: ${cust.customeraddress.statename.state_name}</p>
                                <!-- other info you want -->
                            `;
                        });
                    });
                })
                .catch(err => console.error(err));
        }

        window.onload = initMap;
    </script>
</x-app-layout>