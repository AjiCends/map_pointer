<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ Str::title($program->name) }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('programs.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
                <a href="{{ route('programs.edit', $program) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit Program
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-2 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Map Section -->
            @if ($program->activities->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-semibold mb-4 text-center">Peta Kegiatan</h3>
                        <div class="h-48 sm:h-96 rounded-lg" id="map">
                            {{-- Detail Program (warna per activity) --}}
                            <x-map :coordinates="$program->activities->map(
                                fn($a) => [
                                    'id' => $a->id,
                                    'lat' => $a->latitude,
                                    'lng' => $a->longitude,
                                    'name' => $a->name,
                                ],
                            )" id="map-program-{{ $program->id }}" />
                        </div>
                    </div>
                </div>
            @endif
            <!-- Program Information -->
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-3">Deskripsi Program</h3>
                    <div
                        class="container max-h-60 overflow-y-auto overscroll-contain bg-slate-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-700 leading-relaxed text-justify p-3">{{ $program->description }}</p>
                    </div>

                    <div class="mt-4 text-sm text-gray-500">
                        <p><strong>Dibuat:</strong> {{ $program->created_at->format('d M Y, H:i') }}</p>
                        <p><strong>Terakhir diupdate:</strong> {{ $program->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Activities Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Kegiatan dalam Program</h3>
                        <button onclick="window.location='{{ route('activities.create', $program) }}'"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div class="flex justify-center items-center">
                                <x-heroicon-s-plus class="w-5 h-5 text-white mr-1" />
                                Tambah
                            </div>
                        </button>
                    </div>

                    @if ($program->activities->count() > 0)
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($program->activities as $activity)
                                <div class="border border-gray-300 rounded-lg p-4 hover:shadow-lg transition-shadow ">
                                    <h4 class="text-md font-semibold mb-2">{{ $activity->name }}</h4>

                                    <div class="text-sm text-gray-600 mb-5">
                                        <p><strong>Koordinat:</strong></p>
                                        <p>Lat: {{ $activity->latitude }}</p>
                                        <p>Lng: {{ $activity->longitude }}</p>
                                    </div>

                                    <div class="text-sm text-gray-500 flex justify-between">
                                        <a href="{{ route('gallery.index', $activity->id) }}">
                                            @if ($activity->galleries && $activity->galleries->count() > 0)
                                                <span
                                                    class="hover:bg-slate-700 bg-slate-500 text-slate-800 px-3 py-[6px] rounded flex items-center space-x-1 justify-center transition-all duration-200 ease-in-out">
                                                    <x-heroicon-s-camera class="w-5 h-5 text-white" />
                                                    <p class="text-white">
                                                        {{ $activity->galleries->count() }} Foto
                                                    </p>
                                                </span>
                                            @else
                                                <span
                                                    class="hover:bg-slate-700 bg-slate-500 text-slate-800 px-3 py-[6px] rounded flex items-center space-x-1 justify-center transition-all duration-200 ease-in-out">
                                                    <x-heroicon-s-camera class="w-5 h-5 text-white" />
                                                    <p class="text-white">Foto</p>
                                                </span>
                                            @endif
                                        </a>
                                        <div class="flex justify-between items-center">
                                            <div class="flex space-x-1">
                                                <button
                                                    onclick="window.location='{{ route('activities.edit', $activity->id) }}'"
                                                    class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs px-4 py-2 rounded">
                                                    <div class="flex justify-center items-center">
                                                        <x-heroicon-s-pencil class="w-4 h-4 text-white mr-1" />
                                                        Edit
                                                    </div>
                                                </button>
                                                <button
                                                    onclick="event.preventDefault(); document.getElementById('delete-form-{{ $activity->id }}').submit();"
                                                    class="bg-red-500 hover:bg-red-700 text-white text-xs px-4 py-2 rounded">
                                                    <div class="flex justify-center items-center">
                                                        <x-heroicon-s-trash class="w-4 h-4 text-white mr-1" />
                                                        Hapus
                                                    </div>
                                                </button>
                                                <form id="delete-form-{{ $activity->id }}"
                                                    action="{{ route('activities.destroy', $activity->id) }}"
                                                    method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <div
                                class="bg-red-50 border border-red-200 text-red-700 px-4 py-6 rounded-lg sm:rounded-2xl text-center shadow">
                                Belum ada kegiatan dalam program ini</div>

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
