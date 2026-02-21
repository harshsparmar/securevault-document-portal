<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed Document Types
    |--------------------------------------------------------------------------
    |
    | Config-driven whitelist of uploadable document types.
    | Each entry maps an extension to its allowed MIME types.
    |
    */

    'allowed_types' => [
        'pdf'  => ['application/pdf'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'txt'  => ['text/plain'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Maximum Upload Size (KB)
    |--------------------------------------------------------------------------
    */

    'max_size_kb' => env('DOCUMENT_MAX_SIZE_KB', 20480), // 20 MB

    /*
    |--------------------------------------------------------------------------
    | Storage Paths (relative to storage/app)
    |--------------------------------------------------------------------------
    */

    'storage_path'  => 'private/documents',
    'preview_path'  => 'private/previews',

    /*
    |--------------------------------------------------------------------------
    | Convertible Types
    |--------------------------------------------------------------------------
    |
    | File extensions that require conversion to HTML for preview.
    | Uses phpoffice libraries (pure PHP — no external dependencies).
    |
    */

    'convertible_types' => ['docx', 'pptx', 'xlsx'],

    /*
    |--------------------------------------------------------------------------
    | Inline Renderable Types
    |--------------------------------------------------------------------------
    |
    | Types that can be rendered inline without conversion.
    |
    */

    'inline_types' => ['pdf', 'txt'],

    /*
    |--------------------------------------------------------------------------
    | Signed URL Expiry (minutes)
    |--------------------------------------------------------------------------
    |
    | How long document view/preview URLs remain valid.
    |
    */

    'url_expiry_minutes' => env('DOCUMENT_URL_EXPIRY', 30),

];
