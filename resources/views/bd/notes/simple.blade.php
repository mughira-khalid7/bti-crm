@extends('bd.layouts.master')

@section('title', 'Resource (Notes) - BD CRM')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-dark fw-bold">
                                <i class="fas fa-sticky-note text-primary me-2"></i>
                                Resource (Notes)
                            </h4>
                            <p class="text-muted mb-0">Create and manage your personal notes and resources</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="saveBtn">
                                <i class="fas fa-save me-1"></i>
                                Save Notes
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clearBtn">
                                <i class="fas fa-eraser me-1"></i>
                                Clear
                            </button>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i>
                                    Options
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('bd.notes.index') }}?editor=quill">
                                        <i class="fas fa-magic me-2"></i>Switch to Rich Editor
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" id="exportNotes">
                                        <i class="fas fa-download me-2"></i>Export Notes
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="card-body">
                    <div class="notes-editor-container">
                        <div class="editor-toolbar mb-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Headings -->
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-heading"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="insertHeading(1)">Heading 1</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="insertHeading(2)">Heading 2</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="insertHeading(3)">Heading 3</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="formatText('formatBlock', 'p')">Normal Text</a></li>
                                    </ul>
                                </div>

                                <!-- Text Formatting -->
                                <button type="button" class="btn btn-outline-secondary" onclick="formatText('bold')" title="Bold">
                                    <i class="fas fa-bold"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatText('italic')" title="Italic">
                                    <i class="fas fa-italic"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatText('underline')" title="Underline">
                                    <i class="fas fa-underline"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatText('strikeThrough')" title="Strikethrough">
                                    <i class="fas fa-strikethrough"></i>
                                </button>

                                <!-- Lists -->
                                <button type="button" class="btn btn-outline-secondary" onclick="insertList('unordered')" title="Bullet List">
                                    <i class="fas fa-list-ul"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertList('ordered')" title="Numbered List">
                                    <i class="fas fa-list-ol"></i>
                                </button>

                                <!-- Alignment -->
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-align-left"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="formatText('justifyLeft')">Align Left</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="formatText('justifyCenter')">Align Center</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="formatText('justifyRight')">Align Right</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="formatText('justifyFull')">Justify</a></li>
                                    </ul>
                                </div>

                                <!-- Links and Quotes -->
                                <button type="button" class="btn btn-outline-secondary" onclick="insertLink()" title="Insert Link">
                                    <i class="fas fa-link"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatText('formatBlock', 'blockquote')" title="Quote">
                                    <i class="fas fa-quote-right"></i>
                                </button>

                                <!-- Undo/Redo -->
                                <button type="button" class="btn btn-outline-secondary" onclick="formatText('undo')" title="Undo">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatText('redo')" title="Redo">
                                    <i class="fas fa-redo"></i>
                                </button>

                                <!-- Clear Formatting -->
                                <button type="button" class="btn btn-outline-secondary" onclick="formatText('removeFormat')" title="Clear Formatting">
                                    <i class="fas fa-remove-format"></i>
                                </button>
                            </div>
                        </div>
                        <div contenteditable="true" id="notesEditor" class="form-control" style="min-height: 400px; border: 1px solid #dee2e6;">
                            {!! $note->content ?? '' !!}
                        </div>
                    </div>
                    <hr>
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info border-0 bg-light">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Tips for Effective Notes</h6>
                                            <ul class="mb-0 small">
                                                <li>Keep track of important client information</li>
                                                <li>Note down project requirements and deadlines</li>
                                                <li>Save useful resources and links</li>
                                                <li>Record meeting notes and action items</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning border-0 bg-light">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-shield-alt text-warning me-2 mt-1"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Privacy Notice</h6>
                                            <p class="mb-0 small">Your notes are private to you. Only you can view and edit your personal notes.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none"
    style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Saving...</span>
            </div>
            <p>Saving your notes...</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .editor-toolbar {
        border: 1px solid #dee2e6;
        border-bottom: none;
        border-radius: 0.375rem 0.375rem 0 0;
        background-color: #f8f9fa;
        padding: 0.5rem;
    }

    #notesEditor {
        border-radius: 0 0 0.375rem 0.375rem;
        padding: 1rem;
        outline: none;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        line-height: 1.6;
    }

    #notesEditor:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    #notesEditor:empty:before {
        content: "Start writing your notes here...";
        color: #6c757d;
        font-style: italic;
    }

    .alert {
        border-left: 4px solid;
    }

    .alert-info {
        border-left-color: #17a2b8;
    }

    .alert-warning {
        border-left-color: #ffc107;
    }
</style>
@endpush

@push('scripts')
<script>
// Global variables and functions for formatting
let autoSaveTimeout;
let hasUnsavedChanges = false;
let editor;

// Text formatting functions - defined globally
function formatText(command, value = null) {
    if (editor) {
        editor.focus();
        document.execCommand(command, false, value);
    }
}

function insertList(type) {
    if (editor) {
        editor.focus();
        if (type === 'ordered') {
            document.execCommand('insertOrderedList', false, null);
        } else {
            document.execCommand('insertUnorderedList', false, null);
        }
    }
}

function insertLink() {
    if (editor) {
        editor.focus();
        const url = prompt('Enter URL:');
        if (url) {
            document.execCommand('createLink', false, url);
        }
    }
}

function insertHeading(level) {
    if (editor) {
        editor.focus();
        document.execCommand('formatBlock', false, 'h' + level);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    editor = document.getElementById('notesEditor');

    // Track changes
    editor.addEventListener('input', function() {
        hasUnsavedChanges = true;
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            saveNotes();
        }, 5000);
    });

    // Save button
    document.getElementById('saveBtn').addEventListener('click', function() {
        saveNotes();
    });

    // Clear button
    document.getElementById('clearBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Clear Notes?',
            text: "This will permanently delete all your notes. This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear all notes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                editor.innerHTML = '';
                saveNotes();
                Swal.fire({
                    title: 'Cleared!',
                    text: 'Your notes have been cleared.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });

    // Save function
    function saveNotes() {
        const content = editor.innerHTML;

        if (!content.trim()) {
            toast.warning('Empty Notes', 'Please add some content before saving.');
            return;
        }

        showLoading(true);

        fetch('{{ route("bd.notes.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                content: content
            })
        })
        .then(response => response.json())
        .then(data => {
            showLoading(false);
            if (data.success) {
                hasUnsavedChanges = false;
                toast.success('Notes Saved', data.message);

                // Update button state
                const saveBtn = document.getElementById('saveBtn');
                saveBtn.innerHTML = '<i class="fas fa-check me-1"></i>Saved';
                saveBtn.classList.remove('btn-outline-primary');
                saveBtn.classList.add('btn-success');

                setTimeout(() => {
                    saveBtn.innerHTML = '<i class="fas fa-save me-1"></i>Save Notes';
                    saveBtn.classList.remove('btn-success');
                    saveBtn.classList.add('btn-outline-primary');
                }, 2000);
            } else {
                toast.error('Save Failed', 'Failed to save your notes. Please try again.');
            }
        })
        .catch(error => {
            showLoading(false);
            console.error('Error:', error);
            toast.error('Save Failed', 'An error occurred while saving your notes.');
        });
    }

    // Show/hide loading overlay
    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) {
            overlay.classList.remove('d-none');
        } else {
            overlay.classList.add('d-none');
        }
    }

    // Export notes
    document.getElementById('exportNotes').addEventListener('click', function(e) {
        e.preventDefault();
        const content = editor.innerHTML;
        const blob = new Blob([content], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'my-notes-' + new Date().toISOString().split('T')[0] + '.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        toast.success('Export Complete', 'Your notes have been exported successfully!');
    });

    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>
@endpush
