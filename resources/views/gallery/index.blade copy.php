<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <!-- Versi mobile (pakai limit 20) -->
                <span class="block sm:hidden">
                    Kegiatan {{ Str::limit(Str::title($activity->name), 15) }}
                </span>

                <!-- Versi desktop (full title) -->
                <span class="hidden sm:block">
                    Kegiatan {{ Str::title($activity->name) }}
                </span>
            </h2>
            <div class="flex space-x-2">

                @if (Auth::user())
                    <a href="{{ route('programs.show', $activity->program->id) }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex gap-1 items-center">
                        <x-heroicon-s-arrow-left class="w-5 h-5 text-white" />
                        <span class="hidden sm:inline">Kembali</span>
                    </a>

                    <a href="{{ route('gallery.create', ['activity' => $activity->id]) }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex gap-1 items-center">
                        <x-heroicon-s-camera class="w-5 h-5 text-white" />
                        <span class="hidden sm:inline">Upload</span>
                    </a>
                @else
                
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-2 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if ($galleries->count() > 0)
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 px-3 sm:px-0">
                            @foreach ($galleries as $gallery)
                                <div
                                    class="border border-gray-300 rounded-lg p-4 hover:shadow-lg transition-shadow justify-between">
                                    <div
                                        class="container max-h-[150px] flex justify-center items-center overflow-hidden mb-3 rounded-md">
                                        <img src="{{ asset('storage/' . $gallery->image_url) }}" alt="Gallery Image"
                                            class="object-cover">
                                    </div>
                                    <div class="flex justify-between text-center">
                                        <form action="{{ route('gallery.destroy', [$activity, $gallery]) }}"
                                            method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white text-xs px-4 py-2 rounded delete-btn">
                                                <div class="flex justify-center items-center">
                                                    <x-heroicon-s-trash class="w-4 h-4 text-white mr-1" />
                                                    Hapus
                                                </div>
                                            </button>
                                        </form>
                                        <a href="{{ route('gallery.download', [$activity, $gallery]) }}"
                                            class="bg-green-500 hover:bg-green-700 text-white text-xs px-4 py-2 rounded">
                                            <div class="flex justify-center items-center">
                                                <x-heroicon-s-arrow-down-tray class="w-4 h-4 text-white mr-1" />
                                                Unduh
                                            </div>
                                        </a>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $galleries->links() }}
                        </div>
                    @else
                        <div class="col-span-3">
                            <div
                                class="bg-red-50 border border-red-200 text-red-700 px-4 py-6 rounded-xl text-center shadow">
                                Belum ada foto yang diunggah</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
