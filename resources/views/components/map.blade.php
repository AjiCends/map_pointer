@props(['coordinates' => [], 'id' => 'map', 'groupByProgram' => false])

<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="relative">
    <div id="{{ $id }}" class="w-full h-96 rounded-lg shadow"></div>

    <!-- Tombol Hapus Marker -->
    <button type="button" id="clear-marker-{{ $id }}"
        class="absolute bottom-2 right-2 z-[1000] bg-red-500 text-white px-3 py-1 rounded shadow hover:bg-red-600 text-sm hidden">
        Hapus Marker
    </button>
</div>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const coordinates = @json($coordinates);
        const clearBtn = document.getElementById("clear-marker-{{ $id }}");

        // Init Map
        const map = L.map("{{ $id }}").setView([-8.1725, 113.7008], 13);

        // Base Tile
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        let bounds = L.latLngBounds([]);

        // Marker existing dari DB
        coordinates.forEach((coord) => {
            const marker = L.marker([coord.lat, coord.lng]).addTo(map);
            marker.bindPopup(`<b>${coord.name}</b>`);
            bounds.extend([coord.lat, coord.lng]);
        });

        if (coordinates.length > 0) {
            map.fitBounds(bounds, {
                padding: [50, 50]
            });
        }

        // ======================
        // SATU MARKER UNTUK CLICK & SEARCH
        // ======================
        let activeMarker;

        function placeMarker(lat, lng) {
            if (activeMarker) {
                map.removeLayer(activeMarker);
            }
            activeMarker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            updateLatLng(lat, lng);

            // Drag update
            activeMarker.on("dragend", function(e) {
                const {
                    lat,
                    lng
                } = e.target.getLatLng();
                updateLatLng(lat, lng);
            });

            // Tampilkan tombol clear
            clearBtn.classList.remove("hidden");
        }

        // Klik map → taruh marker
        map.on("click", function(e) {
            const {
                lat,
                lng
            } = e.latlng;
            placeMarker(lat, lng);
        });

        // Search → taruh marker
        const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false
            })
            .on("markgeocode", function(e) {
                const latlng = e.geocode.center;
                map.setView(latlng, 16);
                placeMarker(latlng.lat, latlng.lng);
            })
            .addTo(map);

        // Update input form
        function updateLatLng(lat, lng) {
            const latInput = document.getElementById("latitude");
            const lngInput = document.getElementById("longitude");
            if (latInput && lngInput) {
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }
        }

        // Clear marker
        clearBtn.addEventListener("click", function() {
            if (activeMarker) {
                map.removeLayer(activeMarker);
                activeMarker = null;
            }
            clearBtn.classList.add("hidden");
            updateLatLng("", "");
        });
    });
</script>



{{-- @props(['coordinates' => [], 'id' => 'map', 'groupByProgram' => false])

<!-- Geocoder CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="relative">
    <!-- Map -->
    <div id="{{ $id }}" class="w-full h-96 rounded-lg shadow"></div>
</div>

<!-- Leaflet Geocoder JS -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const coordinates = @json($coordinates);
        const groupByProgram = @json($groupByProgram);

        // Init Map
        const map = L.map("{{ $id }}").setView([-8.1725, 113.7008], 13);

        // Tile Layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        let bounds = L.latLngBounds([]);

        // Marker dari DB
        coordinates.forEach((coord) => {
            const marker = L.marker([coord.lat, coord.lng]).addTo(map);
            marker.bindPopup(`<b>${coord.name}</b>`);
            bounds.extend([coord.lat, coord.lng]);
        });

        if (coordinates.length > 0) {
            map.fitBounds(bounds, {
                padding: [50, 50]
            });
        }

        // =======================
        // FITUR CLICK MAP
        // =======================
        let clickedMarker;
        map.on("click", function(e) {
            const {
                lat,
                lng
            } = e.latlng;

            if (clickedMarker) {
                map.removeLayer(clickedMarker);
            }

            clickedMarker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            updateLatLng(lat, lng);

            // Event drag marker klik
            clickedMarker.on("dragend", function(e) {
                const {
                    lat,
                    lng
                } = e.target.getLatLng();
                updateLatLng(lat, lng);
            });
        });

        // =======================
        // FITUR SEARCH GEOCODER
        // =======================
        let searchMarker;
        const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false
            })
            .on("markgeocode", function(e) {
                const latlng = e.geocode.center;
                map.setView(latlng, 16);

                if (searchMarker) {
                    map.removeLayer(searchMarker);
                }

                searchMarker = L.marker(latlng, {
                    draggable: true
                }).addTo(map);
                updateLatLng(latlng.lat, latlng.lng);

                // Drag update form
                searchMarker.on("dragend", function(e) {
                    const {
                        lat,
                        lng
                    } = e.target.getLatLng();
                    updateLatLng(lat, lng);
                });
            })
            .addTo(map);

        // =======================
        // UPDATE FORM
        // =======================
        function updateLatLng(lat, lng) {
            const latInput = document.getElementById("latitude");
            const lngInput = document.getElementById("longitude");
            if (latInput && lngInput) {
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }
        }
    });
</script> --}}



{{-- @props(['coordinates' => [], 'id' => 'map', 'groupByProgram' => false])

<!-- Geocoder CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="relative">
    <!-- Map -->
    <div id="{{ $id }}" class="w-full h-96 rounded-lg shadow"></div>
</div>

<!-- Leaflet Geocoder JS -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const coordinates = @json($coordinates);
        const groupByProgram = @json($groupByProgram);

        // Init Map
        const map = L.map("{{ $id }}").setView([-8.1725, 113.7008], 13);

        // Tile Layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        let bounds = L.latLngBounds([]);

        // Tambahkan marker dari database
        coordinates.forEach((coord) => {
            const marker = L.marker([coord.lat, coord.lng]).addTo(map);
            marker.bindPopup(`<b>${coord.name}</b>`);
            bounds.extend([coord.lat, coord.lng]);
        });

        if (coordinates.length > 0) {
            map.fitBounds(bounds, {
                padding: [50, 50]
            });
        }

        // =======================
        // FITUR CLICK MAP
        // =======================
        let clickedMarker;
        map.on("click", function(e) {
            const {
                lat,
                lng
            } = e.latlng;

            // Hapus marker sebelumnya
            if (clickedMarker) {
                map.removeLayer(clickedMarker);
            }

            // Tambah marker baru
            clickedMarker = L.marker([lat, lng]).addTo(map);

            // Auto isi form input latitude & longitude jika ada
            const latInput = document.getElementById("latitude");
            const lngInput = document.getElementById("longitude");
            if (latInput && lngInput) {
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }
        });

        // =======================
        // FITUR SEARCH GEOCODER
        // =======================
        let searchMarker;
        const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false
            })
            .on("markgeocode", function(e) {
                const latlng = e.geocode.center;

                // Pindah view map
                map.setView(latlng, 16);

                // Hapus marker search lama
                if (searchMarker) {
                    map.removeLayer(searchMarker);
                }

                // Tambah marker baru dari search
                searchMarker = L.marker(latlng).addTo(map);

                // Auto isi form input
                const latInput = document.getElementById("latitude");
                const lngInput = document.getElementById("longitude");
                if (latInput && lngInput) {
                    latInput.value = latlng.lat.toFixed(6);
                    lngInput.value = latlng.lng.toFixed(6);
                }
            })
            .addTo(map);
    });
</script> --}}




{{-- @props(['coordinates', 'id' => 'map', 'groupByProgram' => false])

<div id="{{ $id }}" class="w-full h-96 rounded-lg shadow"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const coordinates = @json($coordinates);
        const groupByProgram = @json($groupByProgram);

        const map = L.map("{{ $id }}").setView([-8.1725, 113.7008], 13);

        // Tile Layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Warna marker lama
        const colors = ["red", "blue", "green", "orange", "purple", "darkred", "cadetblue", "darkblue",
            "darkgreen", "darkorange"
        ];

        function getColor(key) {
            return colors[key % colors.length];
        }

        let bounds = L.latLngBounds([]);

        // Tambah marker dari data lama
        coordinates.forEach((coord) => {
            const key = groupByProgram ? coord.program_id : coord.id;
            const color = getColor(key);

            L.circleMarker([coord.lat, coord.lng], {
                radius: 8,
                fillColor: color,
                color: color,
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).bindPopup(`<b>${coord.name}</b>`).addTo(map);

            bounds.extend([coord.lat, coord.lng]);
        });

        if (coordinates.length > 0) {
            map.fitBounds(bounds, {
                padding: [50, 50]
            });
        }

        // === Tambahkan marker baru untuk klik user ===
        let newMarker;
        map.on("click", function(e) {
            const {
                lat,
                lng
            } = e.latlng;

            // isi otomatis form
            document.getElementById("latitude").value = lat.toFixed(6);
            document.getElementById("longitude").value = lng.toFixed(6);

            // hapus marker lama kalau ada
            if (newMarker) {
                map.removeLayer(newMarker);
            }

            // tambahkan marker baru
            newMarker = L.marker([lat, lng]).addTo(map)
                .bindPopup("Lokasi terpilih:<br>Lat: " + lat.toFixed(6) + "<br>Lng: " + lng.toFixed(6))
                .openPopup();
        });
    });
</script> --}}




{{-- @props(['coordinates', 'id' => 'map', 'groupByProgram' => false])

<style>
    .leaflet-popup {
        transition: opacity 1s ease;
    }

    .leaflet-popup.fade-out {
        opacity: 0;
    }
</style>

<div class="relative">
    <!-- Map -->
    <div id="{{ $id }}" class="w-full h-96 rounded-lg sm:rounded-xl shadow pointer-events-none"></div>

    <!-- Tombol Toggle -->
    <button id="toggle-legend-{{ $id }}"
        class="absolute top-2 right-2 z-[1000] p-2 text-lg bg-gray-50 rounded-full shadow hover:bg-gray-100 focus:outline-none">
        <span class="icon-info">
            <x-heroicon-o-information-circle class="w-5 h-5 text-black" />
        </span>
        <span class="icon-close hidden">
            <x-heroicon-o-x-circle class="w-5 h-5 text-black" />
        </span>
    </button>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const coordinates = @json($coordinates);
        const groupByProgram = @json($groupByProgram);

        const map = L.map("{{ $id }}");

        // Tile Layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Warna
        const colors = [
            "red", "blue", "green", "orange", "purple",
            "darkred", "cadetblue", "darkblue", "darkgreen", "darkorange"
        ];

        function getColor(key) {
            return colors[key % colors.length];
        }

        let bounds = L.latLngBounds([]);
        let legendItems = {};

        // Marker
        coordinates.forEach((coord) => {
            const key = groupByProgram ? coord.program_id : coord.id;
            const color = getColor(key);

            const marker = L.circleMarker([coord.lat, coord.lng], {
                radius: 8,
                fillColor: color,
                color: color,
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            // marker.bindPopup(
            //     `<b>${coord.name}</b><br>Lat: ${coord.lat}, Lng: ${coord.lng}` +
            //     (groupByProgram ? `<br><i>Program: ${coord.program_name}</i>` : "")
            // );

            // Buat popup dulu
            const popupContent = `<b>${coord.name}</b><br>Lat: ${coord.lat}, Lng: ${coord.lng}` +
                (groupByProgram ? `<br><i>Program: ${coord.program_name}</i>` : "");

            const popup = L.popup({
                    autoClose: false,
                    closeOnClick: false
                })
                .setLatLng([coord.lat, coord.lng])
                .setContent(popupContent);

            // Saat marker di klik -> tampilkan popup & auto fade
            marker.on("click", function() {
                popup.openOn(map);

                // Tambah class fade
                const popupEl = document.querySelector(".leaflet-popup");
                if (popupEl) {
                    popupEl.wclassList.remove("fade-out"); // reset kalau sebelumnya sudah ada
                    setTimeout(() => {
                        popupEl.classList.add("fade-out");
                        setTimeout(() => {
                            map.closePopup(popup);
                        }, 1000); // setelah animasi fade selesai
                    }, 2000); // tampil selama 3 detik
                }
            });


            bounds.extend([coord.lat, coord.lng]);

            if (!legendItems[key]) {
                legendItems[key] = {
                    name: groupByProgram ? coord.program_name : coord.name,
                    color
                };
            }
        });

        if (coordinates.length > 0) {
            map.fitBounds(bounds, {
                padding: [50, 50]
            });
        } else {
            map.setView([-8.1725, 113.7008], 13);
        }

        // Legend
        const legend = L.control({
            position: "topright"
        });
        legend.onAdd = function() {
            const div = L.DomUtil.create("div", "info legend bg-white p-2 rounded shadow");
            div.innerHTML = "<h1 class='text-lg font-semibold mb-2'>Keterangan</h1>";
            Object.values(legendItems).forEach(item => {
                div.innerHTML += `<div class="flex items-center mb-1">
                    <span style="background:${item.color};width:12px;height:12px;display:inline-block;margin-right:6px;border-radius:2px;"></span>
                    ${item.name}
                </div>`;
            });
            return div;
        };

        // Toggle Legend
        let legendVisible = false;
        const btn = document.getElementById("toggle-legend-{{ $id }}");
        const iconInfo = btn.querySelector(".icon-info");
        const iconClose = btn.querySelector(".icon-close");

        btn.addEventListener("click", function() {
            if (legendVisible) {
                map.removeControl(legend);
                iconInfo.classList.remove("hidden");
                iconClose.classList.add("hidden");
            } else {
                legend.addTo(map);
                iconInfo.classList.add("hidden");
                iconClose.classList.remove("hidden");
            }
            legendVisible = !legendVisible;
        });
    });
</script> --}}
