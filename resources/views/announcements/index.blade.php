@extends('layouts.app')
@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
<div class="card mb-6">
    <div class="card-header">
        <span class="card-title">📢 Club Announcements Broadcasts</span>
        <div style="display:flex; gap:10px; align-items:center;">
            @if($unread > 0)
            <span class="badge badge-yellow" style="font-size:10px;">{{ $unread }} Unread</span>
            @endif
            @if(auth()->user()->hasRole(['admin', 'coach']))
            <a href="{{ route('announcements.create') }}" class="btn btn-primary btn-sm">+ Post Announcement</a>
            @endif
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($announcements->count())
        <div style="display:flex; flex-direction:column;">
            @foreach($announcements as $ann)
            @php
                $isRead = auth()->check() && $ann->reads()->where('user_id', auth()->id())->exists();
            @endphp
            <div id="ann-{{ $ann->id }}" class="announce-card" style="padding: 24px; border-bottom: 1px solid var(--glass-border); transition: var(--transition); background: {{ $isRead ? 'transparent' : 'rgba(16,185,129,0.02)' }};" onclick="markAnnouncementRead({{ $ann->id }}, '{{ route('announcements.read', $ann) }}')">
                <div class="d-flex justify-between align-center mb-2">
                    <div style="display:flex; gap:8px; align-items:center;">
                        <span class="badge {{ $ann->getTypeBadgeClass() }}">{{ $ann->getTypeLabel() }}</span>
                        @if(!$isRead)
                        <span class="badge badge-yellow" id="unread-badge-{{ $ann->id }}" style="font-size:9px; padding:1px 6px;">New</span>
                        @endif
                    </div>
                    <span class="text-xs text-muted">{{ $ann->created_at->diffForHumans() }}</span>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">{{ $ann->title }}</h3>
                <p class="text-sm text-secondary" style="line-height:1.6; white-space: pre-wrap;">{{ $ann->body }}</p>
                
                <div class="d-flex justify-between align-center mt-4" style="margin-top:12px; font-size:11px; color: var(--text-muted);">
                    <span>Published by: <strong>{{ $ann->creator?->name }}</strong></span>
                    @if(auth()->user()->hasRole(['admin', 'coach']))
                    <form method="POST" action="{{ route('announcements.destroy', $ann) }}" onsubmit="return confirm('Delete this announcement?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="font-size:10px; padding:2px 8px;">
                            Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state" style="padding:60px 20px;">
            <div class="empty-state-icon">📢</div>
            <div class="empty-state-title">No announcements found</div>
        </div>
        @endif
    </div>
    @if($announcements->hasPages())
    <div class="card-footer">
        {{ $announcements->links() }}
    </div>
    @endif
</div>

<script>
function markAnnouncementRead(id, url) {
    const unreadBadge = document.getElementById(`unread-badge-${id}`);
    if (!unreadBadge) return; // Already read

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            unreadBadge.remove();
            document.getElementById(`ann-${id}`).style.background = 'transparent';
        }
    })
    .catch(error => console.error('Error marking announcement as read:', error));
}
</script>
@endsection
