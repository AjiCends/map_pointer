<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Kegiatan Baru') }}
        </h2>
    </x-slot>

    <div class="py-2 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm sm:p-10">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('activities.store', $program->id) }}" method="POST" class="sm:px-24">
                        @csrf

                        {{-- MAP --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 text-center">
                                Pilih Lokasi di Peta
                            </label>
                            <p class="text-xs text-gray-500 mb-2 text-center">Klik pada peta untuk mengubah lokasi.</p>
                            <x-map id="map" :interactive="true" />
                        </div>

                        {{-- NAMA --}}
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Aktivitas
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- LAT --}}
                        <div class="mb-6">
                            <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                Latitude
                            </label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('latitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- LNG --}}
                        <div class="mb-6">
                            <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                Longitude
                            </label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('longitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="order_num" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Urut
                            </label>
                            <input type="text" name="order_num" id="order_num" value="{{ old('order_num') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('order_num')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('programs.show', $program->id) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Aktivitas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        document.getElementById('latitude').value = lat.toFixed(6);
                        document.getElementById('longitude').value = lng.toFixed(6);
                        
                        updateMapLocation(lat, lng);
                    },
                    function(error) {
                        let errorMessage = '';
                        switch(error.code) {
                            case 1:
                                errorMessage = 'User denied geolocation permission';
                                break;
                            case 2:
                                errorMessage = 'Position unavailable';
                                break;
                            case 3:
                                errorMessage = 'Geolocation timeout';
                                break;
                            default:
                                errorMessage = 'Unknown geolocation error';
                                break;
                        }
                        console.log('Auto-location failed:', errorMessage);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 8000,
                        maximumAge: 60000
                    }
                );
            } else {
                console.log('Geolocation not supported by browser');
            }
        });

        function updateMapLocation(lat, lng) {
            setTimeout(function() {
                if (typeof window.placeMarkerOnMap === 'function') {
                    window.placeMarkerOnMap(lat, lng);
                }
                if (typeof window.mapInstance !== 'undefined') {
                    window.mapInstance.setView([lat, lng], 15);
                }
            }, 500);
        }
    </script>
</x-app-layout>
