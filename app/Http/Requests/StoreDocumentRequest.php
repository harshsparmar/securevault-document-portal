<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isUploader();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Validates both MIME type and file extension against the config whitelist.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedTypes = config('documents.allowed_types', []);
        $allowedMimes = collect($allowedTypes)->flatten()->unique()->implode(',');
        $allowedExtensions = implode(',', array_keys($allowedTypes));
        $maxSizeKb = config('documents.max_size_kb', 20480);

        return [
            'document' => [
                'required',
                'file',
                'max:' . $maxSizeKb,
                'mimes:' . $allowedExtensions,
                'mimetypes:' . $allowedMimes,
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'document.required'  => 'Please select a document to upload.',
            'document.file'      => 'The uploaded item must be a valid file.',
            'document.max'       => 'The document must not exceed ' . (config('documents.max_size_kb', 20480) / 1024) . ' MB.',
            'document.mimes'     => 'Only PDF, DOCX, PPTX, XLSX, and TXT files are allowed.',
            'document.mimetypes' => 'The file MIME type is not allowed. Only PDF, DOCX, PPTX, XLSX, and TXT are accepted.',
        ];
    }
}
