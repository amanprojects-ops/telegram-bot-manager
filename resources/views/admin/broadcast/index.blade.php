@extends('layouts.admin')

@section('title', 'Broadcast Campaigns')

@section('content')
<div class="row mb-3">
    <div class="col-md-12 text-right">
        <a href="{{ route('admin.broadcast.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Broadcast
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Targets</th>
                                <th>Success / Fail</th>
                                <th>Scheduled At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcasts as $broadcast)
                                <tr>
                                    <td>{{ $broadcast->name }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($broadcast->status == 'draft') badge-secondary
                                            @elseif($broadcast->status == 'queued') badge-warning
                                            @elseif($broadcast->status == 'processing') badge-info
                                            @elseif($broadcast->status == 'completed') badge-success
                                            @elseif($broadcast->status == 'failed') badge-danger
                                            @endif
                                        ">
                                            {{ ucfirst($broadcast->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $broadcast->total_targets }}</td>
                                    <td>
                                        <span class="text-success">{{ $broadcast->successful_sends }}</span> / 
                                        <span class="text-danger">{{ $broadcast->failed_sends }}</span>
                                    </td>
                                    <td>{{ $broadcast->scheduled_at ? $broadcast->scheduled_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.broadcast.show', $broadcast) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($broadcast->status === 'draft')
                                            <form method="POST" action="{{ route('admin.broadcast.start', $broadcast) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Start Now" onclick="return confirm('Start broadcast to all users now?')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No broadcast campaigns found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $broadcasts->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
