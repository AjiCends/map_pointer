<x-app-layout>
    {{-- <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot> --}}

    <!-- Parent harus punya tinggi fix -->
    <div class="py-2 sm:py-12 overflow-hidden" style="height: calc(100vh - 64px);">
        <div class="max-w-7xl sm:mx-auto sm:px-6 lg:px-8 mx-4 h-full">
            
            {{-- Login Alert --}}
            @if (session('success'))
                <script>
                    var notyf = new Notyf();
                    notyf.success(@json(session('success')));
                </script>
            @endif

            <!-- Tambah h-full di flex parent -->
            <div class="flex flex-col lg:flex-row gap-6 mb-5 h-full">
                <!-- Map Section -->
                <div class="lg:basis-2/3 bg-slate-100 border shadow-lg p-2 rounded-lg sm:rounded-xl">
                    <div class="w-full aspect-square lg:h-full lg:aspect-auto">
                        <x-map-dashboard :coordinates="$pined_program->flatMap(
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
                </div>

                <!-- Grid Program -->
                <div class="lg:basis-1/3 relative">
                    <div id="programs-container"
                        class="overflow-y-auto no-scrollbar pr-2 bg-slate-100 border shadow-lg p-2 rounded-lg sm:rounded-xl"
                        style="height: calc(100vh - 165px);">

                        <div class="flex flex-col px-3">
                            <div class="flex justify-center items-center gap-2 bg-gray-100 p-2 rounded-lg mb-4 border">
                                <span class="text-md font-bold text-gray-600">Scroll Bawah</span>
                                <x-heroicon-o-arrow-down-circle class="w-6 h-6 text-gray-600" />
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:gap-5 py-1">
                                @forelse($programs as $pk)
                                    <a href="{{ route('programs.show', $pk->id) }}">
                                        <div
                                            class="group bg-white rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 border border-gray-100 hover:border-blue-200 transition-all duration-300 flex flex-col overflow-hidden">

                                            <!-- Thumbnail -->
                                            <div
                                                class="h-20 flex items-center justify-center overflow-hidden bg-gray-100">
                                                @php
                                                    $firstActivity = $pk->activities->first();
                                                    $firstGallery = $firstActivity?->galleries->first();
                                                @endphp
                                                @if ($firstGallery && $firstGallery->image_url)
                                                    <img src="{{ asset('storage/' . $firstGallery->image_url) }}"
                                                        class="w-full h-full object-cover"
                                                        alt="Preview {{ $pk->name }}"
                                                        onerror="this.parentElement.innerHTML='<span class=\'text-gray-500 text-xs\'>Gambar tidak ditemukan</span>'">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gradient-to-r from-slate-300 to-slate-400 flex items-center justify-center">
                                                        <x-heroicon-o-photo
                                                            class="w-10 h-10 text-gray-700 group-hover:text-indigo-600 transition-colors" />
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Content -->
                                            <div class="p-5 flex flex-col flex-1">
                                                <h5
                                                    class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors mb-2">
                                                    {{ $pk->name }}
                                                </h5>
                                                <p class="text-gray-600 text-sm text-justify flex-1">
                                                    {{ Str::limit($pk->description, 100) }}
                                                </p>
                                            </div>

                                            <!-- Footer -->
                                            <div
                                                class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between text-sm text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <x-heroicon-o-map class="w-4 h-4" /> {{ $pk->activities->count() }}
                                                    Kegiatan
                                                </span>
                                                <span class="text-blue-600 font-medium group-hover:underline">Detail
                                                    →</span>
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

                    <!-- Scroll to Top Button -->
                    <button id="scrollToTopBtn"
                        class=" hidden absolute sm:bottom-6 sm:right-6 z-50 bg-gray-500 hover:bg-gray-600 text-white p-3 rounded-full shadow-lg transition hover:scale-110 hover:shadow-xl">
                        <x-heroicon-o-chevron-up class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <footer class="hidden sm:block text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Pemerintah Kabupaten Jember. All rights reserved.
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById("programs-container");
            const btn = document.getElementById("scrollToTopBtn");

            container.addEventListener("scroll", function() {
                if (container.scrollTop > 200) {
                    btn.classList.remove("hidden");
                } else {
                    btn.classList.add("hidden");
                }
            });

            btn.addEventListener("click", function() {
                container.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            });
        });
    </script>
</x-app-layout>
