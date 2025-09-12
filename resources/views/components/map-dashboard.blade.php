@props(['coordinates' => [], 'id' => 'map-dashboard', 'groupByProgram' => false])

<style>
    .leaflet-popup {
        transition: opacity 1s ease;
    }

    .leaflet-popup.fade-out {
        opacity: 0;
    }
</style>

<div class="relative w-full h-full">
    <!-- Map Full Height -->
    <div id="{{ $id }}" class="w-full h-full rounded-lg sm:rounded-xl shadow"></div>

    <!-- Tombol Toggle Legend -->
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

        // Base Tile
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Warna marker
        const colors = [
            "red", "blue", "green", "orange", "purple",
            "darkred", "cadetblue", "darkblue", "darkgreen", "darkorange"
        ];

        function getColor(key) {
            return colors[key % colors.length];
        }

        let bounds = L.latLngBounds([]);
        let legendItems = {};

        // Marker dari DB
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

            // Popup custom dengan fade
            const popupContent = `<b>${coord.name}</b><br>Lat: ${coord.lat}, Lng: ${coord.lng}` +
                (groupByProgram ? `<br><i>Program: ${coord.program_name}</i>` : "");

            const popup = L.popup({
                    autoClose: false,
                    closeOnClick: false
                })
                .setLatLng([coord.lat, coord.lng])
                .setContent(popupContent);

            marker.on("click", function() {
                popup.openOn(map);

                const popupEl = document.querySelector(".leaflet-popup");
                if (popupEl) {
                    popupEl.classList.remove("fade-out");
                    setTimeout(() => {
                        popupEl.classList.add("fade-out");
                        setTimeout(() => {
                            map.closePopup(popup);
                        }, 1000);
                    }, 2000); // tampil 2 detik, fade 1 detik
                }
            });

            // extend bounds
            bounds.extend([coord.lat, coord.lng]);

            if (!legendItems[key]) {
                legendItems[key] = {
                    name: groupByProgram ? coord.program_name : coord.name,
                    color
                };
            }
        });

        // Gunakan bounds dari database
        if (coordinates.length > 0) {
            map.fitBounds(bounds, {
                padding: [50, 50]
            });
        } else {
            map.setView([-8.1725, 113.7008], 13);
        }

        // Legend
        const legend = L.control({
            position: "bottomright"
        });
        legend.onAdd = function() {
            const div = L.DomUtil.create("div", "info legend bg-white p-3 rounded shadow text-sm");
            div.innerHTML = "<h1 class='text-base font-semibold mb-2'>Keterangan</h1>";
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
</script>
