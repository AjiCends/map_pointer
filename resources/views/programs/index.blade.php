<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Program Saya') }}
            </h2>
            <a href="{{ route('programs.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tambah Program
            </a>
        </div>
    </x-slot>
    <div class="py-2 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-2">
                    <a href="{{ route('full_map.index') }}"
                        class="mb-3 inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <x-heroicon-o-map class="w-5 h-5 text-white" />
                        <span>Full Map</span>
                    </a>
                </div>
                <div class="p-6 text-gray-900">
                    @if ($programs->count() > 0)
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 px-3 sm:px-0 ">
                            @foreach ($programs as $program)
                                <div
                                    class="relative border border-gray-300 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    @if ($program->is_pin)
                                        <div class="absolute top-1 right-1">
                                            <div class="bg-white rounded-full shadow-md p-2">
                                                <x-heroicon-s-bookmark class="w-5 h-5 text-blue-600" />
                                            </div>
                                        </div>
                                    @endif
                                    <div
                                        class="container max-h-[100px] flex justify-center items-center overflow-hidden mb-3 rounded-md bg-gray-100">
                                        @if ($program->first_photo && $program->first_photo->image_url)
                                            <img src="{{ asset('storage/' . $program->first_photo->image_url) }}"
                                                class="w-full h-full object-cover" alt="Preview {{ $program->name }}"
                                                onerror="this.parentElement.innerHTML='<span class=\'text-gray-500 text-sm\'>Gambar tidak ditemukan</span>'">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <img src="/images/dummy.png" alt="No image available">
                                            </div>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-semibold mb-2">{{ $program->name }}</h3>
                                    <div class="container h-[30px] mb-5">
                                        <p class="text-gray-600 mb-3 text-sm ">
                                            {{ Str::limit($program->description, 70) }}
                                        </p>
                                    </div>

                                    <div class="text-sm text-gray-500 mb-3">
                                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            {{ $program->activities_count ?? $program->activities->count() }} Kegiatan
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <form action="{{ route('programs.destroy', $program) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white text-xs px-4 py-2 rounded delete-btn ">
                                                <div class="flex justify-center items-center">
                                                    <x-heroicon-s-trash class="w-4 h-4 text-white mr-1" />
                                                    Hapus
                                                </div>
                                            </button>
                                        </form>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('programs.edit', $program) }}"
                                                class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs px-3 py-2 rounded">
                                                <div class="flex justify-center items-center">
                                                    <x-heroicon-s-pencil class="w-4 h-4 text-white mr-1" />
                                                    Edit
                                                </div>
                                            </a>
                                            <a href="{{ route('programs.show', $program) }}"
                                                class="bg-green-500 hover:bg-green-700 text-white text-xs px-3 py-2 rounded">
                                                <div class="flex justify-center items-center">
                                                    Lihat
                                                    <x-heroicon-s-arrow-right class="w-4 h-4 text-white mr-1" />
                                                </div>
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $programs->links() }}
                        </div>
                    @else
                        <div class="col-span-3">
                            <div
                                class="bg-red-50 border border-red-200 text-red-700 px-4 py-6 rounded-xl text-center shadow">
                                Belum ada program yang dibuat</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
