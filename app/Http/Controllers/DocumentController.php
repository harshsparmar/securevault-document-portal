<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Services\DocumentService;
use App\Services\PreviewService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly DocumentService $documentService,
        private readonly PreviewService $previewService,
    ) {}

    /**
     * Display a listing of all documents.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Document::class);

        $documents = $this->documentService->getAll();

        return view('documents.index', compact('documents'));
    }

    /**
     * Show the upload form. Uploaders only.
     */
    public function create(Request $request): View
    {
        $this->authorize('upload', Document::class);

        return view('documents.upload');
    }

    /**
     * Store a newly uploaded document. Uploaders only.
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $this->authorize('upload', Document::class);

        $document = $this->documentService->store(
            $request->file('document'),
            $request->user()
        );

        // Generate preview synchronously for convertible types
        if ($document->requiresConversion()) {
            $this->previewService->generatePreview($document);
        }

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Show the document details/preview page.
     * Uses signed URL — link expires and can't be shared.
     */
    public function show(Request $request, Document $document): View
    {
        $this->authorize('view', $document);

        $textContent = null;
        if ($document->isText()) {
            $textContent = $this->previewService->getTextContent($document);
        }

        // Generate a signed preview URL (30 min expiry)
        $previewUrl = URL::temporarySignedRoute(
            'documents.preview',
            now()->addMinutes(30),
            ['document' => $document->id]
        );

        return view('documents.show', compact('document', 'textContent', 'previewUrl'));
    }

    /**
     * Stream the document preview inline
     */
    public function preview(Request $request, Document $document)
    {
        // Block direct browser navigation — only allow AJAX from our viewer
        if (! $request->ajax()) {
            abort(403, 'Direct access to document files is not permitted.');
        }

        $this->authorize('preview', $document);

        return $this->previewService->getPreviewResponse($document);
    }
}
