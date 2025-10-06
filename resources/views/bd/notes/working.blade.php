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
                                    <li><a class="dropdown-item" href="#" id="toggleQuill">
                                        <i class="fas fa-magic me-2"></i>Try Rich Editor
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
                        <!-- Editor Selection -->
                        <div class="mb-3">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-primary" id="simpleEditorBtn">
                                    <i class="fas fa-edit me-1"></i>Simple Editor
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="richEditorBtn">
                                    <i class="fas fa-magic me-1"></i>Rich Editor
                                </button>
                            </div>
                        </div>

                        <!-- Simple Editor -->
                        <div id="simpleEditor" class="editor-section">
                            <div class="editor-toolbar mb-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('bold')" title="Bold">
                                        <i class="fas fa-bold"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('italic')" title="Italic">
                                        <i class="fas fa-italic"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('underline')" title="Underline">
                                        <i class="fas fa-underline"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertList('unordered')" title="Bullet List">
                                        <i class="fas fa-list-ul"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertList('ordered')" title="Numbered List">
                                        <i class="fas fa-list-ol"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertLink()" title="Insert Link">
                                        <i class="fas fa-link"></i>
                                    </button>
                                </div>
                            </div>
                            <div contenteditable="true" id="simpleEditorContent" class="form-control" style="min-height: 400px;">
                                {!! $note->content ?? '' !!}
                            </div>
                        </div>

                        <!-- Rich Editor -->
                        <div id="richEditor" class="editor-section" style="display: none;">
                            <div id="quillEditor" style="height: 400px;">
                                {!! $note->content ?? '' !!}
                            </div>
                        </div>
                    </div>
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
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info border-0 bg-light">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Professional Features</h6>
                                            <ul class="mb-0 small">
                                                <li>Two editor modes: Simple and Rich</li>
                                                <li>Auto-save every 5 seconds</li>
                                                <li>Export functionality</li>
                                                <li>Real-time word and character count</li>
                                                <li>Full formatting support</li>
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
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .notes-editor-container {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
    }

    .editor-toolbar {
        border: 1px solid #dee2e6;
        border-bottom: none;
        border-radius: 0.375rem 0.375rem 0 0;
        background-color: #f8f9fa;
        padding: 0.5rem;
    }

    #simpleEditorContent {
        border-radius: 0 0 0.375rem 0.375rem;
        padding: 1rem;
        outline: none;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        line-height: 1.6;
    }

    #simpleEditorContent:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    #simpleEditorContent:empty:before {
        content: "Start writing your notes here...";
        color: #6c757d;
        font-style: italic;
    }

    .editor-section {
        min-height: 450px;
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
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
// Global variables
let autoSaveTimeout;
let hasUnsavedChanges = false;
let currentEditor = 'simple';
let quill = null;

// Text formatting functions
function formatText(command, value = null) {
    const editor = currentEditor === 'simple' ?
        document.getElementById('simpleEditorContent') :
        quill;

    if (currentEditor === 'simple') {
        editor.focus();
        document.execCommand(command, false, value);
    } else if (quill) {
        quill.focus();
        document.execCommand(command, false, value);
    }
}

function insertList(type) {
    if (currentEditor === 'simple') {
        const editor = document.getElementById('simpleEditorContent');
        editor.focus();
        if (type === 'ordered') {
            document.execCommand('insertOrderedList', false, null);
        } else {
            document.execCommand('insertUnorderedList', false, null);
        }
    }
}

function insertLink() {
    const editor = currentEditor === 'simple' ?
        document.getElementById('simpleEditorContent') :
        quill;

    editor.focus();
    const url = prompt('Enter URL:');
    if (url) {
        document.execCommand('createLink', false, url);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Editor switching
    document.getElementById('simpleEditorBtn').addEventListener('click', function() {
        switchEditor('simple');
    });

    document.getElementById('richEditorBtn').addEventListener('click', function() {
        switchEditor('rich');
    });

    function switchEditor(type) {
        currentEditor = type;

        if (type === 'simple') {
            document.getElementById('simpleEditor').style.display = 'block';
            document.getElementById('richEditor').style.display = 'none';
            document.getElementById('simpleEditorBtn').classList.add('btn-primary');
            document.getElementById('simpleEditorBtn').classList.remove('btn-outline-primary');
            document.getElementById('richEditorBtn').classList.remove('btn-primary');
            document.getElementById('richEditorBtn').classList.add('btn-outline-primary');
        } else {
            document.getElementById('simpleEditor').style.display = 'none';
            document.getElementById('richEditor').style.display = 'block';
            document.getElementById('richEditorBtn').classList.add('btn-primary');
            document.getElementById('richEditorBtn').classList.remove('btn-outline-primary');
            document.getElementById('simpleEditorBtn').classList.remove('btn-primary');
            document.getElementById('simpleEditorBtn').classList.add('btn-outline-primary');

            // Initialize Quill if not already done
            if (!quill && typeof Quill !== 'undefined') {
                try {
                    quill = new Quill('#quillEditor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['blockquote', 'code-block'],
                                ['link'],
                                ['clean']
                            ]
                        },
                        placeholder: 'Start writing your professional notes here...'
                    });

                    // Copy content from simple editor to Quill
                    const simpleContent = document.getElementById('simpleEditorContent').innerHTML;
                    if (simpleContent.trim()) {
                        quill.clipboard.dangerouslyPasteHTML(simpleContent);
                    }

                    // Set up Quill event listeners
                    quill.on('text-change', function() {
                        hasUnsavedChanges = true;
                        updateStats();
                        clearTimeout(autoSaveTimeout);
                        autoSaveTimeout = setTimeout(saveNotes, 5000);
                    });

                    console.log('Quill editor initialized successfully');
                } catch (error) {
                    console.error('Error initializing Quill:', error);
                    alert('Rich editor failed to load. Please use the simple editor.');
                    switchEditor('simple');
                }
            }
        }

        updateStats();
    }

    // Set up simple editor event listeners
    const simpleEditor = document.getElementById('simpleEditorContent');
    simpleEditor.addEventListener('input', function() {
        hasUnsavedChanges = true;
        updateStats();
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(saveNotes, 5000);
    });

    // Save button
    document.getElementById('saveBtn').addEventListener('click', saveNotes);

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
                if (currentEditor === 'simple') {
                    simpleEditor.innerHTML = '';
                } else if (quill) {
                    quill.setContents([]);
                }
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
        let content;

        if (currentEditor === 'simple') {
            content = simpleEditor.innerHTML;
        } else if (quill) {
            content = quill.root.innerHTML;
        } else {
            content = simpleEditor.innerHTML;
        }

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

    // Update statistics
    function updateStats() {
        let text;

        if (currentEditor === 'simple') {
            text = simpleEditor.innerText || simpleEditor.textContent || '';
        } else if (quill) {
            text = quill.getText();
        } else {
            text = simpleEditor.innerText || simpleEditor.textContent || '';
        }

        const words = text.trim().split(/\s+/).filter(word => word.length > 0).length;
        const chars = text.length;
        const lines = text.split('\n').length;

        document.getElementById('wordCount').textContent = words;
        document.getElementById('charCount').textContent = chars;
        document.getElementById('lineCount').textContent = lines;
    }

    // Save function
    function saveNotes() {
        let content;

        if (currentEditor === 'simple') {
            content = simpleEditor.innerHTML;
        } else if (quill) {
            content = quill.root.innerHTML;
        } else {
            content = simpleEditor.innerHTML;
        }

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
