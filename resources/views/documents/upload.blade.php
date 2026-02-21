<x-app-layout>
    <x-slot name="header">
        <div class="page-header">
            <div>
                <h2 class="page-title">{{ __('Upload Document') }}</h2>
                <p class="page-subtitle">Add a new file to the secure vault</p>
            </div>
            <a href="{{ route('documents.index') }}" class="btn btn--ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="doc-page">
        <div class="doc-container--narrow">
            <div class="card--elevated">
                <div class="card__body">
                    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" id="upload-form">
                        @csrf

                        <div class="mb-6">
                            <label for="document" class="block text-sm font-semibold text-gray-700 mb-3">
                                Select File
                            </label>

                            <div class="dropzone" id="dropzone">
                                <div class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48" stroke-width="1.5">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex justify-center text-sm text-gray-600">
                                        <label for="document" class="dropzone__label">
                                            <span>Choose a file</span>
                                            <input id="document" name="document" type="file" class="sr-only"
                                                   accept=".pdf,.docx,.pptx,.xlsx,.txt">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        PDF, DOCX, PPTX, XLSX, TXT — max {{ config('documents.max_size_kb', 20480) / 1024 }}MB
                                    </p>
                                </div>
                            </div>

                            {{-- Server-side validation errors --}}
                            @error('document')
                                <div class="alert alert--error mt-3">
                                    <div class="alert__icon">
                                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="alert__text text-red-700">{{ $message }}</p>
                                </div>
                            @enderror

                            {{-- Client-side error (shown before submit) --}}
                            <div id="client-error" class="alert alert--error mt-3 hidden">
                                <div class="alert__icon">
                                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p id="client-error-text" class="alert__text text-red-700"></p>
                            </div>

                            {{-- File info chip --}}
                            <div id="file-info" class="hidden mt-3 p-3 bg-blue-50 rounded-lg flex items-center gap-3 ring-1 ring-blue-500/20">
                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <span id="file-name" class="text-sm font-medium text-blue-900 truncate block"></span>
                                    <span id="file-size" class="text-xs text-blue-600"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <a href="{{ route('documents.index') }}" class="btn btn--secondary">Cancel</a>
                            <button type="submit" id="submit-btn" class="btn btn--primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security Notice --}}
            <div class="alert alert--warning mt-6">
                <div class="alert__icon">
                    <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="alert__title text-amber-800">Security Notice</p>
                    <p class="alert__text text-amber-700 mt-1">
                        Files are stored in encrypted private storage. Viewers can only preview documents through the secure portal — direct downloads are never provided.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const MAX_SIZE_KB = {{ config('documents.max_size_kb', 20480) }};
        const MAX_SIZE_BYTES = MAX_SIZE_KB * 1024;
        const MAX_SIZE_MB = MAX_SIZE_KB / 1024;
        const ALLOWED_EXTENSIONS = ['pdf', 'docx', 'pptx', 'xlsx', 'txt'];

        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('document');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const clientError = document.getElementById('client-error');
        const clientErrorText = document.getElementById('client-error-text');
        const submitBtn = document.getElementById('submit-btn');
        const uploadForm = document.getElementById('upload-form');

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        function getExtension(name) {
            return name.split('.').pop().toLowerCase();
        }

        function showError(msg) {
            clientErrorText.textContent = msg;
            clientError.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            dropzone.classList.remove('dropzone--active');
            dropzone.classList.add('border-red-400');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        function clearError() {
            clientError.classList.add('hidden');
            dropzone.classList.remove('border-red-400');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        function validateFile(file) {
            clearError();

            if (!file) return false;

            const ext = getExtension(file.name);

            if (!ALLOWED_EXTENSIONS.includes(ext)) {
                showError(`File type ".${ext}" is not allowed. Only PDF, DOCX, PPTX, XLSX, and TXT files are accepted.`);
                return false;
            }

            if (file.size > MAX_SIZE_BYTES) {
                showError(`File is too large (${formatSize(file.size)}). Maximum allowed size is ${MAX_SIZE_MB} MB.`);
                return false;
            }

            // Success — show file info
            fileName.textContent = file.name;
            fileSize.textContent = formatSize(file.size);
            fileInfo.classList.remove('hidden');
            dropzone.classList.add('dropzone--active');
            return true;
        }

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                validateFile(e.target.files[0]);
            }
        });

        // Prevent form submission if validation fails
        uploadForm.addEventListener('submit', (e) => {
            if (fileInput.files.length === 0) {
                e.preventDefault();
                showError('Please select a document to upload.');
                return;
            }
            if (!validateFile(fileInput.files[0])) {
                e.preventDefault();
            }
        });

        // Drag and drop
        ['dragenter', 'dragover'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.add('dropzone--active');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dropzone--active');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                validateFile(e.dataTransfer.files[0]);
            }
        });
    </script>
</x-app-layout>
