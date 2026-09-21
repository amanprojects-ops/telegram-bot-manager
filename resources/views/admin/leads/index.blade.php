@extends('layouts.admin')

@section('title', 'Leads Management')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <form method="GET" action="{{ route('admin.leads.index') }}" class="form-inline">
            <div class="form-group mr-2">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request('status'))
                <a href="{{ route('admin.leads.index') }}" class="btn btn-default ml-2">Clear</a>
            @endif
        </form>
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
                                <th>Lead ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                                <tr>
                                    <td>{{ $lead->lead_id }}</td>
                                    <td>
                                        {{ $lead->name }}
                                        @if($lead->is_priority)
                                            <i class="fas fa-star text-danger" title="High Priority"></i>
                                        @endif
                                    </td>
                                    <td><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
                                    <td>{{ $lead->service->name ?? 'Other' }}</td>
                                    <td>{{ $lead->budget }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.leads.status', $lead) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="form-control form-control-sm 
                                                @if($lead->status == 'new') bg-warning
                                                @elseif($lead->status == 'contacted') bg-info
                                                @elseif($lead->status == 'converted') bg-success
                                                @endif
                                            ">
                                                <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                                                <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                                <option value="in_progress" {{ $lead->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>Converted</option>
                                                <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $lead->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.leads.priority', $lead) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $lead->is_priority ? 'btn-danger' : 'btn-outline-secondary' }}" title="Toggle Priority">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No leads found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $leads->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
