@extends('layouts.app')
@section('title', 'Reports Center')
@section('page-title', 'Club Reports & Exports')

@section('content')
<div style="margin-bottom: 24px;">
    <p class="text-sm text-muted">Generate detailed summaries and export spreadsheets in structured formats.</p>
</div>

<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
    
    {{-- Financial Report --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">💵 Financial Summary</span>
        </div>
        <div class="card-body">
            <p class="text-xs text-muted mb-4" style="min-height: 36px;">Consolidated view of all revenue income streams, paid membership subscriptions, match events fees, and approved treasury expenses.</p>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('reports.export', ['type' => 'financial']) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">📥 Export CSV</a>
            </div>
        </div>
    </div>

    {{-- Payments Report --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📥 Member Payments Log</span>
        </div>
        <div class="card-body">
            <p class="text-xs text-muted mb-4" style="min-height: 36px;">Full database log containing every confirmed and pending payment transaction, inclusive of Daraja M-Pesa tracking codes.</p>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('reports.export', ['type' => 'payment']) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">📥 Export CSV</a>
            </div>
        </div>
    </div>

    {{-- Expense Report --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📤 Approved Expenditures</span>
        </div>
        <div class="card-body">
            <p class="text-xs text-muted mb-4" style="min-height: 36px;">Detailed database lookup of all recorded logistics disbursements, equipment sourcing, ground hires, and referee fees.</p>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('reports.export', ['type' => 'expense']) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">📥 Export CSV</a>
            </div>
        </div>
    </div>

    {{-- Attendance Report --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📅 Player Match Attendance</span>
        </div>
        <div class="card-body">
            <p class="text-xs text-muted mb-4" style="min-height: 36px;">Consolidated response metrics tracking player availabilities, maybe confirmations, locked deadlines, and match days attendance rate.</p>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('reports.export', ['type' => 'attendance']) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">📥 Export CSV</a>
            </div>
        </div>
    </div>

    {{-- Member Report --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">👥 Club Member Roster</span>
        </div>
        <div class="card-body">
            <p class="text-xs text-muted mb-4" style="min-height: 36px;">Current list of members, email details, verified phone codes, player positions, system access roles, and status flags.</p>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('reports.export', ['type' => 'member']) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">📥 Export CSV</a>
            </div>
        </div>
    </div>

    {{-- League Report --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🏆 League Standings & Stats</span>
        </div>
        <div class="card-body">
            <p class="text-xs text-muted mb-4" style="min-height: 36px;">Aggregated metrics containing team points, match results count, goal statistics, goal difference, and wins standings.</p>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('reports.export', ['type' => 'league']) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">📥 Export CSV</a>
            </div>
        </div>
    </div>

</div>
@endsection
