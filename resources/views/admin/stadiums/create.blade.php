@extends('layouts.app')
@section('title', 'Add Stadium')
@section('page-title', 'Add Stadium')
@section('breadcrumb')
<a href="{{ route('admin.stadiums.index') }}">Stadiums</a> / Add
@endsection

@section('content')
<div class="card" style="max-width:620px; margin:0 auto;">
    <div class="card-header">
        <span class="card-title">🏟️ Register New Stadium</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.stadiums.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Stadium Name <span class="required">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="form-control @error('name') error @enderror"
                    placeholder="e.g. Kasarani Stadium, Camp Toyoyo" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="location">Location / Address</label>
                <input type="text" name="location" id="location" value="{{ old('location') }}"
                    class="form-control @error('location') error @enderror"
                    placeholder="e.g. Kasarani, Nairobi">
                @error('location')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="capacity">Capacity (seats)</label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}"
                        min="0" class="form-control @error('capacity') error @enderror"
                        placeholder="e.g. 60000">
                    @error('capacity')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="surface">Playing Surface <span class="required">*</span></label>
                    <select name="surface" id="surface" class="form-control @error('surface') error @enderror" required>
                        <option value="artificial" {{ old('surface','artificial')==='artificial' ? 'selected' : '' }}>🟩 Artificial Turf</option>
                        <option value="grass"      {{ old('surface')==='grass'      ? 'selected' : '' }}>🌿 Natural Grass</option>
                        <option value="indoor"     {{ old('surface')==='indoor'     ? 'selected' : '' }}>🏟️ Indoor</option>
                    </select>
                    @error('surface')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="notes">Notes / Additional Info</label>
                <textarea name="notes" id="notes" rows="3"
                    class="form-control @error('notes') error @enderror"
                    placeholder="Parking info, changing rooms, access notes…">{{ old('notes') }}</textarea>
                @error('notes')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', '1') ? 'checked' : '' }} style="width:18px;height:18px;">
                    <span>Stadium is Active (available for fixture scheduling)</span>
                </label>
            </div>

            <div class="d-flex justify-between" style="margin-top:24px;">
                <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">🏟️ Save Stadium</button>
            </div>
        </form>
    </div>
</div>
@endsection
