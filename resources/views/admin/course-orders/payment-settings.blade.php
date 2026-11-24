@extends('admin.layouts.app')
@section('title')<title>Payment Methods | {{ general()->title }} Admin</title>@endsection

@section('contents')
<form method="post" action="{{ route('admin.payment-settings.save') }}">
    @csrf
    <div class="adm-page-head">
        <div>
            <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li>Payment Methods</li></ul>
            <h1>Manual payment methods</h1>
            <p>These account numbers and instructions are shown to students on the checkout page.</p>
        </div>
        <div class="adm-page-head__actions">
            <a href="{{ route('checkout') }}" target="_blank" class="adm-btn"><i class="fa-regular fa-eye"></i> View checkout</a>
            <button class="adm-btn adm-btn--primary"><i class="fa-solid fa-floppy-disk"></i> Save settings</button>
        </div>
    </div>

    @include('admin.alerts')

    <div class="row">
        @foreach($methods as $i => $m)
            @php($logo = ['bkash' => ['#E2136E', 'b'], 'nagad' => ['#F15A22', 'N'], 'bank' => ['#1E40AF', null]][$m['key']] ?? ['#003080', '?'])
            <div class="col-xl-4 col-lg-6">
                <div class="adm-card">
                    <div class="adm-card__head">
                        <div class="adm-cell">
                            <span class="adm-avatar" style="background: {{ $logo[0] }}; font-size:1.05rem">@if($logo[1]){{ $logo[1] }}@else<i class="fa-solid fa-building-columns"></i>@endif</span>
                            <div><strong>{{ $m['name'] }}</strong><small>{{ $m['key'] === 'bank' ? 'Bank deposit / transfer' : 'Mobile banking' }}</small></div>
                        </div>
                        <label class="adm-switch"><input type="checkbox" name="methods[{{ $i }}][enabled]" value="1" @checked(old("methods.$i.enabled", $m['enabled']))><span class="adm-switch__ui"></span> Enabled</label>
                    </div>
                    <div class="adm-card__body">
                        <input type="hidden" name="methods[{{ $i }}][key]" value="{{ $m['key'] }}">
                        <div class="adm-field">
                            <label class="adm-label">Display name</label>
                            <input type="text" class="adm-input" name="methods[{{ $i }}][name]" value="{{ old("methods.$i.name", $m['name']) }}" required maxlength="50">
                        </div>
                        <div class="adm-field">
                            <label class="adm-label">{{ $m['key'] === 'bank' ? 'Account number' : 'Account number' }} <span class="req">*</span></label>
                            <input type="text" class="adm-input adm-mono" name="methods[{{ $i }}][account]" value="{{ old("methods.$i.account", $m['account']) }}" required maxlength="80" placeholder="{{ $m['key'] === 'bank' ? 'A/C 1234 5678 9012' : '01XXXXXXXXX' }}">
                        </div>
                        <div class="adm-field">
                            <label class="adm-label">Account type / label</label>
                            <input type="text" class="adm-input" name="methods[{{ $i }}][type]" value="{{ old("methods.$i.type", $m['type']) }}" maxlength="120" placeholder="{{ $m['key'] === 'bank' ? 'Account Name: TrainersBD' : 'Send Money (Personal) / Payment (Merchant)' }}">
                        </div>
                        @if($m['key'] === 'bank')
                            <div class="adm-field">
                                <label class="adm-label">Bank &amp; branch</label>
                                <input type="text" class="adm-input" name="methods[{{ $i }}][details]" value="{{ old("methods.$i.details", $m['details']) }}" maxlength="200" placeholder="Dutch Bangla Bank, Uttara Branch">
                            </div>
                        @else
                            <input type="hidden" name="methods[{{ $i }}][details]" value="{{ $m['details'] }}">
                        @endif
                        <div class="adm-field mb-0">
                            <label class="adm-label">Instructions for students</label>
                            <textarea class="adm-textarea" name="methods[{{ $i }}][steps]" rows="4" maxlength="1500">{{ old("methods.$i.steps", $m['steps']) }}</textarea>
                            <div class="adm-hint">One step per line. Shown as a numbered list at checkout.</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="adm-alert adm-alert--info">
        <i class="fa-solid fa-circle-info"></i>
        <div>Payments are not verified automatically. When a student places an order, it appears in <a href="{{ route('admin.course-orders.index') }}"><b>Orders</b></a> as <b>Pending</b> with the sender number and transaction ID so your team can check and confirm it.</div>
    </div>
</form>
@endsection
