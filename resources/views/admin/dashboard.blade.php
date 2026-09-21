@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Total Bot Users -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalUsers ?? 0 }}</h3>
                <p>Total Bot Users</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <!-- Total Leads -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalLeads ?? 0 }}</h3>
                <p>Total Leads</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <a href="{{ route('admin.leads.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Active Sessions -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $activeSessions ?? 0 }}</h3>
                <p>Active Conversations</p>
            </div>
            <div class="icon">
                <i class="fas fa-comments"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Brochure Downloads -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalDownloads ?? 0 }}</h3>
                <p>Brochure Downloads</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title">Recent Leads</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Lead ID</th>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeads ?? [] as $lead)
                                <tr>
                                    <td><a href="#">{{ $lead->lead_id }}</a></td>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->service->name ?? 'Other' }}</td>
                                    <td>{{ $lead->budget }}</td>
                                    <td>
                                        @if($lead->status === 'new')
                                            <span class="badge badge-warning">New</span>
                                        @elseif($lead->status === 'contacted')
                                            <span class="badge badge-info">Contacted</span>
                                        @elseif($lead->status === 'converted')
                                            <span class="badge badge-success">Converted</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($lead->status) }}</span>
                                        @endif
                                        
                                        @if($lead->is_priority)
                                            <span class="badge badge-danger"><i class="fas fa-star"></i></span>
                                        @endif
                                    </td>
                                    <td>{{ $lead->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No leads found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-secondary float-right">View All Leads</a>
            </div>
        </div>
    </div>
</div>
@endsection
