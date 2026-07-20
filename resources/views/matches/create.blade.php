@extends('layouts.app')
@section('title', 'Schedule Match')
@section('page-title', 'Schedule Match')
@section('breadcrumb')
<a href="{{ route('matches.index') }}">Matches</a> / Schedule
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">📝 Schedule New Match</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('matches.store') }}">
            @csrf

            {{-- Title --}}
            <div class="form-group">
                <label class="form-label" for="title">Match Name / Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') error @enderror" placeholder="e.g. Friendly vs Kibera Stars, or League Derby">
                @error('title')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Opponent (Away Team) --}}
            <div class="form-group">
                <label class="form-label" for="away_team">Opponent Team Name <span class="required">*</span></label>
                <input type="text" name="away_team" id="away_team" value="{{ old('away_team') }}" class="form-control @error('away_team') error @enderror" placeholder="e.g. Kibera Black Stars, Eastlands FC..." required>
                @error('away_team')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid">
                {{-- Type --}}
                <div class="form-group">
                    <label class="form-label" for="type">Match Type <span class="required">*</span></label>
                    <select name="type" id="type" class="form-control @error('type') error @enderror" required>
                        <option value="friendly" {{ old('type') === 'friendly' ? 'selected' : '' }}>Friendly</option>
                        <option value="league" {{ old('type') === 'league' ? 'selected' : '' }}>League Match</option>
                    </select>
                    @error('type')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Fee --}}
                <div class="form-group">
                    <label class="form-label" for="match_fee">Match Fee (KSh) <span class="required">*</span></label>
                    <input type="number" name="match_fee" id="match_fee" value="{{ old('match_fee', 200) }}" min="0" class="form-control @error('match_fee') error @enderror" required>
                    @error('match_fee')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                {{-- Date --}}
                <div class="form-group">
                    <label class="form-label" for="match_date">Match Date <span class="required">*</span></label>
                    <input type="date" name="match_date" id="match_date" value="{{ old('match_date') }}" class="form-control @error('match_date') error @enderror" required>
                    @error('match_date')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Time --}}
                <div class="form-group">
                    <label class="form-label" for="match_time">Kickoff Time <span class="required">*</span></label>
                    <input type="time" name="match_time" id="match_time" value="{{ old('match_time') }}" class="form-control @error('match_time') error @enderror" required>
                    @error('match_time')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                {{-- Venue --}}
                <div class="form-group">
                    <label class="form-label" for="venue">Venue <span class="required">*</span></label>
                    <input type="text" name="venue" id="venue" value="{{ old('venue', 'Camp Toyoyo Stadium, Nairobi') }}" class="form-control @error('venue') error @enderror" required>
                    @error('venue')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Response Deadline --}}
                <div class="form-group">
                    <label class="form-label" for="deadline">Availability Lock Deadline <span class="required">*</span></label>
                    <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}" class="form-control @error('deadline') error @enderror" required>
                    <span class="form-hint">Players cannot update availability after this deadline.</span>
                    @error('deadline')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Notes --}}
            <div class="form-group">
                <label class="form-label" for="notes">Additional Information / Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Kit requirements, meeting time details, etc...">{{ old('notes') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-between" style="margin-top: 24px;">
                <a href="{{ route('matches.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Schedule Match</button>
            </div>
        </form>
    </div>
</div>
@endsection
