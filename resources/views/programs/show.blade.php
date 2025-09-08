<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $program->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('programs.edit', $program) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit Program
                </a>
                <a href="{{ route('programs.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Program Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-3">Deskripsi Program</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $program->description }}</p>
                    
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
                        <button onclick="window.location='{{ route('activities.create', $program) }}'" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Kegiatan
                        </button>
                    </div>

                    @if($program->activities->count() > 0)
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($program->activities as $activity)
                                <div class="border border-gray-300 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <h4 class="text-md font-semibold mb-2">{{ $activity->name }}</h4>
                                    
                                    <div class="text-sm text-gray-600 mb-3">
                                        <p><strong>Koordinat:</strong></p>
                                        <p>Lat: {{ $activity->latitude }}</p>
                                        <p>Lng: {{ $activity->longitude }}</p>
                                    </div>

                                    <div class="text-sm text-gray-500 mb-3">
                                        <a href="{{ route('galleries.index', $activity->id) }}">Lihat Galeri</a>
                                        <span class="inline-block bg-purple-100 text-purple-800 px-2 py-1 rounded">
                                            {{ $activity->photos->count() }} Foto
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">
                                            Lihat
                                        </button>
                                        <div class="flex space-x-1">
                                            <button onclick="window.location='{{ route('activities.edit', [$activity->program_id, $activity->id]) }}'" class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs px-2 py-1 rounded">
                                                Edit
                                            </button>
                                            <button onclick="event.preventDefault(); document.getElementById('delete-form-{{ $activity->id }}').submit();" class="bg-red-500 hover:bg-red-700 text-white text-xs px-2 py-1 rounded">
                                                Hapus
                                            </button>
                                            <form id="delete-form-{{ $activity->id }}" action="{{ route('activities.destroy', [$activity->program_id, $activity->id]) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg mb-4">Belum ada kegiatan dalam program ini</div>
                            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Kegiatan Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Map Section (Placeholder) -->
            @if($program->activities->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Peta Kegiatan</h3>
                        <div class="bg-gray-200 h-96 rounded-lg flex items-center justify-center">
                            <p class="text-gray-600">Peta akan ditampilkan di sini</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
