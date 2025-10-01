@extends('bd.layouts.master')

@section('title', 'Interviewing')
@section('page-title', 'Interviewing Proposals')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-video me-2"></i>Interviewing Proposals</h5>
            <a href="{{ route('bd.proposals.index') }}" class="btn btn-outline-secondary">Back to Proposals</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Connects</th>
                            <th>URL</th>
                            <th>Notes</th>
                            <th>Submitted At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proposals as $proposal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('bd.proposals.show', $proposal) }}" class="fw-semibold text-decoration-none">{{ $proposal->title }}</a>
                                    <div class="text-muted small">{{ Str::limit($proposal->job_description, 60) }}</div>
                                </td>
                                <td><strong>{{ $proposal->connects_used }}</strong></td>
                                <td>
                                    <a href="{{ $proposal->url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                                <td>{{ Str::limit($proposal->notes, 60) }}</td>
                                <td>{{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('bd.proposals.show', $proposal) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('bd.proposals.edit', $proposal) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No interviewing proposals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column align-items-center mt-4">
                <x-pagination :paginator="$proposals" />
            </div>
        </div>
    </div>
@endsection


