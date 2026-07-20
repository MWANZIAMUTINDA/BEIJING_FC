@extends('layouts.app')
@section('title', 'Add System User')
@section('page-title', 'Add System User')
@section('breadcrumb')
<a href="{{ route('admin.users.index') }}">Users</a> / Add User
@endsection

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">📝 Add New User Profile</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                {{-- Username --}}
                <div class="form-group">
                    <label class="form-label" for="username">Username <span class="required">*</span></label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" class="form-control @error('username') error @enderror" placeholder="e.g. jdoe" required>
                    @error('username')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Full Name --}}
                <div class="form-group">
                    <label class="form-label" for="name">Full Name <span class="required">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') error @enderror" placeholder="e.g. John Doe" required>
                    @error('name')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') error @enderror" placeholder="e.g. member@beijingfc.co.ke">
                    @error('email')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number <span class="required">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control @error('phone') error @enderror" placeholder="e.g. 0712345678" required>
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
                        <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>Member / Player</option>
                        <option value="coach" {{ old('role') === 'coach' ? 'selected' : '' }}>Coach</option>
                        <option value="treasurer" {{ old('role') === 'treasurer' ? 'selected' : '' }}>Treasurer</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                    @error('role')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Playing Position --}}
                <div class="form-group member-only">
                    <label class="form-label" for="position">Playing Position <span class="required">*</span></label>
                    <select name="position" id="position" class="form-control @error('position') error @enderror" data-required-if-member>
                        <option value="GK" {{ old('position') === 'GK' ? 'selected' : '' }}>Goalkeeper (GK)</option>
                        <option value="DF" {{ old('position') === 'DF' ? 'selected' : '' }}>Defender (DF)</option>
                        <option value="MF" {{ old('position') === 'MF' ? 'selected' : '' }}>Midfielder (MF)</option>
                        <option value="FW" {{ old('position') === 'FW' ? 'selected' : '' }}>Forward (FW)</option>
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
                        <option value="monthly" {{ old('billing_type') === 'monthly' ? 'selected' : '' }}>Monthly (KSh 2,080 / Month)</option>
                        <option value="match" {{ old('billing_type') === 'match' ? 'selected' : '' }}>Pay Per Match (KSh 350 / Match)</option>
                    </select>
                    @error('billing_type')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nationality --}}
                <div class="form-group">
                    <label class="form-label" for="nationality">Nationality <span class="required">*</span></label>
                    <select name="nationality" id="nationality" class="form-control @error('nationality') error @enderror" data-required-if-member>
                        <option value="Kenyan" {{ old('nationality', 'Kenyan') === 'Kenyan' ? 'selected' : '' }}>Kenyan</option>
                        <option value="Ugandan" {{ old('nationality') === 'Ugandan' ? 'selected' : '' }}>Ugandan</option>
                        <option value="Tanzanian" {{ old('nationality') === 'Tanzanian' ? 'selected' : '' }}>Tanzanian</option>
                        <option value="Rwandan" {{ old('nationality') === 'Rwandan' ? 'selected' : '' }}>Rwandan</option>
                        <option value="Burundian" {{ old('nationality') === 'Burundian' ? 'selected' : '' }}>Burundian</option>
                        <option value="Congolese" {{ old('nationality') === 'Congolese' ? 'selected' : '' }}>Congolese</option>
                        <option value="Other" {{ old('nationality') === 'Other' ? 'selected' : '' }}>Other</option>
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
                    <input type="number" name="jersey_number" id="jersey_number" value="{{ old('jersey_number') }}" class="form-control @error('jersey_number') error @enderror" placeholder="e.g. 10" min="1" max="99">
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
                        <option value="{{ $t->id }}" {{ old('league_team_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->short_name }})</option>
                        @endforeach
                    </select>
                    @error('league_team_id')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                {{-- Date Joined --}}
                <div class="form-group">
                    <label class="form-label" for="date_joined">Date Joined</label>
                    <input type="date" name="date_joined" id="date_joined" value="{{ old('date_joined', now()->format('Y-m-d')) }}" class="form-control @error('date_joined') error @enderror">
                    @error('date_joined')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Profile Picture (Avatar) --}}
                <div class="form-group">
                    <label class="form-label" for="avatar">Profile Picture (Avatar)</label>
                    <input type="file" name="avatar" id="avatar" class="form-control @error('avatar') error @enderror" accept="image/*">
                    @error('avatar')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                {{-- Emergency Contact Name --}}
                <div class="form-group">
                    <label class="form-label" for="emergency_contact">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}" class="form-control @error('emergency_contact') error @enderror" placeholder="e.g. Mary Jane">
                    @error('emergency_contact')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Emergency Contact Phone --}}
                <div class="form-group">
                    <label class="form-label" for="emergency_phone">Emergency Contact Phone</label>
                    <input type="text" name="emergency_phone" id="emergency_phone" value="{{ old('emergency_phone') }}" class="form-control @error('emergency_phone') error @enderror" placeholder="e.g. 0722000000">
                    @error('emergency_phone')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required">*</span></label>
                    <input type="password" name="password" id="password" class="form-control @error('password') error @enderror" required>
                    @error('password')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-between" style="margin-top: 24px;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
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

