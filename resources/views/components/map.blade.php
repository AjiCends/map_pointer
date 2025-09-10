@props(['coordinates', 'id' => 'map', 'groupByProgram' => false])

<div id="{{ $id }}" class="w-full h-96 rounded-lg shadow"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const coordinates = @json($coordinates);
        const groupByProgram = @json($groupByProgram);

        // Inisialisasi map TANPA setView awal
        const map = L.map("{{ $id }}");

        // Tile layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Daftar warna
        const colors = [
            "red", "blue", "green", "orange", "purple",
            "darkred", "cadetblue", "darkblue", "darkgreen", "darkorange"
        ];

        function getColor(key) {
            return colors[key % colors.length];
        }

        let bounds = L.latLngBounds([]);
        let legendItems = {};

        // Tambahkan marker
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

            marker.bindPopup(
                `<b>${coord.name}</b><br>Lat: ${coord.lat}, Lng: ${coord.lng}` +
                (groupByProgram ? `<br><i>Program: ${coord.program_name}</i>` : "")
            );

            bounds.extend([coord.lat, coord.lng]);

            // Isi legend
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
            // fallback ke Jember kalau data kosong
            map.setView([-8.1725, 113.7008], 13);
        }

        // Legend
        const legend = L.control({
            position: "bottomleft"
        });
        legend.onAdd = function() {
            const div = L.DomUtil.create("div", "info legend bg-white p-2 rounded shadow");
            div.innerHTML = "<h4 class='font-semibold mb-1'>Legenda</h4>";
            Object.values(legendItems).forEach(item => {
                div.innerHTML += `<div class="flex items-center mb-1">
                    <span style="background:${item.color};width:12px;height:12px;display:inline-block;margin-right:6px;border-radius:2px;"></span>
                    ${item.name}
                </div>`;
            });
            return div;
        };
        legend.addTo(map);
    });
</script>
