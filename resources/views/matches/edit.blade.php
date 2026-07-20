@extends('layouts.app')
@section('title', 'Edit Match')
@section('page-title', 'Edit Match')
@section('breadcrumb')
<a href="{{ route('matches.index') }}">Matches</a> /
<a href="{{ route('matches.show', $match) }}">vs {{ $match->opponent }}</a> / Edit
@endsection

@section('content')
<div class="card" style="max-width:640px; margin:0 auto;">
    <div class="card-header">
        <span class="card-title">✏️ Edit Match: Beijing FC vs {{ $match->opponent }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('matches.update', $match) }}">
            @csrf @method('PUT')

            {{-- Title --}}
            <div class="form-group">
                <label class="form-label" for="title">Match Name / Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $match->title) }}"
                    class="form-control @error('title') error @enderror"
                    placeholder="e.g. League Derby vs Kibera Stars">
                @error('title')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Opponent --}}
            <div class="form-group">
                <label class="form-label" for="away_team">Opponent Team Name <span class="required">*</span></label>
                <input type="text" name="away_team" id="away_team" value="{{ old('away_team', $match->away_team) }}"
                    class="form-control @error('away_team') error @enderror"
                    placeholder="e.g. Kibera Black Stars" required>
                @error('away_team')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid">
                {{-- Type --}}
                <div class="form-group">
                    <label class="form-label" for="type">Match Type <span class="required">*</span></label>
                    <select name="type" id="type" class="form-control @error('type') error @enderror" required>
                        <option value="friendly" {{ old('type', $match->type)==='friendly' ? 'selected' : '' }}>Friendly</option>
                        <option value="league"   {{ old('type', $match->type)==='league'   ? 'selected' : '' }}>League Match</option>
                    </select>
                    @error('type')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Fee --}}
                <div class="form-group">
                    <label class="form-label" for="match_fee">Match Fee (KSh) <span class="required">*</span></label>
                    <input type="number" name="match_fee" id="match_fee" value="{{ old('match_fee', $match->match_fee) }}"
                        min="0" class="form-control @error('match_fee') error @enderror" required>
                    @error('match_fee')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-grid">
                {{-- Date --}}
                <div class="form-group">
                    <label class="form-label" for="match_date">Match Date <span class="required">*</span></label>
                    <input type="date" name="match_date" id="match_date"
                        value="{{ old('match_date', $match->match_date->format('Y-m-d')) }}"
                        class="form-control @error('match_date') error @enderror" required>
                    @error('match_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Time --}}
                <div class="form-group">
                    <label class="form-label" for="match_time">Kickoff Time <span class="required">*</span></label>
                    <input type="time" name="match_time" id="match_time"
                        value="{{ old('match_time', $match->match_time) }}"
                        class="form-control @error('match_time') error @enderror" required>
                    @error('match_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Stadium Selector --}}
            @if($stadiums->count())
            <div class="form-group">
                <label class="form-label" for="stadium_id">Select Stadium (optional)</label>
                <select id="stadium_id" class="form-control" onchange="prefillVenue(this)">
                    <option value="">— Type venue manually or pick a saved stadium —</option>
                    @foreach($stadiums as $s)
                    <option value="{{ $s->name }}, {{ $s->location ?? 'Nairobi' }}"
                        {{ old('venue', $match->venue) === $s->name . ', ' . ($s->location ?? 'Nairobi') ? 'selected' : '' }}>
                        🏟️ {{ $s->name }} @if($s->location)— {{ $s->location }}@endif
                    </option>
                    @endforeach
                </select>
                <span class="form-hint">Selecting a stadium auto-fills the venue field below.</span>
            </div>
            @endif

            <div class="form-grid">
                {{-- Venue --}}
                <div class="form-group">
                    <label class="form-label" for="venue">Venue <span class="required">*</span></label>
                    <input type="text" name="venue" id="venue" value="{{ old('venue', $match->venue) }}"
                        class="form-control @error('venue') error @enderror" required>
                    @error('venue')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Deadline --}}
                <div class="form-group">
                    <label class="form-label" for="deadline">Availability Lock Deadline <span class="required">*</span></label>
                    <input type="datetime-local" name="deadline" id="deadline"
                        value="{{ old('deadline', $match->deadline->format('Y-m-d\TH:i')) }}"
                        class="form-control @error('deadline') error @enderror" required>
                    @error('deadline')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label class="form-label" for="status">Match Status <span class="required">*</span></label>
                <select name="status" id="status" class="form-control @error('status') error @enderror" required>
                    <option value="upcoming"  {{ old('status', $match->status)==='upcoming'  ? 'selected' : '' }}>Upcoming</option>
                    <option value="open"      {{ old('status', $match->status)==='open'      ? 'selected' : '' }}>Open</option>
                    <option value="locked"    {{ old('status', $match->status)==='locked'    ? 'selected' : '' }}>Locked</option>
                    <option value="completed" {{ old('status', $match->status)==='completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Notes --}}
            <div class="form-group">
                <label class="form-label" for="notes">Additional Notes</label>
                <textarea name="notes" id="notes" rows="3"
                    class="form-control">{{ old('notes', $match->notes) }}</textarea>
            </div>

            <div class="d-flex justify-between" style="margin-top:24px;">
                <a href="{{ route('matches.show', $match) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">💾 Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function prefillVenue(select) {
    const val = select.value;
    if (val) {
        document.getElementById('venue').value = val;
    }
}
</script>
@endsection
