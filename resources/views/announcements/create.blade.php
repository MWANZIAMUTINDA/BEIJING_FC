@extends('layouts.app')
@section('title', 'Post Announcement')
@section('page-title', 'Post Announcement')
@section('breadcrumb')
<a href="{{ route('announcements.index') }}">Announcements</a> / Post
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">📝 Publish Club Announcement</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('announcements.store') }}">
            @csrf

            {{-- Title --}}
            <div class="form-group">
                <label class="form-label" for="title">Title <span class="required">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') error @enderror" placeholder="e.g. Training Rescheduled, Jersey Collection Day" required>
                @error('title')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Type --}}
            <div class="form-group">
                <label class="form-label" for="type">Announcement Type <span class="required">*</span></label>
                <select name="type" id="type" class="form-control @error('type') error @enderror" required>
                    <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>General Announcement</option>
                    <option value="match_reminder" {{ old('type') === 'match_reminder' ? 'selected' : '' }}>Match Reminder</option>
                    <option value="payment_alert" {{ old('type') === 'payment_alert' ? 'selected' : '' }}>Payment Alert</option>
                    <option value="league_update" {{ old('type') === 'league_update' ? 'selected' : '' }}>League Update</option>
                    <option value="urgent" {{ old('type') === 'urgent' ? 'selected' : '' }}>Urgent Alert ⚠️</option>
                </select>
                @error('type')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Match --}}
            <div class="form-group">
                <label class="form-label" for="match_id">Relate to Match</label>
                <select name="match_id" id="match_id" class="form-control @error('match_id') error @enderror">
                    <option value="">-- Choose Match (Optional) --</option>
                    @foreach($matches as $m)
                    <option value="{{ $m->id }}" {{ old('match_id') == $m->id ? 'selected' : '' }}>{{ $m->formatted_date }} vs {{ $m->opponent }}</option>
                    @endforeach
                </select>
                @error('match_id')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Body --}}
            <div class="form-group">
                <label class="form-label" for="body">Announcement Message <span class="required">*</span></label>
                <textarea name="body" id="body" class="form-control @error('body') error @enderror" rows="6" placeholder="Write details here..." required>{{ old('body') }}</textarea>
                @error('body')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- SMS Toggle --}}
            <div class="form-group">
                <label class="auth-checkbox-label" style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="send_sms" value="1" {{ old('send_sms') ? 'checked' : '' }}>
                    <span>Send SMS notification to all active players</span>
                </label>
                <span class="form-hint" style="margin-left: 23px;">This will broadcast the title and brief summary to all mobile numbers.</span>
                @error('send_sms')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-between" style="margin-top: 24px;">
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Publish Announcement</button>
            </div>
        </form>
    </div>
</div>
@endsection
