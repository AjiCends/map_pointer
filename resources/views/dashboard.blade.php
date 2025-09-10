<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>
    <div class="py-2 sm:py-12">
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
                <div class="lg:basis-2/3 mb-8 lg:mb-0">
                    {{-- Dashboard (warna per program): --}}
                    <x-map :coordinates="$programs->flatMap(
                        fn($program) => $program->activities->map(
                            fn($a) => [
                                'id' => $a->id,
                                'program_id' => $program->id,
                                'program_name' => $program->name,
                                'lat' => $a->latitude,
                                'lng' => $a->longitude,
                                'name' => $a->name,
                            ],
                        ),
                    )" id="map-dashboard" :group-by-program="true" />

                </div>
                <!-- Grid Program -->
                <div class="container h-screen lg:basis-1/3 overflow-y-auto pr-2">
                    <div class="grid grid-cols-1 gap-2 sm:gap-6 ">
                        @forelse($programs as $pk)
                            <a href="{{ route('programs.show', $pk->id) }}">
                                <div
                                    class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg hover:border hover:border-gray-300 transition flex flex-col ease-in-out">
                                    <div class="p-5 flex flex-col flex-1">
                                        <h5 class="text-lg font-bold text-blue-600 mb-2">{{ $pk->name }}</h5>
                                        <p class="text-gray-600 flex-1">{{ Str::limit($pk->description, 80) }}</p>
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
            <footer class="text-center text-gray-500 text-sm py-4">
                &copy; {{ date('Y') }} Pemerintah Kabupaten Jember. All rights reserved.
            </footer>
        </div>
    </div>
</x-app-layout>
