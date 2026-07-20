@extends('layouts.app')
@section('title', 'Club Settings')
@section('page-title', 'Club Configuration Control Panel')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">⚙️ Club Details & Rules</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="club_name">Club Name</label>
                <input type="text" name="club_name" id="club_name" class="form-control" value="{{ $settings['club_name'] }}" required>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="monthly_fee">Monthly Subscription Fee (KSh)</label>
                    <input type="number" name="monthly_fee" id="monthly_fee" class="form-control" value="{{ $settings['monthly_fee'] }}" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="match_fee">Per Match Fee (KSh)</label>
                    <input type="number" name="match_fee" id="match_fee" class="form-control" value="{{ $settings['match_fee'] }}" min="0" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="paybill_number">M-Pesa Paybill / Till Number</label>
                    <input type="text" name="paybill_number" id="paybill_number" class="form-control" value="{{ $settings['paybill_number'] }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="sms_sender">SMS Sender ID</label>
                    <input type="text" name="sms_sender" id="sms_sender" class="form-control" value="{{ $settings['sms_sender'] }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="current_season">Active Season</label>
                <input type="text" name="current_season" id="current_season" class="form-control" value="{{ $settings['current_season'] }}" required placeholder="e.g. 2025/2026">
            </div>

            <div class="form-group">
                <label class="form-label" for="league_rules">League Rules Description</label>
                <textarea name="league_rules" id="league_rules" class="form-control" rows="4" required>{{ $settings['league_rules'] }}</textarea>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-primary">Save Configuration</button>
            </div>
        </form>
    </div>
</div>
@endsection
