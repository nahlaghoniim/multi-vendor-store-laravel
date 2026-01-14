<x-front-layout title="Order Details">

    <x-slot:breadcrumb>
        <div class="breadcrumbs">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="breadcrumbs-content">
                            <h1 class="page-title">Order # {{ $order->number }}</h1>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <ul class="breadcrumb-nav">
                            <li><a href="{{ route('home') }}"><i class="lni lni-home"></i> Home</a></li>
                            <li><a href="#">Orders</a></li>
                            <li>Order # {{ $order->number }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:breadcrumb>

    <section class="checkout-wrapper section">
        <div class="container">
            <div id="map" style="height: 50vh;"></div>

            @if(!$delivery)
                <p class="text-center mt-3">Delivery has not been assigned yet.</p>
            @endif
        </div>
    </section>

    <!-- Pusher JS -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        var map, marker;

        // Enable Pusher logging (remove in production)
        Pusher.logToConsole = true;

        var pusher = new Pusher('9bbd1071bbb820b9aef1', {
            cluster: 'ap2',
            channelAuthorization: {
                endpoint: "/broadcasting/auth",
                headers: { 
                    "X-CSRF-Token": "{{ csrf_token() }}"
                }
            }
        });

        // Subscribe to the delivery channel
        var channel = pusher.subscribe('private-deliveries.{{ $order->id }}');
        channel.bind('location-updated', function(data) {
            if(marker) {
                marker.setLatLng([Number(data.lat), Number(data.lng)]);
                map.panTo([Number(data.lat), Number(data.lng)]); // optional: keep map centered on marker
            }
        });
    </script>

    @if($delivery)
    <script>
        function initMap() {
            const location = [
                Number("{{ $delivery->lat }}"),
                Number("{{ $delivery->lng }}")
            ];

            // Initialize the map
            map = L.map('map').setView(location, 15);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Add marker
            marker = L.marker(location).addTo(map)
                        .bindPopup('Delivery Location')
                        .openPopup();
        }

        // Call the function to render the map
        initMap();
    </script>
    @endif

</x-front-layout>
