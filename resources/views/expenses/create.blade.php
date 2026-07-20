@extends('layouts.app')
@section('title', 'Record Expense')
@section('page-title', 'Record Expense')
@section('breadcrumb')
<a href="{{ route('expenses.index') }}">Expenses</a> / Record
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">📝 Record Club Expense</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Category --}}
            <div class="form-group">
                <label class="form-label" for="category">Category <span class="required">*</span></label>
                <select name="category" id="category" class="form-control @error('category') error @enderror" required>
                    <option value="turf" {{ old('category') === 'turf' ? 'selected' : '' }}>Turf / Ground Hire</option>
                    <option value="equipment" {{ old('category') === 'equipment' ? 'selected' : '' }}>Balls & Training Equipment</option>
                    <option value="transport" {{ old('category') === 'transport' ? 'selected' : '' }}>Transport & Logistics</option>
                    <option value="refreshments" {{ old('category') === 'refreshments' ? 'selected' : '' }}>Water & Refreshments</option>
                    <option value="medical" {{ old('category') === 'medical' ? 'selected' : '' }}>Medical / First Aid</option>
                    <option value="miscellaneous" {{ old('category') === 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                </select>
                @error('category')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label" for="description">Description <span class="required">*</span></label>
                <input type="text" name="description" id="description" value="{{ old('description') }}" class="form-control @error('description') error @enderror" placeholder="e.g. Turf hire for Kibera match, 3 cases of water" required>
                @error('description')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid">
                {{-- Amount --}}
                <div class="form-group">
                    <label class="form-label" for="amount">Amount (KSh) <span class="required">*</span></label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="1" class="form-control @error('amount') error @enderror" placeholder="e.g. 3500" required>
                    @error('amount')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Date --}}
                <div class="form-group">
                    <label class="form-label" for="expense_date">Date incurred <span class="required">*</span></label>
                    <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" class="form-control @error('expense_date') error @enderror" required>
                    @error('expense_date')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
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

            {{-- Receipt upload --}}
            <div class="form-group">
                <label class="form-label" for="receipt">Attach Receipt Image / PDF</label>
                <input type="file" name="receipt" id="receipt" class="form-control @error('receipt') error @enderror" accept="image/*,application/pdf">
                <span class="form-hint">Accepted formats: JPG, PNG, PDF. Max size: 2MB.</span>
                @error('receipt')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Notes --}}
            <div class="form-group">
                <label class="form-label" for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Payment details, shop name or references...">{{ old('notes') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-between" style="margin-top: 24px;">
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Record Expense</button>
            </div>
        </form>
    </div>
</div>
@endsection
