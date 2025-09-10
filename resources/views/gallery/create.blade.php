<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Foto - {{ Str::title($activity->name) }}
        </h2>
    </x-slot>

    <div class="py-2 sm:py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg sm:p-12 p-8">
                <!-- Form Upload -->
                <form action="{{ route('gallery.store', $activity->id) }}" method="POST" enctype="multipart/form-data"
                    id="uploadForm">
                    @csrf

                    <!-- Input hidden untuk dikirim ke Controller -->
                    <input type="file" name="photos[]" id="finalPhotos" multiple hidden>

                    <!-- Container untuk Preview Foto -->
                    <div id="previewContainer" class="mb-5 hidden">
                        <label class="block text-md font-medium text-gray-700 mb-3">
                            Preview Foto
                        </label>
                        <div id="previewList"
                            class="flex overflow-x-auto space-x-4 pb-4 border rounded-lg p-4 bg-gray-50">
                            <!-- Preview items ditambahkan via JS -->
                        </div>
                    </div>

                    <div class="mb-5 border p-4 rounded-lg bg-gray-50">
                        <label for="photos" class="block text-md font-medium text-gray-700">
                            Silakan Unggah Foto
                        </label>
                        <label for="photos" class="block text-xs text-gray-500 mb-3 font-light">
                            Maksimal 10 foto, masing-masing maksimal 3MB. Klik untuk menambah foto lagi.
                        </label>

                        <!-- Input untuk memilih foto -->
                        <input type="file" id="photos" accept="image/*" multiple
                            class="block w-full text-sm text-gray-500 
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-full file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100">

                        <!-- Info jumlah foto -->
                        <div id="photoCount" class="mt-2 text-sm text-gray-600 hidden">
                            <span id="currentCount">0</span> / 10 foto dipilih
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('gallery.index', $activity->id) }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn" disabled
                            class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:bg-gray-300 disabled:cursor-not-allowed">
                            Upload <span id="uploadCount" class="hidden">(0)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        let selectedFiles = [];
        const maxFiles = 10;
        const maxFileSize = 3 * 1024 * 1024; // 3MB

        const photosInput = document.getElementById('photos');
        const finalPhotos = document.getElementById('finalPhotos');
        const previewContainer = document.getElementById('previewContainer');
        const previewList = document.getElementById('previewList');
        const photoCount = document.getElementById('photoCount');
        const currentCount = document.getElementById('currentCount');
        const submitBtn = document.getElementById('submitBtn');
        const uploadCount = document.getElementById('uploadCount');

        photosInput.addEventListener('change', handleFileSelect);

        function handleFileSelect(event) {
            const files = Array.from(event.target.files);

            files.forEach(file => {
                if (file.size > maxFileSize) {
                    alert(`File ${file.name} terlalu besar. Maksimal 3MB.`);
                    return;
                }
                if (selectedFiles.length >= maxFiles) {
                    alert(`Maksimal ${maxFiles} foto.`);
                    return;
                }

                // cek duplikat
                const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!isDuplicate) {
                    selectedFiles.push(file);
                    createPreview(file, selectedFiles.length - 1);
                }
            });

            updateUI();
            event.target.value = ''; // reset biar bisa pilih file yg sama lagi
        }

        function createPreview(file, index) {
            const previewItem = document.createElement('div');
            previewItem.className = 'flex-shrink-0 relative';
            previewItem.dataset.index = index;

            const reader = new FileReader();
            reader.onload = function(e) {
                previewItem.innerHTML = `
                    <div class="relative group">
                        <img src="${e.target.result}" 
                             class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200">
                        <button type="button" 
                                onclick="removeFile(${index})"
                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                            ×
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);

            previewList.appendChild(previewItem);
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            const previewItem = document.querySelector(`[data-index="${index}"]`);
            if (previewItem) previewItem.remove();

            // update index tombol delete
            const items = previewList.querySelectorAll('[data-index]');
            items.forEach((item, newIndex) => {
                item.dataset.index = newIndex;
                const btn = item.querySelector('button');
                if (btn) btn.setAttribute('onclick', `removeFile(${newIndex})`);
            });

            updateUI();
        }

        function updateUI() {
            const count = selectedFiles.length;

            if (count > 0) {
                previewContainer.classList.remove('hidden');
                photoCount.classList.remove('hidden');
                uploadCount.classList.remove('hidden');
                submitBtn.disabled = false;
            } else {
                previewContainer.classList.add('hidden');
                photoCount.classList.add('hidden');
                uploadCount.classList.add('hidden');
                submitBtn.disabled = true;
            }

            currentCount.textContent = count;
            uploadCount.textContent = `(${count})`;

            // isi input hidden agar Laravel bisa tangkap di $request->file('photos')
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            finalPhotos.files = dt.files;
        }

        // Validasi sebelum submit
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            if (selectedFiles.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 foto.');
            }
        });
    </script>
</x-app-layout>
