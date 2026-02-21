<x-app-layout>
    <x-slot name="header">
        <div class="page-header">
            <div>
                <h2 class="page-title">{{ __('Documents') }}</h2>
                <p class="page-subtitle">{{ $documents->count() }} {{ Str::plural('file', $documents->count()) }} in the secure vault</p>
            </div>
            @if(auth()->user()->isUploader())
                <a href="{{ route('documents.create') }}" class="btn btn--primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Upload Document') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="doc-page">
        <div class="doc-container">
            {{-- Success Alert --}}
            @if(session('success'))
                <div class="alert alert--success mb-6">
                    <div class="alert__icon">
                        <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="alert__text text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="card--elevated">
                @if($documents->isEmpty())
                    <div class="empty-state">
                        <svg class="empty-state__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                        </svg>
                        <p class="empty-state__text">No documents uploaded yet.</p>
                        @if(auth()->user()->isUploader())
                            <a href="{{ route('documents.create') }}" class="btn btn--primary btn--sm mt-4">Upload your first document</a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="doc-table">
                            <thead class="doc-table__head">
                                <tr>
                                    <th class="doc-table__th">Document</th>
                                    <th class="doc-table__th">Type</th>
                                    <th class="doc-table__th">Uploaded By</th>
                                    <th class="doc-table__th">Date</th>
                                    <th class="doc-table__th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($documents as $document)
                                    <tr class="doc-table__row">
                                        <td class="doc-table__td">
                                            <div class="flex items-center gap-3">
                                                <div class="file-icon @if($document->isPdf()) file-icon--pdf @elseif($document->isText()) file-icon--txt @else file-icon--office @endif">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span class="doc-table__name">{{ $document->original_name }}</span>
                                            </div>
                                        </td>
                                        <td class="doc-table__td">
                                            <span class="badge @if($document->isPdf()) badge--pdf @elseif($document->isText()) badge--txt @else badge--office @endif">
                                                {{ strtoupper($document->extension) }}
                                            </span>
                                        </td>
                                        <td class="doc-table__td doc-table__meta">
                                            {{ $document->user->name }}
                                        </td>
                                        <td class="doc-table__td doc-table__meta">
                                            {{ $document->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="doc-table__td">
                                            <a href="{{ URL::temporarySignedRoute('documents.show', now()->addMinutes(30), ['document' => $document->id]) }}" class="doc-table__action">
                                                View →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
