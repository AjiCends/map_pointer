<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Program Saya') }}
            </h2>
            <a href="{{ route('activities.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tambah Program
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($activities->count() > 0)
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($activities as $activity)
                                <div class="border border-gray-300 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <h3 class="text-lg font-semibold mb-2">{{ $activity->name }}</h3>
                                    <p class="text-gray-600 mb-3">{{ Str::limit($activity->description, 100) }}</p>

                                    <div class="text-sm text-gray-500 mb-3">
                                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            {{ $activity->activities_count ?? $activity->activities->count() }} Kegiatan
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('activities.show', $activity) }}"
                                                class="bg-green-500 hover:bg-green-700 text-white text-xs px-3 py-1 rounded">
                                                Lihat
                                            </a>
                                            <a href="{{ route('activities.edit', $activity) }}"
                                                class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs px-3 py-1 rounded">
                                                Edit
                                            </a>
                                        </div>

                                        <form action="{{ route('activities.destroy', $activity) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus activity ini?')"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white text-xs px-3 py-1 rounded">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $activities->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg mb-4">Belum ada activity yang dibuat</div>
                            <a href="{{ route('activities.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Buat Program Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
