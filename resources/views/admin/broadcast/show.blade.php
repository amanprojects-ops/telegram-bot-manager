@extends('layouts.admin')

@section('title', 'Broadcast Details: ' . $broadcast->name)

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Campaign Info</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
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
                    </dd>

                    <dt class="col-sm-4">Scheduled For</dt>
                    <dd class="col-sm-8">{{ $broadcast->scheduled_at ? $broadcast->scheduled_at->format('d M Y, h:i A') : 'Not scheduled' }}</dd>

                    <dt class="col-sm-4">Completed At</dt>
                    <dd class="col-sm-8">{{ $broadcast->completed_at ? $broadcast->completed_at->format('d M Y, h:i A') : 'N/A' }}</dd>

                    <dt class="col-sm-4">Progress</dt>
                    <dd class="col-sm-8">
                        @php
                            $processed = $broadcast->successful_sends + $broadcast->failed_sends;
                            $percentage = $broadcast->total_targets > 0 ? round(($processed / $broadcast->total_targets) * 100) : 0;
                        @endphp
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                        </div>
                        <small>{{ $processed }} / {{ $broadcast->total_targets }} ({{ $percentage }}%)</small>
                    </dd>

                    <dt class="col-sm-4">Success / Fail</dt>
                    <dd class="col-sm-8">
                        <span class="text-success"><i class="fas fa-check"></i> {{ $broadcast->successful_sends }}</span>
                        &nbsp;&nbsp;
                        <span class="text-danger"><i class="fas fa-times"></i> {{ $broadcast->failed_sends }}</span>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Message Preview</h3>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded mb-3">
                    {!! nl2br(e($broadcast->message)) !!}
                </div>
                
                @if($broadcast->keyboard_buttons)
                    <h6>Keyboard Layout:</h6>
                    <pre class="bg-dark p-2 rounded" style="color: #0f0; font-size: 12px;">{{ json_encode($broadcast->keyboard_buttons, JSON_PRETTY_PRINT) }}</pre>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delivery Logs</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Status</th>
                                <th>Error (if any)</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcast->messages as $msg)
                                <tr>
                                    <td>{{ $msg->telegram_user_id }}</td>
                                    <td>
                                        @if($msg->status === 'sent')
                                            <span class="badge badge-success">Sent</span>
                                        @elseif($msg->status === 'failed')
                                            <span class="badge badge-danger">Failed</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-danger" style="font-size: 0.85em;">{{ $msg->error }}</td>
                                    <td>{{ $msg->updated_at->format('M d, H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No logs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
