@extends('layouts.app')
@section('title', 'Edit Stadium')
@section('page-title', 'Edit Stadium')
@section('breadcrumb')
<a href="{{ route('admin.stadiums.index') }}">Stadiums</a> /
<a href="{{ route('admin.stadiums.show', $stadium) }}">{{ $stadium->name }}</a> / Edit
@endsection

@section('content')
<div class="card" style="max-width:620px; margin:0 auto;">
    <div class="card-header">
        <span class="card-title">✏️ Edit: {{ $stadium->name }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.stadiums.update', $stadium) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Stadium Name <span class="required">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $stadium->name) }}"
                    class="form-control @error('name') error @enderror" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="location">Location / Address</label>
                <input type="text" name="location" id="location" value="{{ old('location', $stadium->location) }}"
                    class="form-control @error('location') error @enderror">
                @error('location')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="capacity">Capacity (seats)</label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $stadium->capacity) }}"
                        min="0" class="form-control @error('capacity') error @enderror">
                    @error('capacity')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="surface">Playing Surface <span class="required">*</span></label>
                    <select name="surface" id="surface" class="form-control @error('surface') error @enderror" required>
                        <option value="artificial" {{ old('surface', $stadium->surface)==='artificial' ? 'selected' : '' }}>🟩 Artificial Turf</option>
                        <option value="grass"      {{ old('surface', $stadium->surface)==='grass'      ? 'selected' : '' }}>🌿 Natural Grass</option>
                        <option value="indoor"     {{ old('surface', $stadium->surface)==='indoor'     ? 'selected' : '' }}>🏟️ Indoor</option>
                    </select>
                    @error('surface')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="notes">Notes / Additional Info</label>
                <textarea name="notes" id="notes" rows="3"
                    class="form-control @error('notes') error @enderror">{{ old('notes', $stadium->notes) }}</textarea>
                @error('notes')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', $stadium->is_active) ? 'checked' : '' }} style="width:18px;height:18px;">
                    <span>Stadium is Active</span>
                </label>
            </div>

            <div class="d-flex justify-between" style="margin-top:24px;">
                <a href="{{ route('admin.stadiums.show', $stadium) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">💾 Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
