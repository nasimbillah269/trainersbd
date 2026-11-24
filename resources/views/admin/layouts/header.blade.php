@php($me = Auth::user())
<header class="adm-topbar">
    <button type="button" class="adm-icon-btn" data-adm-toggle aria-label="Toggle menu"><i class="fa-solid fa-bars-staggered"></i></button>

    <form class="adm-search" action="{{ route('admin.course-orders.index') }}" method="get" role="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="q" placeholder="Search orders by invoice, name, mobile or TrxID…" value="{{ request()->routeIs('admin.course-orders.index') ? request('q') : '' }}">
    </form>

    <div class="adm-topbar__right">
        <a href="{{ route('admin.courses.create') }}" class="adm-btn adm-btn--primary adm-btn--sm d-none d-md-inline-flex"><i class="fa-solid fa-plus"></i> New Course</a>
        <a href="{{ route('index') }}" target="_blank" class="adm-icon-btn" title="View website"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        @php($pendingOrders = \App\Models\CourseOrder::where('status', 'pending')->count())
        <a href="{{ route('admin.course-orders.index', ['status' => 'pending']) }}" class="adm-icon-btn" title="Pending orders">
            <i class="fa-regular fa-bell"></i>
            @if($pendingOrders)<span class="adm-dot">{{ $pendingOrders > 9 ? '9+' : $pendingOrders }}</span>@endif
        </a>
        <div class="dropdown">
            <button type="button" class="adm-user" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ asset($me->image()) }}" alt="" onerror="this.style.display='none'">
                <span class="adm-user__text"><strong>{{ \Illuminate\Support\Str::limit($me->name, 18) }}</strong><small>{{ optional($me->permission)->name ?: 'Administrator' }}</small></span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right adm-dropdown">
                <a class="dropdown-item" href="{{ route('admin.myProfile') }}"><i class="fa-regular fa-user"></i> My Profile</a>
                <a class="dropdown-item" href="{{ route('index') }}" target="_blank"><i class="fa-solid fa-globe"></i> View Website</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</header>
