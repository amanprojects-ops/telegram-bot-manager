@extends('layouts.admin')

@section('title', 'New Broadcast')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create Campaign</h3>
            </div>
            
            <form method="POST" action="{{ route('admin.broadcast.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Campaign Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. New Year Offer" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Message Content (supports HTML/Markdown)</label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6" placeholder="Enter your message here..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Inline Keyboard (JSON format) - Optional</label>
                        <textarea name="keyboard_buttons" class="form-control @error('keyboard_buttons') is-invalid @enderror" rows="4" placeholder='[
    [{"text": "Visit Website", "url": "https://amanprojects.com"}]
]'>{{ old('keyboard_buttons') }}</textarea>
                        <small class="form-text text-muted">Format: Array of rows, where each row is an array of buttons.</small>
                        @error('keyboard_buttons')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Schedule At (Leave blank to save as Draft)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at') }}">
                        @error('scheduled_at')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Broadcast</button>
                    <a href="{{ route('admin.broadcast.index') }}" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
