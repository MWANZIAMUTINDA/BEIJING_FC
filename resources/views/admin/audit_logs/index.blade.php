@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page-title', 'Security Audit Trail')

@section('content')
<div class="card mb-6">
    <div class="card-header">
        <span class="card-title">🔍 Search Trail</span>
        <form method="GET" action="{{ route('admin.audit-logs') }}" style="display:flex; gap:8px;">
            <input type="text" name="action" placeholder="Filter by action..." class="form-control form-control-sm" style="width:200px;" value="{{ request('action') }}">
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
    </div>
    
    <div class="table-wrap">
        @if($logs->count())
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Actor</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td class="text-xs text-muted" style="white-space: nowrap;">
                        {{ $log->performed_at->format('d M Y H:i:s') }}
                    </td>
                    <td>
                        @if($log->actor)
                            <strong>{{ $log->actor->name }}</strong><br>
                            <span class="badge badge-gray" style="font-size:9px;">{{ $log->actor->role_label }}</span>
                        @else
                            <span class="text-muted">System</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ str_contains($log->action, 'deleted') || str_contains($log->action, 'remove') ? 'red' : (str_contains($log->action, 'override') ? 'orange' : 'blue') }}" style="font-size: 11px;">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="text-xs text-secondary">
                        @if($log->entity_type)
                            <div style="font-weight:600; margin-bottom:2px;">{{ $log->entity_type }} (ID: {{ $log->entity_id }})</div>
                        @endif
                        @if($log->new_values)
                            <pre style="margin:0; font-family:monospace; background:rgba(0,0,0,0.1); padding:4px; border-radius:4px; max-width: 300px; overflow-x: auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                        @endif
                    </td>
                    <td class="text-xs text-muted">
                        <code>{{ $log->ip_address ?? '—' }}</code>
                    </td>
                    <td class="text-xs text-muted" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->user_agent }}">
                        {{ $log->user_agent }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">🛡️</div>
            <div class="empty-state-title">No audit logs found</div>
        </div>
        @endif
    </div>

    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
