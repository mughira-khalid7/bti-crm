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
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="card-body">
                        <div class="notes-editor-container">
                            <div id="notesEditor" style="min-height: 500px;">
                                {!! $note->content ?? '' !!}
                            </div>
                            <!-- Fallback textarea in case Quill fails -->
                            <textarea id="fallbackEditor" class="form-control d-none" style="min-height: 500px;" placeholder="Start writing your notes here...">{{ strip_tags($note->content ?? '') }}</textarea>
                            <!-- Debug info -->
                            <div id="debugInfo" class="alert alert-info mt-2" style="display: none;">
                                <strong>Debug Info:</strong> <span id="debugText"></span>
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
                                                <p class="mb-0 small">Your notes are private to you. Only you can view and
                                                    edit your personal notes.</p>
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

    <!-- Loading overlay removed: autosave runs silently in background -->
@endsection

@push('styles')
    <style>
        .notes-editor-container {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .ql-toolbar {
            border-top: none;
            border-left: none;
            border-right: none;
            background-color: #f8f9fa;
        }

        .ql-container {
            border: none;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        .ql-editor {
            min-height: 450px;
            padding: 20px;
        }

        .ql-editor.ql-blank::before {
            font-style: normal;
            color: #6c757d;
            font-size: 14px;
        }

        #saveBtn {
            transition: all 0.3s ease;
        }

        #saveBtn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
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
        document.addEventListener('DOMContentLoaded', function() {
            let quill = null;
            let useFallback = false;

            console.log('DOM loaded, checking for Quill...');
            console.log('Quill available:', typeof Quill !== 'undefined');

            // Check if Quill is available
            if (typeof Quill === 'undefined') {
                console.error('Quill.js is not loaded, using fallback editor');
                useFallback = true;
                document.getElementById('notesEditor').style.display = 'none';
                document.getElementById('fallbackEditor').classList.remove('d-none');
            } else {
                // Initialize Quill editor
                try {
                    quill = new Quill('#notesEditor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'indent': '-1'}, { 'indent': '+1' }],
                                ['blockquote', 'code-block'],
                                ['link', 'image'],
                                ['clean']
                            ]
                        },
                        placeholder: 'Start writing your notes here...'
                    });
                    console.log('Quill editor initialized successfully');
                } catch (error) {
                    console.error('Error initializing Quill:', error);
                    useFallback = true;
                    document.getElementById('notesEditor').style.display = 'none';
                    document.getElementById('fallbackEditor').classList.remove('d-none');
                }
            }

            console.log('Editor setup complete. Using fallback:', useFallback);

            // Show debug info
            const debugInfo = document.getElementById('debugInfo');
            const debugText = document.getElementById('debugText');
            if (debugInfo && debugText) {
                debugText.textContent = `Quill loaded: ${typeof Quill !== 'undefined'}, Using fallback: ${useFallback}`;
                debugInfo.style.display = 'block';
            }

            let autoSaveTimeout;
            let hasUnsavedChanges = false;

            // Track changes
            if (quill && !useFallback) {
                quill.on('text-change', function() {
                    hasUnsavedChanges = true;
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(function() {
                        saveNotes(true);
                    }, 5000);
                });
            } else if (useFallback) {
                const fallbackEditor = document.getElementById('fallbackEditor');
                fallbackEditor.addEventListener('input', function() {
                    hasUnsavedChanges = true;
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(function() {
                        saveNotes(true);
                    }, 5000);
                });
            }

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
                        if (quill && !useFallback) {
                            quill.setContents([]);
                        } else if (useFallback) {
                            document.getElementById('fallbackEditor').value = '';
                        }
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
            function saveNotes(isAuto = false) {
                let content;

                if (quill && !useFallback) {
                    content = quill.root.innerHTML;
                } else if (useFallback) {
                    content = document.getElementById('fallbackEditor').value;
                } else {
                    toast.error('Save Failed', 'No editor available.');
                    return;
                }

                if (!content.trim()) {
                    if (!isAuto) { toast.warning('Empty Notes', 'Please add some content before saving.'); }
                    return;
                }

                fetch('{{ route('bd.notes.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            content: content
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            hasUnsavedChanges = false;
                            if (!isAuto) { toast.success('Notes Saved', data.message); }

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
                            if (!isAuto) { toast.error('Save Failed', 'Failed to save your notes. Please try again.'); }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (!isAuto) { toast.error('Save Failed', 'An error occurred while saving your notes.'); }
                    });
            }

            // Show/hide loading overlay
            function showLoading(show) { /* no-op: overlay removed for silent autosave */ }

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
