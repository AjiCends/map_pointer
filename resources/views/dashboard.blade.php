<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="py-12 flex flex-col h-screen justify-evenly">
        <div class="max-w-7xl sm:mx-auto sm:px-6 lg:px-8 mx-4">
            {{-- Login Alert --}}
            @if (session('success'))
                <script>
                    var notyf = new Notyf();
                    notyf.success(@json(session('success')));
                </script>
            @endif

            <div class="flex flex-col lg:flex-row gap-6 mb-5">
                <!-- Map Section -->
                <div class="w-full lg:basis-2/3 mb-8 lg:mb-0">
                    <div id="map" class="sm:rounded-2xl rounded-lg shadow-md border border-gray-300"
                        style="height: 400px;"></div>
                </div>
                <!-- Grid Program -->
                <div class="w-full lg:basis-1/3">
                    <div class="grid grid-cols-1 gap-4 sm:gap-6">
                        @forelse($program as $pk)
                            <a href="{{ route('programs.show', $pk->id) }}">
                                <div
                                    class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg hover:border hover:border-gray-300 transition flex flex-col ease-in-out">
                                    <div class="p-5 flex flex-col flex-1">
                                        <h5 class="text-lg font-bold text-blue-600 mb-2">{{ $pk->nama }}</h5>
                                        <p class="text-gray-600 flex-1">{{ Str::limit($pk->deskripsi, 80) }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div
                                class="bg-red-50 border border-red-200 text-red-700 px-4 py-6 rounded-lg sm:rounded-2xl text-center shadow">
                                Belum ada program kerja.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="text-justify p-4 bg-slate-100 border border-gray-300 ">
                <h1 class="font-bold uppercase mb-3">Jember Penuh Cinta</h1>
                <p class="text-base">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem a placeat recusandae minima nobis.
                    Nostrum accusamus provident in esse suscipit laborum commodi magni, officiis harum sequi vitae
                    ipsam. Suscipit dolorum harum ipsam iure alias cupiditate nesciunt ex fuga quos? Consectetur
                    aspernatur maiores voluptatibus ut beatae, atque eum quaerat et esse.
                </p>
            </div>
        </div>
        <footer class="text-center text-gray-500 text-sm py-4">
            &copy; {{ date('Y') }} Pemerintah Kabupaten Jember. All rights reserved.
        </footer>
    </div>
</x-app-layout>


{{-- Leaflet --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const LatLong = [-8.169823136924164, 113.70220374757565]; // Koordinat Kantor Bupati Jember
        const zoom = 18;
        const map = L.map("map").setView(LatLong, zoom);
        // Tile layer OpenStreetMap
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            minZoom: 7,
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Marker Kantor Bupati Jember
        L.marker(LatLong)
            .addTo(map)
            .bindPopup("<b>Kantor Bupati Jember</b><br>Jl. Sudarman No.1")
            .openPopup();

        function getBounds() {
            const southWest = new L.LatLng(-8.171363100764367, 113.69969478426162);
            const northEast = new L.LatLng(-8.169033528927415, 113.70405518027638);
            return new L.LatLngBounds(southWest, northEast);
        }
        // set maxBounds
        map.setMaxBounds(map.getBounds());
        // zoom the map to the polyline
        map.fitBounds(getBounds(), {
            reset: true
        });
        // Routing example
        // L.Routing.control({
        //     waypoints: [
        //         L.latLng(-8.169823136924164, 113.70220374757565),
        //         L.latLng(-8.16530099813156, 113.71664464473724)
        //     ]
        // }).addTo(map);

        // map.on('click', function(e) {
        //     // Ambil posisi user dulu
        //     if (navigator.geolocation) {
        //         navigator.geolocation.getCurrentPosition(function(pos) {
        //             var userLatLng = L.latLng(pos.coords.latitude, pos.coords.longitude);
        //             var destLatLng = L.latLng(e.latlng.lat, e.latlng.lng);

        //             var newMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);

        //             L.Routing.control({
        //                 waypoints: [
        //                     userLatLng,
        //                     destLatLng
        //                 ]
        //             }).on('routesfound', function(e) {
        //                 var routes = e.routes;
        //                 console.log(routes);

        //                 // Jika ingin animasi marker mengikuti rute:
        //                 // let marker = L.marker(userLatLng).addTo(map);
        //                 // routes[0].coordinates.forEach(function(coord, index) {
        //                 //     setTimeout(function() {
        //                 //         marker.setLatLng([coord.lat, coord.lng]);
        //                 //     }, 100 * index)
        //                 // });
        //             }).addTo(map);
        //         }, function(error) {
        //             alert('Lokasi tidak bisa diakses!');
        //         });
        //     } else {
        //         alert('Geolocation tidak didukung browser ini.');
        //     }
        // });
    });
</script>
