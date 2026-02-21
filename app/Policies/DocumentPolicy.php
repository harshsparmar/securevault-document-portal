<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine if the user can upload documents.
     */
    public function upload(User $user): bool
    {
        return $user->isUploader();
    }

    /**
     * Determine if the user can view the document list.
     */
    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can view the list
    }

    /**
     * Determine if the user can view/preview a specific document.
     */
    public function view(User $user, Document $document): bool
    {
        return true; // Any authenticated user can view any document
    }

    /**
     * Determine if the user can preview a document.
     */
    public function preview(User $user, Document $document): bool
    {
        return true; // Any authenticated user can preview
    }
}
