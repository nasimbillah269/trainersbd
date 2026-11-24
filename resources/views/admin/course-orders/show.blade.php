@extends('admin.layouts.app')
@section('title')<title>Order #{{ $order->invoice }} | {{ general()->title }} Admin</title>@endsection
@php use App\Support\Storefront; use App\Models\CourseOrder; @endphp

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li><a href="{{ route('admin.course-orders.index') }}">Orders</a></li><li>#{{ $order->invoice }}</li></ul>
        <h1>Order <span class="adm-mono">#{{ $order->invoice }}</span></h1>
        <p>
            <span class="adm-badge adm-badge--{{ $order->status }}">{{ CourseOrder::STATUSES[$order->status] }}</span>
            <span class="adm-badge adm-badge--{{ $order->payment_status }}">Payment {{ strtolower(CourseOrder::PAYMENT_STATUSES[$order->payment_status]) }}</span>
            &nbsp;Placed {{ $order->created_at->format('d M Y, h:i A') }} ({{ $order->created_at->diffForHumans() }})
        </p>
    </div>
    <div class="adm-page-head__actions">
        @if($order->status !== 'confirmed')
            <form method="post" action="{{ route('admin.course-orders.quick', $order) }}">@csrf<input type="hidden" name="action" value="confirm"><button class="adm-btn adm-btn--success" data-confirm="Mark payment {{ $order->transaction_id }} as verified and confirm this order?"><i class="fa-solid fa-circle-check"></i> Verify &amp; confirm</button></form>
        @endif
        @if(!in_array($order->status, ['hold', 'cancelled']))
            <form method="post" action="{{ route('admin.course-orders.quick', $order) }}">@csrf<input type="hidden" name="action" value="hold"><button class="adm-btn adm-btn--warning"><i class="fa-solid fa-pause"></i> Hold</button></form>
        @endif
        @if($order->status !== 'cancelled')
            <form method="post" action="{{ route('admin.course-orders.quick', $order) }}">@csrf<input type="hidden" name="action" value="cancel"><button class="adm-btn adm-btn--danger" data-confirm="Cancel order #{{ $order->invoice }}?"><i class="fa-solid fa-xmark"></i> Cancel</button></form>
        @endif
    </div>
</div>

@include('admin.alerts')

<div class="row">
    <div class="col-xl-8">
        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-wallet"></i> Payment to verify</h3></div>
            <div class="adm-card__body">
                <div class="adm-paybox adm-paybox--{{ $order->payment_method }}">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <small class="text-muted d-block">Transaction ID</small>
                            <div class="adm-paybox__trx adm-mono">{{ $order->transaction_id }} <button type="button" class="adm-copy" data-copy="{{ $order->transaction_id }}" title="Copy"><i class="fa-regular fa-copy"></i></button></div>
                        </div>
                        <div class="col-md-3 col-6">
                            <small class="text-muted d-block">Method</small>
                            <strong class="text-dark">{{ $order->methodName() }}</strong>
                        </div>
                        <div class="col-md-4 col-6">
                            <small class="text-muted d-block">Sent from</small>
                            <strong class="text-dark adm-mono">{{ $order->sender_number }}</strong>
                        </div>
                    </div>
                    <hr style="border-color:rgba(0,0,0,.06)">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem">
                        <span class="text-muted">Amount the student should have sent</span>
                        <strong style="font-size:1.4rem;color:var(--a-ink)">{{ Storefront::money($order->total) }}</strong>
                    </div>
                </div>
                <p class="adm-hint mt-3 mb-0"><i class="fa-regular fa-lightbulb"></i> Check your {{ $order->methodName() }} statement for this transaction ID and amount before confirming.</p>

                @if($duplicates->count())
                    <div class="adm-alert adm-alert--warning mt-3 mb-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div><strong>Other orders with the same mobile number or transaction ID:</strong>
                            <ul>@foreach($duplicates as $d)<li><a href="{{ route('admin.course-orders.show', $d) }}">#{{ $d->invoice }}</a> — {{ CourseOrder::STATUSES[$d->status] }}, TrxID {{ $d->transaction_id }}, {{ $d->created_at->format('d M Y') }}</li>@endforeach</ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-layer-group"></i> Courses ordered</h3></div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th>Course</th><th class="num">Regular</th><th class="num">Price</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td><div class="adm-cell">
                                    @if($item->course && $item->course->thumbnailUrl())<img src="{{ $item->course->thumbnailUrl() }}" class="adm-thumb" alt="">@else<span class="adm-thumb"><i class="fa-regular fa-image"></i></span>@endif
                                    <div><strong>{{ $item->title }}</strong>
                                        @if($item->course)<small><a href="{{ route('admin.courses.edit', $item->course_id) }}">Edit course</a> &middot; <a href="{{ $item->course->url() }}" target="_blank">View</a></small>@else<small>Course deleted</small>@endif
                                    </div>
                                </div></td>
                                <td class="num text-muted">@if($item->old_price > $item->price)<s>{{ Storefront::money($item->old_price) }}</s>@else — @endif</td>
                                <td class="num"><b>{{ Storefront::money($item->price) }}</b></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="adm-card__foot">
                <dl class="adm-dl ml-auto" style="max-width:320px">
                    <div><dt>Subtotal</dt><dd>{{ Storefront::money($order->subtotal) }}</dd></div>
                    <div><dt>Discount</dt><dd style="color:var(--a-success)">-{{ Storefront::money($order->discount) }}</dd></div>
                    <div class="adm-total"><dt>Total</dt><dd>{{ Storefront::money($order->total) }}</dd></div>
                </dl>
            </div>
        </div>

        @if($order->note)
            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-regular fa-message"></i> Note from student</h3></div>
                <div class="adm-card__body">{{ $order->note }}</div>
            </div>
        @endif
    </div>

    <div class="col-xl-4">
        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-regular fa-user"></i> Student</h3></div>
            <div class="adm-card__body">
                <div class="adm-cell mb-3"><span class="adm-avatar adm-avatar--lg">{{ \App\Support\Storefront::initials($order->name) }}</span><div><strong style="font-size:1.05rem">{{ $order->name }}</strong><small>{{ $order->email }}</small></div></div>
                <div class="d-flex flex-wrap" style="gap:.5rem">
                    <a href="tel:{{ $order->mobile }}" class="adm-btn adm-btn--sm"><i class="fa-solid fa-phone"></i> {{ $order->mobile }}</a>
                    <a href="https://wa.me/{{ preg_replace('/^0/', '880', preg_replace('/\D/', '', $order->mobile)) }}" target="_blank" rel="noopener" class="adm-btn adm-btn--sm"><i class="fa-brands fa-whatsapp" style="color:#25D366"></i> WhatsApp</a>
                    <a href="mailto:{{ $order->email }}?subject={{ rawurlencode('Your TrainersBD order #' . $order->invoice) }}" class="adm-btn adm-btn--sm"><i class="fa-regular fa-envelope"></i> Email</a>
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-pen-to-square"></i> Update order</h3></div>
            <form method="post" action="{{ route('admin.course-orders.update', $order) }}" class="adm-card__body">
                @csrf
                <div class="adm-field">
                    <label class="adm-label" for="oStatus">Order status</label>
                    <select class="adm-select" id="oStatus" name="status">
                        @foreach(CourseOrder::STATUSES as $k => $l)<option value="{{ $k }}" @selected($order->status === $k)>{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="oPay">Payment status</label>
                    <select class="adm-select" id="oPay" name="payment_status">
                        @foreach(CourseOrder::PAYMENT_STATUSES as $k => $l)<option value="{{ $k }}" @selected($order->payment_status === $k)>{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="oNote">Internal note</label>
                    <textarea class="adm-textarea" id="oNote" name="admin_note" rows="3" placeholder="Only visible to admins, e.g. enrolled in WhatsApp group on 24 Nov">{{ old('admin_note', $order->admin_note) }}</textarea>
                </div>
                <button class="adm-btn adm-btn--primary adm-btn--block"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </form>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-timeline"></i> Activity</h3></div>
            <div class="adm-card__body">
                <ul class="adm-timeline">
                    <li><b>Order placed</b><small>{{ $order->created_at->format('d M Y, h:i A') }} &middot; IP {{ $order->ip ?: '—' }}</small></li>
                    @if($order->verified_at)<li><b>Payment verified</b><small>{{ $order->verified_at->format('d M Y, h:i A') }} by {{ $order->verifier->name ?? 'admin' }}</small></li>@endif
                    @if($order->updated_at->gt($order->created_at))<li><b>Last updated</b><small>{{ $order->updated_at->format('d M Y, h:i A') }}</small></li>@endif
                </ul>
                <form method="post" action="{{ route('admin.course-orders.destroy', $order) }}" class="mt-2">@csrf<button class="adm-btn adm-btn--sm adm-btn--danger" data-confirm="Delete this order permanently?"><i class="fa-regular fa-trash-can"></i> Delete order</button></form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).on('click', '[data-copy]', function () {
        var b = this, t = b.getAttribute('data-copy');
        (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject()).catch(function () {}).then(function () {
            b.innerHTML = '<i class="fa-solid fa-check"></i>'; setTimeout(function () { b.innerHTML = '<i class="fa-regular fa-copy"></i>'; }, 1500);
        });
    });
</script>
@endpush
