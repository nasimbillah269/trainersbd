@extends('admin.layouts.app')
@section('title')<title>Course Categories | {{ general()->title }} Admin</title>@endsection

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li><a href="{{ route('admin.courses.index') }}">Courses</a></li><li>Categories</li></ul>
        <h1>Categories</h1>
        <p>Group courses so students can filter them on the website.</p>
    </div>
</div>

@include('admin.alerts')

<div class="row">
    <div class="col-xl-4">
        <div class="adm-card" id="catFormCard">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-plus"></i> <span data-form-title>Add category</span></h3></div>
            <form method="post" action="{{ route('admin.course-categories.store') }}" class="adm-card__body" id="catForm" data-store="{{ route('admin.course-categories.store') }}">
                @csrf
                <div class="adm-field">
                    <label class="adm-label" for="cName">Name <span class="req">*</span></label>
                    <input type="text" class="adm-input" id="cName" name="name" value="{{ old('name') }}" required maxlength="150" placeholder="e.g. Web Development">
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="cSlug">Slug</label>
                    <input type="text" class="adm-input" id="cSlug" name="slug" value="{{ old('slug') }}" maxlength="180" placeholder="auto from name">
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="cIcon">Icon</label>
                    <div class="adm-input-group">
                        <span><i class="bi bi-grid" id="cIconPreview" style="font-size:1.1rem"></i></span>
                        <input type="text" class="adm-input" id="cIcon" name="icon" value="{{ old('icon') }}" placeholder="bi-code-slash">
                    </div>
                    <div class="adm-hint">A <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Bootstrap Icons</a> class. Quick pick:</div>
                    <div class="d-flex flex-wrap mt-2" style="gap:.35rem">
                        @foreach(['bi-code-slash','bi-palette','bi-megaphone','bi-laptop','bi-file-earmark-spreadsheet','bi-chat-quote','bi-cpu','bi-camera-reels','bi-graph-up','bi-phone','bi-translate','bi-briefcase'] as $ic)
                            <button type="button" class="adm-btn adm-btn--xs adm-btn--icon" data-pick-icon="{{ $ic }}" title="{{ $ic }}"><i class="bi {{ $ic }}"></i></button>
                        @endforeach
                    </div>
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="cSort">Sort order</label>
                    <input type="number" class="adm-input" id="cSort" name="sort" value="{{ old('sort', 0) }}">
                </div>
                <label class="adm-switch mb-3"><input type="checkbox" name="status" value="1" id="cStatus" checked><span class="adm-switch__ui"></span> Active</label>
                <div class="d-flex" style="gap:.5rem">
                    <button class="adm-btn adm-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                    <button class="adm-btn" type="button" data-reset hidden>Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-shapes"></i> All categories <span class="adm-badge adm-badge--plain">{{ $categories->count() }}</span></h3></div>
            @if($categories->isEmpty())
                <div class="adm-empty"><i class="fa-solid fa-shapes"></i><h5>No categories yet</h5><p>Add your first category using the form.</p></div>
            @else
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>Category</th><th>Slug</th><th class="num">Courses</th><th class="num">Order</th><th>Status</th><th class="actions">Actions</th></tr></thead>
                        <tbody>
                            @foreach($categories as $cat)
                                <tr>
                                    <td><div class="adm-cell"><span class="adm-avatar" style="background:var(--a-primary-50);color:var(--a-primary);font-size:1.05rem"><i class="bi {{ $cat->icon ?: 'bi-grid' }}"></i></span><strong>{{ $cat->name }}</strong></div></td>
                                    <td class="adm-mono text-muted">{{ $cat->slug }}</td>
                                    <td class="num"><a href="{{ route('admin.courses.index', ['category' => $cat->id]) }}">{{ $cat->courses_count }}</a></td>
                                    <td class="num">{{ $cat->sort }}</td>
                                    <td><span class="adm-badge adm-badge--{{ $cat->status ? 'active' : 'draft' }}">{{ $cat->status ? 'Active' : 'Hidden' }}</span></td>
                                    <td class="actions">
                                        <button type="button" class="adm-btn adm-btn--sm adm-btn--icon" title="Edit" data-edit="{{ json_encode(['url' => route('admin.course-categories.update', $cat), 'name' => $cat->name, 'slug' => $cat->slug, 'icon' => $cat->icon, 'sort' => $cat->sort, 'status' => $cat->status]) }}"><i class="fa-regular fa-pen-to-square"></i></button>
                                        <form method="post" action="{{ route('admin.course-categories.destroy', $cat) }}">@csrf<button class="adm-btn adm-btn--sm adm-btn--icon adm-btn--danger" title="Delete" data-confirm="Delete category “{{ $cat->name }}”? Its {{ $cat->courses_count }} course(s) will become uncategorised."><i class="fa-regular fa-trash-can"></i></button></form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('css')<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">@endpush
@push('js')
<script>
(function ($) {
    var $f = $('#catForm');
    function icon() { $('#cIconPreview').attr('class', 'bi ' + ($('#cIcon').val() || 'bi-grid')); }
    $('#cIcon').on('input', icon); icon();
    $('[data-pick-icon]').on('click', function () { $('#cIcon').val($(this).data('pick-icon')); icon(); });
    $('[data-edit]').on('click', function () {
        var d = $(this).data('edit');
        $f.attr('action', d.url);
        $('#cName').val(d.name); $('#cSlug').val(d.slug); $('#cIcon').val(d.icon); $('#cSort').val(d.sort); $('#cStatus').prop('checked', !!d.status);
        icon();
        $('[data-form-title]').text('Edit category'); $('[data-reset]').prop('hidden', false);
        $('html, body').animate({ scrollTop: $('#catFormCard').offset().top - 100 }, 250);
        $('#cName').trigger('focus');
    });
    $('[data-reset]').on('click', function () {
        $f.attr('action', $f.data('store'))[0].reset(); icon();
        $('[data-form-title]').text('Add category'); $(this).prop('hidden', true);
    });
})(jQuery);
</script>
@endpush
