@props(['coordinates' => [], 'id' => 'map', 'groupByProgram' => false, 'interactive' => false])

<div class="relative">
    <div id="{{ $id }}" class="w-full h-96 rounded-lg shadow"></div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const coordinates = @json($coordinates);
        const interactive = @json($interactive);

        // Init Map
        const map = L.map("{{ $id }}", {
            zoomControl: true,
            dragging: true, // Map movement is always allowed
            scrollWheelZoom: true,
            doubleClickZoom: true,
            boxZoom: true,
            keyboard: true,
            touchZoom: true
        }).setView([-8.1725, 113.7008], 13);

        // Base Tile
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        let bounds = L.latLngBounds([]);

        // Marker dari DB
        coordinates.forEach((coord) => {
            const marker = L.marker([coord.lat, coord.lng], {
                draggable: false
            }).addTo(map);
            marker.bindPopup(`<b>${coord.name}</b>`);
            bounds.extend([coord.lat, coord.lng]);
        });

        if (coordinates.length > 0) {
            map.fitBounds(bounds, {
                padding: [50, 50]
            });
        }

        // Hanya jalankan fitur marker/click jika interactive = true
        if (!interactive) return;

        // ======================
        // Fitur interaktif (hanya untuk create/edit)
        // ======================
        let activeMarker;

        function placeMarker(lat, lng) {
            if (activeMarker) map.removeLayer(activeMarker);
            activeMarker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            updateLatLng(lat, lng);

            activeMarker.on("dragend", function(e) {
                const {
                    lat,
                    lng
                } = e.target.getLatLng();
                updateLatLng(lat, lng);
            });
        }

        // Klik map → taruh marker
        map.on("click", function(e) {
            placeMarker(e.latlng.lat, e.latlng.lng);
        });

        // Search → taruh marker
        L.Control.geocoder({
                defaultMarkGeocode: false
            })
            .on("markgeocode", function(e) {
                const latlng = e.geocode.center;
                map.setView(latlng, 16);
                placeMarker(latlng.lat, latlng.lng);
            })
            .addTo(map);

        function updateLatLng(lat, lng) {
            const latInput = document.getElementById("latitude");
            const lngInput = document.getElementById("longitude");
            if (latInput && lngInput) {
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }
        }
    });
</script>
