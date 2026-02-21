<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    /**
     * Store a new document from an uploaded file.
     *
     * @param  UploadedFile  $file
     * @param  User  $user
     * @return Document
     */
    public function store(UploadedFile $file, User $user): Document
    {
        $storagePath = config('documents.storage_path');

        // Store file in private storage with a unique name
        $path = $file->store($storagePath, 'local');

        $document = Document::create([
            'user_id'       => $user->id,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'storage_path'  => $path,
        ]);

        return $document;
    }

    /**
     * Get all documents ordered by newest first.
     */
    public function getAll(): Collection
    {
        return Document::with('user')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Find a document by UUID.
     */
    public function findOrFail(string $uuid): Document
    {
        return Document::findOrFail($uuid);
    }

    /**
     * Stream the original file with Content-Disposition.
     */
    public function getInlineResponse(Document $document): StreamedResponse
    {
        $disk = Storage::disk('local');
        $path = $document->storage_path;

        abort_unless($disk->exists($path), 404, 'File not found.');

        return response()->stream(
            function () use ($disk, $path) {
                $stream = $disk->readStream($path);
                fpassthru($stream);
                fclose($stream);
            },
            200,
            [
                'Content-Type'        => $document->mime_type,
                'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
                'Cache-Control'       => 'no-store, no-cache, must-revalidate',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    /**
     * Delete a document and its associated storage files.
     */
    public function delete(Document $document): void
    {
        $disk = Storage::disk('local');
        
        $filesToDelete = array_filter([
            $document->storage_path,
            $document->preview_path,
        ]);
        
        if (!empty($filesToDelete)) {
            $disk->delete($filesToDelete);
        }

        $document->delete();
    }
}
