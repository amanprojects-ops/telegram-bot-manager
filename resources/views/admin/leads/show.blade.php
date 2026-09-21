@extends('layouts.admin')

@section('title', 'Lead Details: ' . $lead->lead_id)

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Lead Info -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">
                    {{ $lead->name }}
                    @if($lead->is_priority)
                        <i class="fas fa-star text-danger" title="High Priority"></i>
                    @endif
                </h3>

                <p class="text-muted text-center">{{ $lead->service->name ?? 'General Inquiry' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Lead ID</b> <a class="float-right">{{ $lead->lead_id }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> 
                        <a class="float-right">
                            <span class="badge 
                                @if($lead->status == 'new') badge-warning
                                @elseif($lead->status == 'contacted') badge-info
                                @elseif($lead->status == 'converted') badge-success
                                @else badge-secondary
                                @endif
                            ">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <b>Date</b> <a class="float-right">{{ $lead->created_at->format('d M Y, h:i A') }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Source</b> <a class="float-right">{{ ucfirst($lead->source) }}</a>
                    </li>
                </ul>

                <form method="POST" action="{{ route('admin.leads.priority', $lead) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ $lead->is_priority ? 'btn-danger' : 'btn-outline-danger' }} btn-block">
                        <i class="fas fa-star"></i> {{ $lead->is_priority ? 'Remove Priority' : 'Mark as Priority' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Contact Information</h3>
            </div>
            <div class="card-body">
                <strong><i class="fas fa-phone mr-1"></i> Phone</strong>
                <p class="text-muted">
                    <a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>
                    <br>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="btn btn-xs btn-success mt-1">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </p>

                <hr>

                <strong><i class="fas fa-user mr-1"></i> Telegram User</strong>
                <p class="text-muted">
                    @if($lead->user)
                        ID: {{ $lead->user->telegram_user_id }}<br>
                        @if($lead->user->username)
                            Username: <a href="https://t.me/{{ $lead->user->username }}" target="_blank">@{{ $lead->user->username }}</a>
                        @endif
                    @else
                        Not available
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Requirement Details</a></li>
                    <li class="nav-item"><a class="nav-link" href="#status" data-toggle="tab">Update Status</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="details">
                        <div class="post">
                            <div class="user-block mb-3">
                                <span class="username ml-0">
                                    Service Required
                                </span>
                            </div>
                            <p>
                                {{ $lead->service->name ?? 'Other / General' }}
                            </p>
                        </div>
                        
                        <div class="post">
                            <div class="user-block mb-3">
                                <span class="username ml-0">
                                    Budget
                                </span>
                            </div>
                            <p>
                                {{ $lead->budget ?? 'Not specified' }}
                            </p>
                        </div>

                        <div class="post">
                            <div class="user-block mb-3">
                                <span class="username ml-0">
                                    Detailed Requirement
                                </span>
                            </div>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($lead->requirement ?? 'No specific requirements provided.')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="status">
                        <form method="POST" action="{{ route('admin.leads.status', $lead) }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Current Status</label>
                                <div class="col-sm-10">
                                    <select name="status" class="form-control">
                                        <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                        <option value="in_progress" {{ $lead->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>Converted</option>
                                        <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
