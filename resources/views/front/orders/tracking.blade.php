<x-front-layout title="Order Tracking">

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
            @if($delivery)
                <div class="alert alert-info mb-3">
                    <strong>Delivery Status:</strong> {{ ucfirst($delivery->status ?? 'Pending') }}
                </div>
                <div id="map" style="height: 500px; width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
            @else
                <div class="alert alert-warning text-center">
                    <i class="lni lni-warning"></i>
                    <p class="mb-0 mt-2">Delivery has not been assigned yet.</p>
                </div>
            @endif
        </div>
    </section>

    @if($delivery)
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    
    <!-- Pusher JS -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing map...');
            
            // Get coordinates from delivery
            const lat = {{ $delivery->lat }};
            const lng = {{ $delivery->lng }};
            
            console.log('Coordinates:', lat, lng);
            
            // Initialize the map
            const map = L.map('map').setView([lat, lng], 15);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Custom marker icon (red marker)
            const deliveryIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Add marker
            let marker = L.marker([lat, lng], {icon: deliveryIcon}).addTo(map)
                        .bindPopup('<strong>Delivery Location</strong><br>Order #{{ $order->number }}')
                        .openPopup();

            console.log('Map initialized successfully');

            // Pusher real-time updates
            try {
                const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                    channelAuthorization: {
                        endpoint: "/broadcasting/auth",
                        headers: { 
                            "X-CSRF-Token": "{{ csrf_token() }}"
                        }
                    }
                });

                const channel = pusher.subscribe('private-deliveries.{{ $order->id }}');
                
                channel.bind('location-updated', function(data) {
                    console.log('Location updated:', data);
                    
                    if(marker && data.lat && data.lng) {
                        const newLocation = [Number(data.lat), Number(data.lng)];
                        marker.setLatLng(newLocation);
                        map.panTo(newLocation, { animate: true, duration: 1 });
                        marker.setPopupContent(`<strong>Delivery Location</strong><br>Order #{{ $order->number }}<br><small>Updated: ${new Date().toLocaleTimeString()}</small>`);
                    }
                });

                pusher.connection.bind('connected', function() {
                    console.log('Pusher connected');
                });

                pusher.connection.bind('error', function(err) {
                    console.error('Pusher error:', err);
                });
            } catch(e) {
                console.error('Pusher initialization failed:', e);
            }
        });
    </script>
    @endif

</x-front-layout>
```

**Steps to create the file:**

1. Navigate to `resources/views/front/orders/`
2. Create a new file called `tracking.blade.php`
3. Paste the code above
4. Save the file

Now refresh your browser and visit:
```
http://127.0.0.1:8000/en/orders/83/tracking