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
        <input id="searchInput" type="text" placeholder="Cari nama kegiatan..."
            class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring focus:ring-blue-400">

        <button id="searchBtn" class="w-full bg-blue-600 text-white py-1.5 rounded-md hover:bg-blue-700 text-sm">
            🔍 Cari Marker
        </button>

        <button id="togglePopupBtn" class="w-full bg-gray-700 text-white py-1.5 rounded-md hover:bg-gray-800 text-sm">
            💬 Tampilkan Semua Popup
        </button>

        <button onclick="window.location.href='{{ route('dashboard') }}'"
            class="w-full bg-red-500 text-white py-1.5 rounded-md hover:bg-red-600 text-sm">
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
            const marker = L.circleMarker([coord.lat, coord.lng], {
                radius: 8,
                fillColor: "blue",
                color: "blue",
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            const popupContent = `<b>${coord.name}</b>`;

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

        // Search marker (tetap sama)
        document.getElementById('searchBtn').addEventListener('click', () => {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const found = coordinates.find(c => c.name.toLowerCase().includes(q));
            if (found) {
                map.setView([found.lat, found.lng], 16);
                const marker = markers.find(m => m.getPopup().getContent().includes(found.name));
                if (marker) marker.openPopup();
            } else {
                alert('Marker tidak ditemukan!');
            }
        });

        // --- Toggle semua popup (baru, bersih, dan dapat di-render ulang) ---
        let popupVisible = false;
        const toggleBtn = document.getElementById('togglePopupBtn');

        function removeAllPopupDOMFragments() {
            // Hapus elemen popup yang mungkin tersisa (mis. dari implementasi clone sebelumnya)
            document.querySelectorAll('.leaflet-popup').forEach(el => el.remove());
        }

        toggleBtn.addEventListener('click', () => {
            popupVisible = !popupVisible;

            if (popupVisible) {
                // Bersihkan dulu semua popup yang ada (baik yang ter-bind maupun sisa klon)
                markers.forEach(m => m.closePopup()); // pastikan state Leaflet bersih
                removeAllPopupDOMFragments();

                // Buka ulang semua popup (render ulang bersih)
                markers.forEach(m => {
                    m.openPopup();
                    const p = m.getPopup();
                    // tambahkan animasi jika ada elemen DOM
                    const el = p && p.getElement();
                    if (el) {
                        el.classList.remove('fade-out');
                        el.classList.add('fade-in');
                    }
                });

                toggleBtn.innerText = "💬 Tutup Semua Popup";
            } else {
                // Tutup semua popup dengan animasi (jika tersedia)
                markers.forEach(m => {
                    const p = m.getPopup();
                    const el = p && p.getElement();
                    if (el) {
                        el.classList.remove('fade-in');
                        el.classList.add('fade-out');
                        // tunggu animasi lalu tutup popup (agar DOM popup yang dikelola Leaflet ikut terhapus)
                        setTimeout(() => m.closePopup(), 350);
                    } else {
                        m.closePopup();
                    }
                });

                // juga bersihkan fragment DOM yang tidak ter-handle setelah delay
                setTimeout(() => removeAllPopupDOMFragments(), 400);

                toggleBtn.innerText = "💬 Tampilkan Semua Popup";
            }
        });
    </script>
</body>

</html>
