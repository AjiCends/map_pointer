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
    <div id="controlCardPanel"
        class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-3 z-[1000] w-64 transition-all duration-300 overflow-hidden">

        <!-- Grup Tombol -->
        <div id="buttonPanelGroup" class="flex flex-col gap-2 transition-all duration-300">
            <input id="searchInput" type="text" placeholder="Cari nama kegiatan..."
                class="w-full px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring focus:ring-blue-400">

            <button id="searchBtn"
                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 text-sm font-medium focus:ring-4 focus:ring-blue-300">
                🔍 Cari Marker
            </button>

            <button id="openAllBtn"
                class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 text-sm font-medium focus:ring-4 focus:ring-green-300">
                💬 Tampilkan Semua Popup
            </button>

            <button id="closeAllBtn"
                class="w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 text-sm font-medium focus:ring-4 focus:ring-red-300">
                ❌ Tutup Semua Popup
            </button>

            <button onclick="window.location.href='{{ route('programs.show', $program) }}'"
                class="w-full bg-gray-600 text-white py-2 rounded-md hover:bg-gray-700 text-sm font-medium focus:ring-4 focus:ring-gray-300">
                Programs
            </button>

            <button onclick="window.location='{{ route('routes.index', $program) }}'"
                class="w-full bg-gray-400 text-white py-2 rounded-md hover:bg-gray-500 text-sm font-medium focus:ring-4 focus:ring-gray-300">
                Rute
            </button>
        </div>

        <!-- Tombol Toggle -->
        <div class="mt-2">
            <button id="togglePanelBtn"
                class="w-full bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 text-sm font-medium focus:ring-4 focus:ring-yellow-300">
                👁️ Hide Buttons
            </button>
        </div>
    </div>


    <script>
        const togglePanelBtn = document.getElementById('togglePanelBtn');
        const buttonPanelGroup = document.getElementById('buttonPanelGroup');
        const cardPanel = document.getElementById('controlCardPanel');
        let hidden = false;

        togglePanelBtn.addEventListener('click', () => {
            hidden = !hidden;

            if (hidden) {
                // Sembunyikan semua tombol kecuali toggle
                buttonPanelGroup.style.maxHeight = '0';
                buttonPanelGroup.style.opacity = '0';
                buttonPanelGroup.style.pointerEvents = 'none';
                cardPanel.style.width = 'fit-content';
                cardPanel.style.padding = '0.5rem';
                cardPanel.style.opacity = '0.8';
                togglePanelBtn.textContent = '👁️ Show Buttons';
            } else {
                // Tampilkan kembali semua tombol
                buttonPanelGroup.style.maxHeight = '1000px';
                buttonPanelGroup.style.opacity = '1';
                buttonPanelGroup.style.pointerEvents = 'auto';
                cardPanel.style.width = '16rem';
                cardPanel.style.padding = '0.75rem';
                cardPanel.style.opacity = '1';
                togglePanelBtn.textContent = '🙈 Hide Buttons';
            }
        });

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
        coordinates.forEach((coord, index) => {

            console.log('test coord', coord);
            

            const number = coord.order_num ?? '';

            const numberedIcon = L.divIcon({
                className: "", // biar style-nya murni dari inline
                html: `
            <div style="position: relative; width: 25px; height: 41px;">
                <img 
                    src="https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png" 
                    style="width: 25px; height: 41px; display: block;"
                />
                <div 
                    style="
                        position: absolute;
                        top: 0px;
                        left: 50%;
                        transform: translateX(-50%);
                        background: white;
                        color: black;
                        border-radius: 50%;
                        width: 27px;
                        height: 27px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 16px;
                        font-weight: bold;
                        border: 2px solid rgba(42, 129, 203);
                        box-shadow: 0 0 2px rgba(0,0,0,0.5);
                    "
                >${number}</div>
            </div>
        `,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [0, -30]
            });

            // Kode utama
            const marker = L.marker([coord.lat, coord.lng], {
                radius: 8,
                fillColor: "blue",
                color: "blue",
                weight: 2,
                opacity: 1,
                fillOpacity: 1,
                icon: numberedIcon
            }).addTo(map);


            const popupContent = `
                <div style="min-width: 140px">
                    <b>${coord.name}</b><br>

                    <div style="display: flex; flex-direction: column; gap: 4px; margin-top: 6px;">
                        <!-- Tombol ke Google Maps -->
                        <button 
                            onclick="window.open('https://www.google.com/maps?q=${coord.lat},${coord.lng}', '_blank')" 
                            style="display: flex; align-items: center; gap: 4px; background-color: #2563eb; color: white; border: none; border-radius: 6px; padding: 4px 8px; cursor: pointer; font-size: 11px;"
                        >
                            🔗 GMaps
                        </button>

                        <!-- Tombol ke Gallery -->
                        <button 
                            onclick="window.open('/activities/${coord.id}/gallery', '_blank')" 
                            style="display: flex; align-items: center; gap: 4px; background-color: #16a34a; color: white; border: none; border-radius: 6px; padding: 4px 8px; cursor: pointer; font-size: 11px;"
                        >
                            🖼️ Gallery
                        </button>
                    </div>
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

        function performSearch() {
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
        };

        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');

        // Klik tombol cari
        searchBtn.addEventListener('click', performSearch);

        // Tekan Enter di input juga trigger pencarian
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault(); // supaya form tidak reload halaman
                performSearch();
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
