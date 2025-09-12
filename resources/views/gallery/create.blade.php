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
                            Maksimal 10 foto, otomatis dikompres untuk mengoptimalkan upload. Klik untuk menambah foto
                            lagi.
                        </label>

                        <!-- Input untuk memilih foto -->
                        <input type="file" id="photos" accept="image/*" multiple
                            class="block w-full text-sm text-gray-500 
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-full file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100">

                        <!-- Progress Bar -->
                        <div id="compressionProgress" class="mt-3 hidden">
                            <div class="text-sm text-gray-600 mb-1">
                                Mengkompres foto: <span id="progressText">0/0</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>
                        </div>

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

    <!-- Include Compressor.js from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.2.1/compressor.min.js"
        onload="console.log('Compressor.js loaded successfully')" onerror="console.error('Failed to load Compressor.js')">
    </script>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedFiles = [];
            const maxFiles = 10;
            const maxFileSize = 10 * 1024 * 1024;
            let isCompressing = false;

            const $ = id => document.getElementById(id);
            const photosInput = $('photos');
            const finalPhotos = $('finalPhotos');
            const previewContainer = $('previewContainer');
            const previewList = $('previewList');
            const photoCount = $('photoCount');
            const currentCount = $('currentCount');
            const submitBtn = $('submitBtn');
            const uploadCount = $('uploadCount');
            const compressionProgress = $('compressionProgress');
            const progressBar = $('progressBar');
            const progressText = $('progressText');

            photosInput.addEventListener('change', handleFileSelect);

            async function handleFileSelect(event) {
                if (isCompressing) return alert('Sedang memproses foto. Mohon tunggu sebentar.') || (event
                    .target.value = '');

                const files = Array.from(event.target.files).filter(file => {
                    if (file.size > maxFileSize) return alert(
                        `File ${file.name} terlalu besar. Maksimal ${formatFileSize(maxFileSize)}.`
                        );
                    if (selectedFiles.some(f => f.name === file.name && f.size === file.size))
                    return false;
                    if (selectedFiles.length >= maxFiles) return alert(`Maksimal ${maxFiles} foto.`);
                    return true;
                });

                if (!files.length) return event.target.value = '';

                isCompressing = true;
                photosInput.disabled = true;
                compressionProgress.classList.remove('hidden');

                try {
                    await Promise.all(files.map(async (file, i) => {
                        progressText.textContent = `${i + 1}/${files.length} - ${file.name}`;
                        progressBar.style.width = `${((i + 1) / files.length) * 100}%`;

                        const compressed = await compressImage(file);
                        selectedFiles.push(compressed);
                        createPreview(compressed, selectedFiles.length - 1, file.size);
                    }));
                } catch (error) {
                    alert('Error mengkompres foto: ' + error.message);
                } finally {
                    isCompressing = false;
                    photosInput.disabled = false;
                    compressionProgress.classList.add('hidden');
                    event.target.value = '';
                    updateUI();
                }
            }

            function compressImage(file) {
                return new Promise(resolve => {
                    if (typeof Compressor === 'undefined') return resolve(file);

                    new Compressor(file, {
                        quality: file.size > 3 * 1024 * 1024 ? 0.6 : 0.8,
                        maxWidth: 1920,
                        maxHeight: 1080,
                        mimeType: 'image/jpeg',
                        convertSize: 2 * 1024 * 1024,
                        convertTypes: ['image/png', 'image/webp'],
                        success: compressed => {
                            const ext = compressed.type === 'image/jpeg' ? 'jpg' : file.name
                                .split('.').pop();
                            const name = file.name.substring(0, file.name.lastIndexOf('.'));
                            resolve(new File([compressed], `${name}_compressed.${ext}`, {
                                type: compressed.type,
                                lastModified: Date.now()
                            }));
                        },
                        error: () => resolve(file)
                    });
                });
            }

            function createPreview(file, index, originalSize) {
                const item = document.createElement('div');
                item.className = 'flex-shrink-0 relative';
                item.dataset.index = index;

                const reader = new FileReader();
                reader.onload = e => {
                    const size = formatFileSize(file.size);
                    const compressed = file.name.includes('_compressed');
                    const ratio = originalSize ? Math.round((1 - file.size / originalSize) * 100) : 0;
                    const sizeInfo = compressed && originalSize ? `${size} (-${ratio}%)` : size;

                    item.innerHTML = `
                        <div class="relative group">
                            <img src="${e.target.result}" class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200">
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white text-xs p-1 rounded-b-lg truncate">
                                ${sizeInfo} ${compressed ? '📦' : ''}
                            </div>
                            <button type="button" class="remove-btn absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold" data-index="${index}">×</button>
                        </div>`;
                    setTimeout(updateUI, 50);
                };
                reader.onerror = () => item.innerHTML =
                    `<div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center"><span class="text-xs text-gray-500">Error</span></div>`;

                previewList.appendChild(item);
                reader.readAsDataURL(file);
            }

            function removeFile(index) {
                selectedFiles.splice(index, 1);
                document.querySelector(`[data-index="${index}"]`)?.remove();

                previewList.querySelectorAll('[data-index]').forEach((item, i) => {
                    item.dataset.index = i;
                    item.querySelector('.remove-btn')?.setAttribute('data-index', i);
                });
                updateUI();
            }

            previewList.addEventListener('click', e => {
                if (e.target.classList.contains('remove-btn')) {
                    removeFile(parseInt(e.target.getAttribute('data-index')));
                }
            });

            function updateUI() {
                const count = selectedFiles.length;
                const canSubmit = count > 0 && !isCompressing;

                [previewContainer, photoCount].forEach(el => el?.classList.toggle('hidden', count === 0));
                uploadCount?.classList.toggle('hidden', count === 0);

                submitBtn.disabled = !canSubmit;
                if (currentCount) currentCount.textContent = count;
                if (uploadCount) uploadCount.textContent = `(${count})`;

                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                finalPhotos.files = dt.files;
            }

            const formatFileSize = bytes => {
                if (!bytes) return '0 Bytes';
                const k = 1024,
                    sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            };

            $('uploadForm').addEventListener('submit', e => {
                if (!selectedFiles.length) return e.preventDefault() || alert('Pilih minimal 1 foto.');
                if (isCompressing) return e.preventDefault() || alert('Masih mengkompres foto.');
                if (selectedFiles.length > 5 && !confirm(`Upload ${selectedFiles.length} foto?`)) {
                    return e.preventDefault();
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    `Mengupload ${selectedFiles.length} foto... <span class="animate-pulse">⏳</span>`;
                photosInput.disabled = true;
            });
        });
    </script>
</x-app-layout>
