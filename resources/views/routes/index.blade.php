<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Rute Perjalanan - {{ Str::title($program->name) }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('programs.show', $program) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-2 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Preview Map -->
            @if ($activities->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Preview Lokasi Aktivitas</h3>
                        <div class="bg-gray-200 h-96 rounded-lg" id="previewMap">
                            <x-map :coordinates="$activities->map(
                                fn($a) => [
                                    'id' => $a->id,
                                    'lat' => $a->latitude,
                                    'lng' => $a->longitude,
                                    'name' => $a->name,
                                ],
                            )" id="map-route-{{ $program->id }}" :interactive="false" />
                        </div>
                    </div>
                </div>
            @endif

            <!-- Route Planner Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">Pilih Aktivitas untuk Rute</h3>

                    @if ($activities->count() > 0)
                        <form id="routeForm">
                            @csrf

                            <!-- Current Location Card -->
                            <div class="mb-6 p-4 border-2 border-dashed border-blue-300 rounded-lg bg-blue-50">
                                <div class="flex items-start space-x-3">
                                    <input type="checkbox" name="use_current_location" id="use_current_location"
                                        class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        onchange="handleCurrentLocationChange()">

                                    <div class="flex-1">
                                        <label for="use_current_location" class="cursor-pointer">
                                            <h4 class="text-md font-semibold mb-2 text-blue-800">
                                                📍 Lokasi Saya Saat Ini (Titik Awal)
                                            </h4>
                                            <div class="text-sm text-blue-600 mb-3" id="locationInfo">
                                                <p>Klik untuk menggunakan lokasi Anda sebagai titik awal rute</p>
                                                <p class="text-xs text-gray-500 mt-1">* Memerlukan izin akses lokasi
                                                    browser</p>
                                            </div>
                                        </label>

                                        <!-- Hidden inputs for coordinates -->
                                        <input type="hidden" name="current_lat" id="current_lat">
                                        <input type="hidden" name="current_lng" id="current_lng">
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 mb-6">
                                @foreach ($activities as $index => $activity)
                                    <div
                                        class="border border-gray-300 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                        <div class="flex items-start space-x-3">
                                            <input type="checkbox" name="selected_activities[]"
                                                value="{{ $activity->id }}" id="activity_{{ $activity->id }}"
                                                class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                data-name="{{ $activity->name }}" data-lat="{{ $activity->latitude }}"
                                                data-lng="{{ $activity->longitude }}"
                                                onchange="updateRouteButton(this)">

                                            <div class="flex-1">
                                                <label for="activity_{{ $activity->id }}" class="cursor-pointer">
                                                    <h4 class="text-md font-semibold mb-2">{{ $activity->name }}</h4>

                                                    <div class="text-sm text-gray-600 mb-3">
                                                        <p><strong>Koordinat:</strong></p>
                                                        <p>Lat: {{ $activity->latitude }}</p>
                                                        <p>Lng: {{ $activity->longitude }}</p>
                                                    </div>

                                                    <div class="text-sm text-gray-500">
                                                        @if ($activity->galleries && $activity->galleries->count() > 0)
                                                            <span
                                                                class="bg-slate-500 text-white px-2 py-1 rounded text-xs">
                                                                {{ $activity->galleries->count() }} Foto
                                                            </span>
                                                        @endif
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Selected Activities Counter -->
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">
                                    <span id="selectedCount">0</span> aktivitas dipilih (maksimal 10 aktivitas + lokasi saat ini)
                                    <span id="routePreview" class="text-blue-600 font-medium"></span>
                                </p>

                                <!-- Maximum Limit Warning -->
                                <div id="maxLimitWarning" class="mt-2 p-2 bg-orange-100 border border-orange-300 text-orange-700 text-xs rounded hidden">
                                    ⚠️ Maksimal 10 aktivitas telah tercapai. Hapus beberapa aktivitas untuk memilih yang lain.
                                </div>

                                <!-- Selected Activities List -->
                                <div id="selectedActivitiesList" class="mt-4 hidden">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Urutan Rute:</h4>
                                    <div id="routeOrderList" class="space-y-2"></div>
                                </div>
                            </div>

                            <!-- Generate Route Button -->
                            <div class="flex justify-center">
                                <button type="submit" id="generateRouteBtn" disabled
                                    class="bg-green-500 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    <x-heroicon-s-map class="w-5 h-5 inline mr-2" />
                                    Buka di Google Maps
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center">
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-6 rounded-lg">
                                Belum ada aktivitas dalam program ini.
                                <a href="{{ route('activities.create', $program) }}" class="underline font-medium">
                                    Tambah aktivitas terlebih dahulu.
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentLocation = null;
        let clickOrder = [];

        function updateRouteButton(clickedCheckbox = null) {
            if (clickedCheckbox) {
                const activityId = clickedCheckbox.value;
                if (clickedCheckbox.checked) {
                    if (!clickOrder.includes(activityId)) {
                        clickOrder.push(activityId);
                    }
                } else {
                    clickOrder = clickOrder.filter(id => id !== activityId);
                }
            }

            const checkboxes = document.querySelectorAll('input[name="selected_activities[]"]:checked');
            const useCurrentLocation = document.getElementById('use_current_location').checked;
            const count = checkboxes.length;
            const totalPoints = count + (useCurrentLocation ? 1 : 0);

            const generateBtn = document.getElementById('generateRouteBtn');
            const selectedCount = document.getElementById('selectedCount');
            const routePreview = document.getElementById('routePreview');
            const selectedActivitiesList = document.getElementById('selectedActivitiesList');
            const routeOrderList = document.getElementById('routeOrderList');

            const maxActivities = 10;
            const maxLimitWarning = document.getElementById('maxLimitWarning');
            const allActivityCheckboxes = document.querySelectorAll('input[name="selected_activities[]"]');
            
            allActivityCheckboxes.forEach(checkbox => {
                if (!checkbox.checked && count >= maxActivities) {
                    checkbox.disabled = true;
                    checkbox.parentElement.parentElement.classList.add('opacity-50');
                } else if (checkbox.disabled && count < maxActivities) {
                    checkbox.disabled = false;
                    checkbox.parentElement.parentElement.classList.remove('opacity-50');
                }
            });

            if (count >= maxActivities) {
                maxLimitWarning.classList.remove('hidden');
            } else {
                maxLimitWarning.classList.add('hidden');
            }

            selectedCount.textContent = totalPoints;

            if (totalPoints >= 2) {
                generateBtn.disabled = false;
                const startPoint = useCurrentLocation ? 'lokasi Anda' : 'aktivitas pertama';
                routePreview.textContent = `(${totalPoints} titik - mulai dari ${startPoint})`;
            } else {
                generateBtn.disabled = true;
                const needed = 2 - totalPoints;
                routePreview.textContent = needed > 0 ? `(Pilih ${needed} titik lagi)` : '';
            }

            updateSelectedActivitiesList();
        }

        function updateSelectedActivitiesList() {
            const useCurrentLocation = document.getElementById('use_current_location').checked;
            const selectedActivitiesList = document.getElementById('selectedActivitiesList');
            const routeOrderList = document.getElementById('routeOrderList');

            const selectedActivities = [];

            clickOrder.forEach(activityId => {
                const checkbox = document.querySelector(
                    `input[name="selected_activities[]"][value="${activityId}"]:checked`);
                if (checkbox) {
                    selectedActivities.push({
                        id: checkbox.value,
                        name: checkbox.dataset.name,
                        lat: checkbox.dataset.lat,
                        lng: checkbox.dataset.lng
                    });
                }
            });

            routeOrderList.innerHTML = '';

            if (selectedActivities.length > 0 || useCurrentLocation) {
                selectedActivitiesList.classList.remove('hidden');

                let orderNumber = 1;

                if (useCurrentLocation) {
                    const currentLocationItem = document.createElement('div');
                    currentLocationItem.className =
                        'flex items-center space-x-3 p-2 bg-blue-100 rounded border-l-4 border-blue-500';
                    currentLocationItem.innerHTML = `
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center font-bold">${orderNumber}</span>
                        <div>
                            <p class="font-medium text-blue-800">📍 Lokasi Saya Saat Ini (Titik Awal)</p>
                            <p class="text-xs text-blue-600">
                                ${currentLocation ? `Lat: ${currentLocation.lat.toFixed(6)}, Lng: ${currentLocation.lng.toFixed(6)}` : 'Koordinat akan didapat saat generate rute'}
                            </p>
                        </div>
                    `;
                    routeOrderList.appendChild(currentLocationItem);
                    orderNumber++;
                }

                selectedActivities.forEach(activity => {
                    const activityItem = document.createElement('div');
                    activityItem.className =
                        'flex items-center space-x-3 p-2 bg-white rounded border-l-4 border-gray-300';
                    activityItem.innerHTML = `
                        <span class="flex-shrink-0 w-6 h-6 bg-gray-500 text-white text-xs rounded-full flex items-center justify-center font-bold">${orderNumber}</span>
                        <div>
                            <p class="font-medium text-gray-800">${activity.name}</p>
                            <p class="text-xs text-gray-600">Lat: ${activity.lat}, Lng: ${activity.lng}</p>
                        </div>
                    `;
                    routeOrderList.appendChild(activityItem);
                    orderNumber++;
                });

            } else {
                selectedActivitiesList.classList.add('hidden');
            }
        }

        function handleCurrentLocationChange() {
            const checkbox = document.getElementById('use_current_location');
            const locationInfo = document.getElementById('locationInfo');

            if (checkbox.checked) {
                if (navigator.geolocation) {
                    locationInfo.innerHTML = '<p class="text-blue-600">🔄 Mendapatkan lokasi...</p>';

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            currentLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };

                            document.getElementById('current_lat').value = currentLocation.lat;
                            document.getElementById('current_lng').value = currentLocation.lng;

                            locationInfo.innerHTML = `
                                <p class="text-green-600">✅ Lokasi berhasil didapat</p>
                                <p class="text-xs text-gray-600">Lat: ${currentLocation.lat.toFixed(6)}, Lng: ${currentLocation.lng.toFixed(6)}</p>
                            `;
                            updateRouteButton();
                        },
                        function(error) {
                            checkbox.checked = false;
                            locationInfo.innerHTML = `
                                <p class="text-red-600">❌ Gagal mendapatkan lokasi</p>
                                <p class="text-xs text-red-500">${getGeolocationError(error.code)}</p>
                            `;
                            updateRouteButton();
                        }
                    );
                } else {
                    checkbox.checked = false;
                    locationInfo.innerHTML = '<p class="text-red-600">❌ Browser tidak mendukung geolocation</p>';
                }
            } else {
                currentLocation = null;
                document.getElementById('current_lat').value = '';
                document.getElementById('current_lng').value = '';
                locationInfo.innerHTML = `
                    <p>Klik untuk menggunakan lokasi Anda sebagai titik awal rute</p>
                    <p class="text-xs text-gray-500 mt-1">* Memerlukan izin akses lokasi browser</p>
                `;
                updateRouteButton();
            }
        }

        function getGeolocationError(code) {
            switch (code) {
                case 1:
                    return 'Akses lokasi ditolak';
                case 2:
                    return 'Lokasi tidak tersedia';
                case 3:
                    return 'Timeout mendapatkan lokasi';
                default:
                    return 'Error tidak diketahui';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateRouteButton();

            document.getElementById('routeForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const generateBtn = document.getElementById('generateRouteBtn');
                const originalText = generateBtn.innerHTML;

                generateBtn.innerHTML =
                    '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Membuka Google Maps...';
                generateBtn.disabled = true;

                const formData = new FormData();

                formData.append('_token', document.querySelector('input[name="_token"]').value);

                clickOrder.forEach(activityId => {
                    const checkbox = document.querySelector(
                        `input[name="selected_activities[]"][value="${activityId}"]:checked`);
                    if (checkbox) {
                        formData.append('selected_activities[]', activityId);
                    }
                });

                const useCurrentLocationCheckbox = document.getElementById('use_current_location');
                if (useCurrentLocationCheckbox.checked) {
                    formData.append('use_current_location', 'on');
                    formData.append('current_lat', document.getElementById('current_lat').value);
                    formData.append('current_lng', document.getElementById('current_lng').value);
                }

                fetch('{{ route('routes.generate', $program) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.open(data.google_maps_url, '_blank');
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat membuat rute');
                    })
                    .finally(() => {
                        generateBtn.innerHTML = originalText;
                        generateBtn.disabled = false;
                        updateRouteButton();
                    });
            });
        });
    </script>
</x-app-layout>
