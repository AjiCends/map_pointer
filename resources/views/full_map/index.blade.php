@vite('resources/css/app.css')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Lengkap</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        .leaflet-popup.fade-in {
            opacity: 1;
            transition: opacity 0.25s ease;
        }

        .leaflet-popup.fade-out {
            opacity: 0;
            transition: opacity 0.35s ease;
        }
    </style>
</head>

<body class="h-screen w-screen bg-gray-900 relative">

    {{-- MAP --}}
    <div id="map" class="h-full w-full z-0"></div>

    {{-- CONTROL PANEL --}}
    <div class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-3 space-y-2 z-[1000] w-64">
        <!-- Input Pencarian -->
        <input id="searchInput" type="text" placeholder="Cari nama kegiatan..."
            class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring focus:ring-blue-400">

        <!-- Tombol Cari -->
        <button id="searchBtn"
            class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 text-sm font-medium focus:ring-4 focus:ring-blue-300">
            🔍 Cari Marker
        </button>

        <!-- Tombol Tampilkan Semua Popup -->
        <button id="openAllBtn"
            class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 text-sm font-medium focus:ring-4 focus:ring-green-300">
            💬 Tampilkan Semua Popup
        </button>

        <!-- Tombol Tutup Semua Popup -->
        <button id="closeAllBtn"
            class="w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 text-sm font-medium focus:ring-4 focus:ring-red-300">
            ❌ Tutup Semua Popup
        </button>

        <!-- Tombol Kembali -->
        <button onclick="window.location.href='{{ route('dashboard') }}'"
            class="w-full bg-gray-600 text-white py-2 rounded-md hover:bg-gray-700 text-sm font-medium focus:ring-4 focus:ring-gray-300">
            ⬅️ Kembali ke Beranda
        </button>
    </div>


    <script>
        const coordinates = @json($coordinates);
        const map = L.map('map').setView([-8.1725, 113.7008], 12);

        // Tile layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>'
        }).addTo(map);

        const markers = [];
        let bounds = L.latLngBounds([]);

        // Buat semua marker dan bind popup (autoClose:false supaya bisa banyak terbuka)
        coordinates.forEach(coord => {
            const marker = L.marker([coord.lat, coord.lng], {
                radius: 8,
                fillColor: "blue",
                color: "blue",
                weight: 2,
                opacity: 1,
                fillOpacity: 1
            }).addTo(map);

            // const popupContent = `<b>${coord.name}</b>`;
            const popupContent = `
                <div style="min-width: 140px">
                    <b>${coord.name}</b><br>
                    <button 
                        onclick="window.open('https://www.google.com/maps?q=${coord.lat},${coord.lng}', '_blank')" 
                        style="margin-top: 6px; display: flex; align-items: center; gap: 4px; background-color: #2563eb; color: white; border: none; border-radius: 6px; padding: 4px 8px; cursor: pointer; font-size: 11px;"
                    >
                        🔗 GMaps
                    </button>
                </div>
            `;



            const popup = L.popup({
                    autoClose: false, // penting: jangan auto close popup saat popup lain dibuka
                    closeOnClick: false
                })
                .setLatLng([coord.lat, coord.lng])
                .setContent(popupContent);

            marker.bindPopup(popup);
            markers.push(marker);
            bounds.extend([coord.lat, coord.lng]);
        });

        if (coordinates.length > 0) map.fitBounds(bounds, {
            padding: [40, 40]
        });

        document.getElementById('searchBtn').addEventListener('click', () => {
            const q = document.getElementById('searchInput').value.toLowerCase().trim();
            if (!q) return; // kalau kosong, abaikan

            // cari semua yang cocok
            const foundList = coordinates.filter(c => c.name.toLowerCase().includes(q));

            // tutup semua popup dulu
            markers.forEach(m => m.closePopup());

            if (foundList.length > 0) {
                // buat bounds baru untuk zoom ke semua hasil
                const searchBounds = L.latLngBounds([]);

                foundList.forEach(found => {
                    // temukan marker sesuai hasil pencarian
                    const marker = markers.find(m =>
                        m.getPopup().getContent().includes(found.name)
                    );
                    if (marker) {
                        marker.openPopup();
                        searchBounds.extend([found.lat, found.lng]);
                    }
                });

                // kalau hanya satu hasil → fokuskan zoom
                if (foundList.length === 1) {
                    map.setView([foundList[0].lat, foundList[0].lng], 16);
                } else {
                    map.fitBounds(searchBounds, {
                        padding: [40, 40]
                    });
                }

            } else {
                alert('Marker tidak ditemukan!');
            }
        });


        // --- Toggle semua popup (baru, bersih, dan dapat di-render ulang) ---
        let popupVisible = false;
        const toggleBtn = document.getElementById('togglePopupBtn');

        // Fungsi pembantu untuk bersihkan popup DOM lama
        function removeAllPopupDOMFragments() {
            document.querySelectorAll('.leaflet-popup').forEach(el => el.remove());
        }

        // --- Tombol: Buka Semua Popup ---
        document.getElementById('openAllBtn').addEventListener('click', () => {
            // Tutup semua popup dulu biar clean
            markers.forEach(m => m.closePopup());
            removeAllPopupDOMFragments();

            // Buka ulang semua popup dengan animasi fade-in
            markers.forEach(m => {
                m.openPopup();
                const p = m.getPopup();
                const el = p && p.getElement();
                if (el) {
                    el.classList.remove('fade-out');
                    el.classList.add('fade-in');
                }
            });
        });

        // --- Tombol: Tutup Semua Popup ---
        document.getElementById('closeAllBtn').addEventListener('click', () => {
            // Tutup semua popup dengan animasi fade-out
            markers.forEach(m => {
                const p = m.getPopup();
                const el = p && p.getElement();
                if (el) {
                    el.classList.remove('fade-in');
                    el.classList.add('fade-out');
                    setTimeout(() => m.closePopup(), 350);
                } else {
                    m.closePopup();
                }
            });

            // Bersihkan popup DOM fragment setelah animasi
            setTimeout(() => removeAllPopupDOMFragments(), 400);
        });
    </script>
</body>

</html>
