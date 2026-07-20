@extends('layouts.app')
@section('title', 'Edit Member')
@section('page-title', 'Edit Member')
@section('breadcrumb')
<a href="{{ route('admin.users.index') }}">Members</a> / Edit Profile
@endsection

@section('content')
<div class="dashboard-grid">
    {{-- Left side: Profile edit form --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">📝 Edit Member Profile ({{ $user->name }})</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="form-grid">
                        {{-- Username (Disabled) --}}
                        <div class="form-group">
                            <label class="form-label" for="username">Username (Cannot change)</label>
                            <input type="text" id="username" value="{{ $user->username }}" class="form-control" style="opacity:0.6; cursor:not-allowed;" readonly disabled>
                        </div>

                        {{-- Full Name --}}
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name <span class="required">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') error @enderror" required>
                            @error('name')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-grid">
                        {{-- Email --}}
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') error @enderror">
                            @error('email')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number <span class="required">*</span></label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') error @enderror" required>
                            @error('phone')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

            <div class="form-grid">
                {{-- System Role --}}
                <div class="form-group">
                    <label class="form-label" for="role">System Role <span class="required">*</span></label>
                    <select name="role" id="role" class="form-control @error('role') error @enderror" required>
                        <option value="member" {{ old('role', $user->role) === 'member' ? 'selected' : '' }}>Member / Player</option>
                        <option value="coach" {{ old('role', $user->role) === 'coach' ? 'selected' : '' }}>Coach</option>
                        <option value="treasurer" {{ old('role', $user->role) === 'treasurer' ? 'selected' : '' }}>Treasurer</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                    @error('role')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Playing Position --}}
                <div class="form-group member-only">
                    <label class="form-label" for="position">Playing Position <span class="required">*</span></label>
                    <select name="position" id="position" class="form-control @error('position') error @enderror" data-required-if-member>
                        <option value="GK" {{ old('position', $user->position) === 'GK' ? 'selected' : '' }}>Goalkeeper (GK)</option>
                        <option value="DF" {{ old('position', $user->position) === 'DF' ? 'selected' : '' }}>Defender (DF)</option>
                        <option value="MF" {{ old('position', $user->position) === 'MF' ? 'selected' : '' }}>Midfielder (MF)</option>
                        <option value="FW" {{ old('position', $user->position) === 'FW' ? 'selected' : '' }}>Forward (FW)</option>
                    </select>
                    @error('position')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid member-only">
                {{-- Billing Model --}}
                <div class="form-group">
                    <label class="form-label" for="billing_type">Billing Type / Payment Model <span class="required">*</span></label>
                    <select name="billing_type" id="billing_type" class="form-control @error('billing_type') error @enderror" data-required-if-member>
                        <option value="monthly" {{ old('billing_type', $user->billing_type) === 'monthly' ? 'selected' : '' }}>Monthly (KSh 2,080 / Month)</option>
                        <option value="match" {{ old('billing_type', $user->billing_type) === 'match' ? 'selected' : '' }}>Pay Per Match (KSh 350 / Match)</option>
                    </select>
                    @error('billing_type')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nationality --}}
                <div class="form-group">
                    <label class="form-label" for="nationality">Nationality <span class="required">*</span></label>
                    <select name="nationality" id="nationality" class="form-control @error('nationality') error @enderror" data-required-if-member>
                        <option value="Kenyan" {{ old('nationality', $user->nationality ?? 'Kenyan') === 'Kenyan' ? 'selected' : '' }}>Kenyan</option>
                        <option value="Ugandan" {{ old('nationality', $user->nationality) === 'Ugandan' ? 'selected' : '' }}>Ugandan</option>
                        <option value="Tanzanian" {{ old('nationality', $user->nationality) === 'Tanzanian' ? 'selected' : '' }}>Tanzanian</option>
                        <option value="Rwandan" {{ old('nationality', $user->nationality) === 'Rwandan' ? 'selected' : '' }}>Rwandan</option>
                        <option value="Burundian" {{ old('nationality', $user->nationality) === 'Burundian' ? 'selected' : '' }}>Burundian</option>
                        <option value="Congolese" {{ old('nationality', $user->nationality) === 'Congolese' ? 'selected' : '' }}>Congolese</option>
                        <option value="Other" {{ old('nationality', $user->nationality) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('nationality')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid member-only">
                {{-- Playing Number (Jersey) --}}
                <div class="form-group">
                    <label class="form-label" for="jersey_number">Jersey Number (1-99)</label>
                    <input type="number" name="jersey_number" id="jersey_number" value="{{ old('jersey_number', $user->jersey_number) }}" class="form-control @error('jersey_number') error @enderror" min="1" max="99">
                    @error('jersey_number')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Assigned Team --}}
                <div class="form-group">
                    <label class="form-label" for="league_team_id">Assigned League Team</label>
                    <select name="league_team_id" id="league_team_id" class="form-control @error('league_team_id') error @enderror">
                        <option value="">-- No Assigned Team (Independent) --</option>
                        @foreach($teams as $t)
                        <option value="{{ $t->id }}" {{ old('league_team_id', $user->league_team_id) == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->short_name }})</option>
                        @endforeach
                    </select>
                    @error('league_team_id')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="date_joined">Date Joined</label>
                            <input type="date" name="date_joined" id="date_joined" value="{{ old('date_joined', $user->date_joined ? $user->date_joined->format('Y-m-d') : '') }}" class="form-control @error('date_joined') error @enderror">
                            @error('date_joined')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Profile Picture (Avatar) --}}
                        <div style="display:flex; align-items:center; gap:20px; margin-top: 15px;">
                            <img src="{{ $user->avatar_url }}" style="width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid var(--glass-border);">
                            <div style="flex:1;">
                                <label class="form-label" for="avatar">Change Profile Picture</label>
                                <input type="file" name="avatar" id="avatar" class="form-control @error('avatar') error @enderror" accept="image/*">
                                @error('avatar')
                                <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        {{-- Emergency Contact Name --}}
                        <div class="form-group">
                            <label class="form-label" for="emergency_contact">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact', $user->emergency_contact) }}" class="form-control @error('emergency_contact') error @enderror" placeholder="e.g. Mary Jane">
                            @error('emergency_contact')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Emergency Contact Phone --}}
                        <div class="form-group">
                            <label class="form-label" for="emergency_phone">Emergency Contact Phone</label>
                            <input type="text" name="emergency_phone" id="emergency_phone" value="{{ old('emergency_phone', $user->emergency_phone) }}" class="form-control @error('emergency_phone') error @enderror" placeholder="e.g. 0722000000">
                            @error('emergency_phone')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="auth-checkbox-label" style="display:flex; align-items:center; gap:8px;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <span>Account Active & Enabled</span>
                        </label>
                    </div>

                    {{-- Optional Password Change --}}
                    <div style="background: rgba(255,255,255,0.02); border: 1px dashed var(--glass-border); border-radius: var(--radius-sm); padding: 18px; margin: 24px 0 16px;">
                        <span class="font-bold text-sm" style="display:block; margin-bottom:12px; color: var(--gold-400);">🔑 Reset Security Password (Optional)</span>
                        
                        <div class="form-grid">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="password">New Password</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') error @enderror" placeholder="Leave blank to keep current">
                                @error('password')
                                <div class="form-error" style="margin-top:4px;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex justify-between" style="margin-top: 24px;">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        @if($user->id !== auth()->id())
        {{-- Danger Zone Card --}}
        <div class="card" style="margin-top:24px; border:1px solid var(--red-500);">
            <div class="card-header" style="background:rgba(239,68,68,0.08); border-bottom:1px solid rgba(239,68,68,0.2);">
                <span class="card-title" style="color:var(--red-400);">⚠️ Danger Zone</span>
            </div>
            <div class="card-body">
                <p class="text-sm text-secondary" style="margin-bottom:16px;">
                    Deleting this member will permanently remove their profile details, balance records, and matches attendance history. 
                    This action is irreversible.
                </p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you absolutely sure you want to permanently delete this member? All their data will be wiped.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-red">Delete Member Profile</button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Right side: Account Balance Summary & Recent Payments --}}
    <div>
        {{-- Account Balance Summary --}}
        <div class="card mb-6">
            <div class="card-header">
                <span class="card-title">💰 Balance Information</span>
            </div>
            <div class="card-body">
                @if($user->role === 'member' && $user->balance)
                <div style="text-align:center; padding:12px 0;">
                    <div style="font-size:32px; font-weight:800; font-family:'Outfit',sans-serif; color:{{ $user->balance->isInCredit() ? 'var(--emerald-400)' : 'var(--red-400)' }};">
                        KSh {{ number_format(abs($user->balance->balance)) }}
                    </div>
                    <div class="text-xs text-muted" style="margin-top:4px;">
                        {{ $user->balance->isInCredit() ? 'Advance Credit balance' : 'Outstanding debt balance' }}
                    </div>
                    <div class="badge {{ $user->balance->getStatusClass() }}" style="margin-top:10px;">
                        {{ $user->balance->getStatusLabel() }}
                    </div>
                </div>
                <hr class="divider">
                <div class="d-flex justify-between" style="font-size:13px;">
                    <span class="text-secondary">Total Contributions</span>
                    <strong class="text-emerald">KSh {{ number_format($user->balance->total_paid) }}</strong>
                </div>
                @if($user->balance->last_payment_at)
                <div class="d-flex justify-between mt-2" style="font-size:13px;">
                    <span class="text-secondary">Last Active Payment</span>
                    <span>{{ $user->balance->last_payment_at->format('d M Y') }}</span>
                </div>
                @endif
                @else
                <p class="text-xs text-muted text-center">No balance details calculated yet.</p>
                @endif
            </div>
        </div>

        {{-- Member's Recent Payments Log --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">💳 Member's Payments</span>
            </div>
            <div style="max-height: 380px; overflow-y: auto;">
                @forelse($user->payments as $p)
                <div style="padding:12px 18px; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div class="text-sm font-bold" style="color:var(--text-primary);">{{ $p->getTypeLabel() }}</div>
                        <div class="text-xs text-muted">{{ $p->created_at->format('d M Y') }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="text-emerald font-bold" style="font-size:13px;">KSh {{ number_format($p->amount) }}</div>
                        <span class="badge {{ $p->getStatusBadge()['class'] }}" style="font-size:9px;">{{ $p->getStatusBadge()['label'] }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:30px;">
                    <p class="text-xs text-muted">No payments recorded for this member.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const memberFields = document.querySelectorAll('.member-only');

        function toggleMemberFields() {
            const isMember = roleSelect.value === 'member';
            memberFields.forEach(el => {
                el.style.display = isMember ? '' : 'none';
                const inputs = el.querySelectorAll('select, input');
                inputs.forEach(input => {
                    if (isMember) {
                        if (input.hasAttribute('data-required-if-member')) {
                            input.setAttribute('required', 'required');
                        }
                    } else {
                        input.removeAttribute('required');
                    }
                });
            });
        }

        roleSelect.addEventListener('change', toggleMemberFields);
        toggleMemberFields();
    });
</script>
@endsection

