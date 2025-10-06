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
                                    <button type="button" class="btn btn-outline-info dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="fas fa-cog me-1"></i>
                                        Options
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" id="toggleEditor">
                                                <i class="fas fa-edit me-2"></i>Switch to Simple Editor
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" id="exportNotes">
                                                <i class="fas fa-download me-2"></i>Export Notes
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" id="importNotes">
                                                <i class="fas fa-upload me-2"></i>Import Notes
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="notes-editor-container">
                            <div id="quillEditor" style="height: 500px;">
                                {!! $note->content ?? '' !!}
                            </div>
                        </div>
                        <hr>
                        <div class="mt-4">
                            <!-- Statistics -->
                            <div class="row mt-3 mb-3">
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light text-center">
                                        <div class="card-body py-2">
                                            <h6 class="card-title mb-1 text-primary" id="wordCount">0</h6>
                                            <small class="text-muted">Words</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light text-center">
                                        <div class="card-body py-2">
                                            <h6 class="card-title mb-1 text-success" id="charCount">0</h6>
                                            <small class="text-muted">Characters</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light text-center">
                                        <div class="card-body py-2">
                                            <h6 class="card-title mb-1 text-info" id="lineCount">0</h6>
                                            <small class="text-muted">Lines</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light text-center">
                                        <div class="card-body py-2">
                                            <h6 class="card-title mb-1 text-warning" id="lastSaved">Never</h6>
                                            <small class="text-muted">Last Saved</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-info border-0 bg-light">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">Professional Features</h6>
                                                <ul class="mb-0 small">
                                                    <li>Rich text formatting with full toolbar</li>
                                                    <li>Auto-save every 5 seconds</li>
                                                    <li>Export/Import functionality</li>
                                                    <li>Word count and character tracking</li>
                                                    <li>Full-screen editing mode</li>
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
                                                <h6 class="alert-heading mb-1">Privacy & Security</h6>
                                                <p class="mb-0 small">Your notes are private to you. Only you can view and
                                                    edit your personal notes. All data is encrypted and stored securely.</p>
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

    <!-- File Input for Import -->
    <input type="file" id="importFileInput" accept=".txt,.html,.md" style="display: none;">
@endsection

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 12px;
        }

        .ql-container {
            border: none;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            line-height: 1.7;
        }

        .ql-editor {
            min-height: 400px;
            padding: 20px;
        }

        .ql-editor.ql-blank::before {
            font-style: normal;
            color: #6c757d;
            font-size: 14px;
        }

        .ql-toolbar .ql-formats {
            margin-right: 15px;
        }

        .ql-toolbar button:hover {
            color: #007bff;
        }

        .ql-toolbar button.ql-active {
            color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
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

        .card.border-0 {
            transition: all 0.3s ease;
        }

        .card.border-0:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <!-- Try multiple CDNs for Quill.js -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js" onerror="loadQuillFallback()"></script>
    <script>
        function loadQuillFallback() {
            console.log('Primary Quill CDN failed, trying fallback...');
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/quill@1.3.6/dist/quill.min.js';
            script.onerror = function() {
                console.error('All Quill CDNs failed');
                document.getElementById('quillEditor').innerHTML = `
                    <div class="alert alert-warning">
                        <h5>Network Issue Detected</h5>
                        <p>Unable to load the rich text editor due to network issues.</p>
                        <p>Please try:</p>
                        <ul>
                            <li>Checking your internet connection</li>
                            <li>Using the <a href="?editor=simple">Simple Editor</a> instead</li>
                            <li>Refreshing the page</li>
                        </ul>
                    </div>
                `;
            };
            script.onload = function() {
                console.log('Fallback Quill CDN loaded successfully');
                initializeQuill();
            };
            document.head.appendChild(script);
        }

        function initializeQuill() {
            // This will be called when Quill is successfully loaded
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, checking for Quill...');
            console.log('Quill available:', typeof Quill !== 'undefined');

            // Check if Quill is available
            if (typeof Quill === 'undefined') {
                console.error('Quill.js failed to load');
                document.getElementById('quillEditor').innerHTML = `
                    <div class="alert alert-danger">
                        <h5>Editor Loading Error</h5>
                        <p>Failed to load the rich text editor. Please try:</p>
                        <ul>
                            <li>Refreshing the page</li>
                            <li>Checking your internet connection</li>
                            <li>Using the <a href="?editor=simple">Simple Editor</a> instead</li>
                        </ul>
                    </div>
                `;
                return;
            }

            // Initialize Quill editor
            console.log('Initializing Quill editor...');
            let quill;
            try {
                quill = new Quill('#quillEditor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{
                                'header': [1, 2, 3, 4, 5, 6, false]
                            }],
                            [{
                                'font': []
                            }],
                            [{
                                'size': ['small', false, 'large', 'huge']
                            }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{
                                'color': []
                            }, {
                                'background': []
                            }],
                            [{
                                'script': 'sub'
                            }, {
                                'script': 'super'
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            [{
                                'indent': '-1'
                            }, {
                                'indent': '+1'
                            }],
                            [{
                                'direction': 'rtl'
                            }],
                            [{
                                'align': []
                            }],
                            ['blockquote', 'code-block'],
                            ['link', 'image', 'video'],
                            ['clean'],
                            [{
                                'formula': []
                            }]
                        ]
                    },
                    placeholder: 'Start writing your professional notes here...'
                });
                console.log('Quill editor initialized successfully');
            } catch (error) {
                console.error('Error initializing Quill:', error);
                document.getElementById('quillEditor').innerHTML = `
                    <div class="alert alert-danger">
                        <h5>Editor Initialization Error</h5>
                        <p>Failed to initialize the rich text editor. Error: ${error.message}</p>
                        <p>Please try using the <a href="?editor=simple">Simple Editor</a> instead.</p>
                    </div>
                `;
                return;
            }

            let autoSaveTimeout;
            let hasUnsavedChanges = false;

            // Update statistics
            function updateStats() {
                const text = quill.getText();
                const words = text.trim().split(/\s+/).filter(word => word.length > 0).length;
                const chars = text.length;
                const lines = text.split('\n').length;

                document.getElementById('wordCount').textContent = words;
                document.getElementById('charCount').textContent = chars;
                document.getElementById('lineCount').textContent = lines;
            }

            // Track changes
            quill.on('text-change', function() {
                hasUnsavedChanges = true;
                updateStats();

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
                        quill.setContents([]);
                        saveNotes();
                        updateStats();
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

            // Export notes
            document.getElementById('exportNotes').addEventListener('click', function(e) {
                e.preventDefault();
                const content = quill.root.innerHTML;
                const blob = new Blob([content], {
                    type: 'text/html'
                });
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

            // Import notes
            document.getElementById('importNotes').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('importFileInput').click();
            });

            document.getElementById('importFileInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const content = e.target.result;
                        quill.clipboard.dangerouslyPasteHTML(content);
                        updateStats();
                        toast.success('Import Complete', 'Your notes have been imported successfully!');
                    };
                    reader.readAsText(file);
                }
            });

            // Switch to simple editor
            document.getElementById('toggleEditor').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = '{{ route('bd.notes.index') }}?editor=simple';
            });

            // Save function
            function saveNotes() {
                const content = quill.root.innerHTML;

                if (!content.trim()) {
                    toast.warning('Empty Notes', 'Please add some content before saving.');
                    return;
                }

                showLoading(true);

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
                        showLoading(false);
                        if (data.success) {
                            hasUnsavedChanges = false;
                            toast.success('Notes Saved', data.message);

                            // Update last saved time
                            document.getElementById('lastSaved').textContent = new Date().toLocaleTimeString();

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

            // Warn before leaving if there are unsaved changes
            window.addEventListener('beforeunload', function(e) {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Initialize stats
            updateStats();
        });
    </script>
@endpush
