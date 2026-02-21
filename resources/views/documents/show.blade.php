<x-app-layout>
    <x-slot name="header">
        <div class="page-header">
            <div class="flex items-center gap-3">
                <div class="file-icon @if($document->isPdf()) file-icon--pdf @elseif($document->isText()) file-icon--txt @else file-icon--office @endif">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h2 class="page-title">{{ $document->original_name }}</h2>
                    <p class="page-subtitle">Uploaded by {{ $document->user->name }} · {{ $document->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @can('delete', $document)
                    <form method="POST" action="{{ route('documents.destroy', $document) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--ghost text-red-500 hover:text-red-700 hover:bg-red-50">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                @endcan
                <a href="{{ route('documents.index') }}" class="btn btn--ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="doc-page">
        <div class="doc-container">
            {{-- Metadata Strip --}}
            <div class="card mb-6">
                <div class="card__body">
                    <dl class="meta-grid">
                        <div>
                            <dt class="meta-item__label">File Name</dt>
                            <dd class="meta-item__value">{{ $document->original_name }}</dd>
                        </div>
                        <div>
                            <dt class="meta-item__label">Type</dt>
                            <dd class="mt-1">
                                <span class="badge @if($document->isPdf()) badge--pdf @elseif($document->isText()) badge--txt @else badge--office @endif">
                                    {{ strtoupper($document->extension) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="meta-item__label">Uploaded By</dt>
                            <dd class="meta-item__value">{{ $document->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="meta-item__label">Upload Date</dt>
                            <dd class="meta-item__value">{{ $document->created_at->format('M d, Y \a\t H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Document Preview --}}
            @if($document->isText())
                {{-- TXT: Safe rendering --}}
                <div class="card--elevated">
                    <div class="card__body">
                        <pre class="text-preview">{{ $textContent }}</pre>
                    </div>
                </div>

            @elseif($document->isPdf())
                {{-- PDF Viewer (PDF.js) --}}
                <div class="pdf-viewer">
                    {{-- Toolbar --}}
                    <div class="pdf-toolbar">
                        <div class="flex items-center gap-2">
                            <button id="prev-page" class="pdf-toolbar__btn" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="text-xs text-gray-300">Page <span id="page-num" class="text-white font-medium">1</span> of <span id="page-count" class="text-white font-medium">—</span></span>
                            <button id="next-page" class="pdf-toolbar__btn" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button id="zoom-out" class="pdf-toolbar__zoom-btn">−</button>
                            <span id="zoom-level" class="pdf-toolbar__zoom-label">100%</span>
                            <button id="zoom-in" class="pdf-toolbar__zoom-btn">+</button>
                        </div>
                        <div class="pdf-toolbar__badge">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            Secure View
                        </div>
                    </div>

                    {{-- Loading --}}
                    <div id="pdf-loading" class="pdf-loading">
                        <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-gray-500">Loading document…</span>
                    </div>

                    {{-- Canvas container --}}
                    <div id="pdf-pages" class="pdf-canvas-area"
                         oncontextmenu="return false;"
                         onselectstart="return false;"
                         ondragstart="return false;">
                    </div>

                    {{-- Error state --}}
                    <div id="pdf-error" class="pdf-error">
                        <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        <p>Failed to load preview. Please try refreshing.</p>
                    </div>
                </div>

                {{-- PDF.js --}}
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs" type="module"></script>
                <script type="module">
                    import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs';
                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';

                    const PREVIEW_URL = @json($previewUrl);
                    let pdfDoc = null;
                    let currentPage = 1;
                    let zoomFactor = 1.0;
                    let baseContainerWidth = 0;
                    const BASE_RENDER_SCALE = 2.0;

                    async function loadPdf() {
                        try {
                            const res = await fetch(PREVIEW_URL, {
                                credentials: 'same-origin',
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            if (!res.ok) throw new Error('HTTP ' + res.status);

                            pdfDoc = await pdfjsLib.getDocument({ data: await res.arrayBuffer() }).promise;

                            document.getElementById('page-count').textContent = pdfDoc.numPages;
                            document.getElementById('pdf-loading').classList.add('hidden');
                            document.getElementById('prev-page').disabled = false;
                            document.getElementById('next-page').disabled = false;

                            await renderAllPages();
                            applyZoom();
                        } catch (err) {
                            console.error('PDF load error:', err);
                            document.getElementById('pdf-loading').classList.add('hidden');
                            document.getElementById('pdf-error').classList.remove('hidden');
                        }
                    }

                    async function renderAllPages() {
                        const container = document.getElementById('pdf-pages');
                        container.innerHTML = '';

                        for (let i = 1; i <= pdfDoc.numPages; i++) {
                            const page = await pdfDoc.getPage(i);
                            const viewport = page.getViewport({ scale: BASE_RENDER_SCALE });

                            const wrapper = document.createElement('div');
                            wrapper.className = 'pdf-page';

                            const canvas = document.createElement('canvas');
                            canvas.className = 'pdf-page__canvas';
                            canvas.width = viewport.width;
                            canvas.height = viewport.height;

                            const inner = document.createElement('div');
                            inner.className = 'pdf-page__inner';
                            inner.appendChild(canvas);
                            wrapper.appendChild(inner);
                            container.appendChild(wrapper);

                            await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
                        }

                        document.getElementById('page-num').textContent = '1';
                        updateNavButtons();
                    }

                    function applyZoom() {
                        const container = document.getElementById('pdf-pages');
                        if (!baseContainerWidth) baseContainerWidth = container.clientWidth;
                        const targetWidth = baseContainerWidth * zoomFactor;
                        container.querySelectorAll('.pdf-page__inner').forEach(el => {
                            el.style.width = targetWidth + 'px';
                            el.style.maxWidth = 'none';
                        });
                    }

                    function updateNavButtons() {
                        document.getElementById('prev-page').disabled = (currentPage <= 1);
                        document.getElementById('next-page').disabled = (currentPage >= (pdfDoc ? pdfDoc.numPages : 1));
                    }

                    function scrollToPage(num) {
                        const pages = document.getElementById('pdf-pages').children;
                        if (pages[num - 1]) pages[num - 1].scrollIntoView({ behavior: 'smooth', block: 'start' });
                        currentPage = num;
                        document.getElementById('page-num').textContent = num;
                        updateNavButtons();
                    }

                    document.getElementById('prev-page').addEventListener('click', () => { if (currentPage > 1) scrollToPage(currentPage - 1); });
                    document.getElementById('next-page').addEventListener('click', () => { if (pdfDoc && currentPage < pdfDoc.numPages) scrollToPage(currentPage + 1); });

                    document.getElementById('zoom-in').addEventListener('click', () => {
                        zoomFactor = Math.min(zoomFactor + 0.15, 2.5);
                        document.getElementById('zoom-level').textContent = Math.round(zoomFactor * 100) + '%';
                        applyZoom();
                    });
                    document.getElementById('zoom-out').addEventListener('click', () => {
                        zoomFactor = Math.max(zoomFactor - 0.15, 0.5);
                        document.getElementById('zoom-level').textContent = Math.round(zoomFactor * 100) + '%';
                        applyZoom();
                    });

                    document.addEventListener('keydown', (e) => {
                        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p')) e.preventDefault();
                    });

                    loadPdf();
                </script>

            @elseif($document->requiresConversion() && $document->hasPreview())
                {{-- DOCX/XLSX/PPTX: HTML preview in sandboxed iframe --}}
                <div class="pdf-viewer">
                    <div class="pdf-toolbar">
                        <span class="text-xs text-gray-300">{{ strtoupper($document->extension) }} Preview</span>
                        <div class="pdf-toolbar__badge">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            Secure View
                        </div>
                    </div>
                    <div id="html-preview-container" class="bg-white" style="height: 78vh; overflow: hidden;">
                        <div class="pdf-loading" id="html-loading">
                            <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm text-gray-500">Loading preview…</span>
                        </div>
                    </div>
                </div>
                <script>
                    (async function() {
                        const PREVIEW_URL = @json($previewUrl);
                        try {
                            const res = await fetch(PREVIEW_URL, {
                                credentials: 'same-origin',
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            const html = await res.text();

                            const container = document.getElementById('html-preview-container');
                            const iframe = document.createElement('iframe');
                            iframe.sandbox = 'allow-same-origin';
                            iframe.style.cssText = 'width:100%;height:100%;border:none;';
                            iframe.srcdoc = html;

                            document.getElementById('html-loading').remove();
                            container.appendChild(iframe);
                        } catch (err) {
                            console.error('Preview error:', err);
                            document.getElementById('html-loading').innerHTML =
                                '<p class="text-red-500 text-sm">Failed to load preview. Please try refreshing.</p>';
                        }
                    })();
                </script>

            @elseif($document->requiresConversion() && !$document->hasPreview())
                {{-- Conversion failed or pending --}}
                <div class="card--elevated">
                    <div class="empty-state">
                        <svg class="empty-state__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                        <p class="empty-state__text">Preview generation failed. The file may be corrupted or unsupported.</p>
                        <form method="POST" action="{{ route('documents.store') }}" class="mt-4">
                            @csrf
                            <button type="button" onclick="location.reload()" class="btn btn--secondary btn--sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Try Again
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Unsupported fallback --}}
                <div class="card--elevated">
                    <div class="empty-state">
                        <svg class="empty-state__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                        </svg>
                        <p class="empty-state__text">Preview is not available for this file type.</p>
                    </div>
                </div>
            @endif

            {{-- Security Info --}}
            <div class="alert alert--info mt-6">
                <div class="alert__icon">
                    <svg class="h-5 w-5 text-sky-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="alert__text text-sky-700">
                    Document rendered securely. The raw file is never exposed. Links expire after 30 minutes and cannot be shared.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
