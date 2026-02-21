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
                                            <div class="flex items-center gap-2">
                                                <a href="{{ URL::temporarySignedRoute('documents.show', now()->addMinutes(30), ['document' => $document->id]) }}" class="btn btn--secondary btn--sm">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                                @can('delete', $document)
                                                    <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline m-0 p-0 line-height-none" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn--secondary btn--sm text-red-600 hover:bg-red-50 hover:text-red-700 hover:border-red-200">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
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
