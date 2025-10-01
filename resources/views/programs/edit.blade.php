<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Program') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm  sm:p-10">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('programs.update', $program) }}" method="POST" class="sm:px-24">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Program
                            </label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $program->name) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi Program
                            </label>
                            <textarea name="description" id="description" rows="5"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>{{ old('description', $program->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="is_pin" class="block text-sm font-medium text-gray-700 mb-2">
                                Tandai sebagai pinned
                            </label>
                            <input type="hidden" name="is_pin" value="0">
                            <label for="is_pin" class="relative inline-block w-11 h-6 cursor-pointer">
                                <input type="checkbox" id="is_pin" name="is_pin" value="1" class="sr-only peer"
                                    {{ old('is_pin', $program->is_pin) ? 'checked' : '' }}>
                                <span
                                    class="block w-11 h-6 rounded-full bg-gray-300 peer-checked:bg-blue-500 transition-colors"></span>
                                <span
                                    class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></span>
                            </label>

                            @error('is_pin')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="flex justify-between items-center">
                            <a href="{{ route('programs.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Program
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
