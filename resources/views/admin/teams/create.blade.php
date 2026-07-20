@extends('layouts.app')
@section('title', 'Add League Team')
@section('page-title', 'Add League Team')
@section('breadcrumb')
<a href="{{ route('admin.teams.index') }}">Teams</a> / Add Team
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">🛡️ Register New League Team</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.teams.store') }}">
            @csrf

            {{-- Team Name --}}
            <div class="form-group">
                <label class="form-label" for="name">Team Name <span class="required">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') error @enderror" placeholder="e.g. Kibera Black Stars" required>
                @error('name')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Short Name --}}
            <div class="form-group">
                <label class="form-label" for="short_name">Short Code (Max 5 chars) <span class="required">*</span></label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name') }}" class="form-control @error('short_name') error @enderror" placeholder="e.g. KBS" maxlength="5" required>
                <span class="form-hint">Must be uppercase abbreviations (e.g. BFC, KBS).</span>
                @error('short_name')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid">
                {{-- Badge/Brand Color --}}
                <div class="form-group">
                    <label class="form-label" for="color">Badge / Accent Color <span class="required">*</span></label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="color" name="color" id="color" value="{{ old('color', '#0B1F4D') }}" class="form-control" style="width: 60px; height: 38px; padding: 2px;" required>
                        <span class="text-xs text-secondary">Hex color for schedules & charts</span>
                    </div>
                    @error('color')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kit Color --}}
                <div class="form-group">
                    <label class="form-label" for="kit_color">Primary Kit Color</label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="color" name="kit_color" id="kit_color" value="{{ old('kit_color', '#FFFFFF') }}" class="form-control" style="width: 60px; height: 38px; padding: 2px;">
                        <span class="text-xs text-secondary">Kit jersey base color</span>
                    </div>
                    @error('kit_color')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Active --}}
            <div class="form-group" style="margin-top: 10px;">
                <label class="form-label" style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width:16px; height:16px;">
                    <span>Active & Available for League Fixtures</span>
                </label>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-between" style="margin-top: 28px;">
                <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Team</button>
            </div>
        </form>
    </div>
</div>
@endsection
