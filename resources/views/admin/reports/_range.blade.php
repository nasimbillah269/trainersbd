{{-- Date-range presets shared by every report. Keeps the page's other filters when switching range. --}}
@php
    $presets = ['today' => 'Today', '7d' => '7 days', '30d' => '30 days', 'month' => 'This month', 'year' => 'This year', 'all' => 'All time'];
    $keep = request()->except(['range', 'startDate', 'endDate', 'page', 'export']);
    $current = request('range', (request('startDate') || request('endDate')) ? 'custom' : $defaultRange);
@endphp
<div class="adm-toolbar">
    <nav class="adm-tabs">
        @foreach($presets as $key => $label)
            <a href="{{ route('admin.reportsAll', array_merge(['type' => $type], $keep, ['range' => $key])) }}" class="{{ $current === $key ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>
    <form method="get" action="{{ route('admin.reportsAll', $type) }}" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
        @foreach($keep as $k => $v)
            @if(!is_array($v))<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
        @endforeach
        <input type="date" name="startDate" value="{{ $from?->format('Y-m-d') }}" class="adm-input adm-input--sm" style="width:150px" title="From date">
        <span class="text-muted">to</span>
        <input type="date" name="endDate" value="{{ $to?->format('Y-m-d') }}" class="adm-input adm-input--sm" style="width:150px" title="To date">
        <button class="adm-btn adm-btn--sm adm-btn--primary"><i class="fa-solid fa-filter"></i> Apply</button>
    </form>
</div>
<p class="text-muted" style="margin:-.5rem 0 1rem;font-size:.82rem">
    <i class="fa-regular fa-calendar"></i>
    @if($from || $to)
        Showing {{ $from ? $from->format('d M Y') : 'beginning' }} &ndash; {{ $to ? $to->format('d M Y') : 'today' }}
    @else
        Showing all time
    @endif
</p>
