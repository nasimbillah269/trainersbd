@extends('admin.layouts.app')
@section('title')<title>Instructors | {{ general()->title }} Admin</title>@endsection

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li><a href="{{ route('admin.courses.index') }}">Courses</a></li><li>Instructors</li></ul>
        <h1>Instructors</h1>
        <p>Trainers shown on course pages.</p>
    </div>
</div>

@include('admin.alerts')

<div class="row">
    <div class="col-xl-4">
        <div class="adm-card" id="insFormCard">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-user-plus"></i> <span data-form-title>Add instructor</span></h3></div>
            <form method="post" action="{{ route('admin.instructors.store') }}" enctype="multipart/form-data" class="adm-card__body" id="insForm" data-store="{{ route('admin.instructors.store') }}">
                @csrf
                <div class="d-flex align-items-center mb-3" style="gap:1rem">
                    <label class="adm-upload adm-upload--square" style="width:96px;flex-shrink:0;border-radius:20px">
                        <input type="file" name="photo" accept="image/*" id="iPhoto">
                        <img src="" alt="" id="iPhotoPreview" hidden>
                        <span class="adm-upload__empty" id="iPhotoEmpty" style="padding:.25rem"><i class="fa-solid fa-camera" style="font-size:1.2rem"></i><small>Photo</small></span>
                    </label>
                    <div class="adm-hint m-0">Square photo works best. Resized to 400×400. Optional; initials are shown without a photo.</div>
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="iName">Name <span class="req">*</span></label>
                    <input type="text" class="adm-input" id="iName" name="name" required maxlength="150" value="{{ old('name') }}">
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="iTitle">Designation</label>
                    <input type="text" class="adm-input" id="iTitle" name="title" maxlength="150" value="{{ old('title') }}" placeholder="e.g. Senior Web Developer">
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="iBio">Short bio</label>
                    <textarea class="adm-textarea" id="iBio" name="bio" rows="4" maxlength="3000">{{ old('bio') }}</textarea>
                </div>
                <label class="adm-switch mb-3"><input type="checkbox" name="status" value="1" id="iStatus" checked><span class="adm-switch__ui"></span> Active</label>
                <div class="d-flex" style="gap:.5rem">
                    <button class="adm-btn adm-btn--primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                    <button class="adm-btn" type="button" data-reset hidden>Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-chalkboard-user"></i> All instructors <span class="adm-badge adm-badge--plain">{{ $instructors->count() }}</span></h3></div>
            @if($instructors->isEmpty())
                <div class="adm-empty"><i class="fa-solid fa-chalkboard-user"></i><h5>No instructors yet</h5></div>
            @else
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>Instructor</th><th>Bio</th><th class="num">Courses</th><th>Status</th><th class="actions">Actions</th></tr></thead>
                        <tbody>
                            @foreach($instructors as $ins)
                                <tr>
                                    <td style="min-width:220px"><div class="adm-cell">
                                        @if($ins->photoUrl())<img src="{{ $ins->photoUrl() }}" class="adm-avatar" alt="">@else<span class="adm-avatar">{{ $ins->initials() }}</span>@endif
                                        <div><strong>{{ $ins->name }}</strong><small>{{ $ins->title }}</small></div>
                                    </div></td>
                                    <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($ins->bio, 90) }}</small></td>
                                    <td class="num">{{ $ins->courses_count }}</td>
                                    <td><span class="adm-badge adm-badge--{{ $ins->status ? 'active' : 'draft' }}">{{ $ins->status ? 'Active' : 'Hidden' }}</span></td>
                                    <td class="actions">
                                        <button type="button" class="adm-btn adm-btn--sm adm-btn--icon" title="Edit" data-edit="{{ json_encode(['url' => route('admin.instructors.update', $ins), 'name' => $ins->name, 'title' => $ins->title, 'bio' => $ins->bio, 'status' => $ins->status, 'photo' => $ins->photoUrl()]) }}"><i class="fa-regular fa-pen-to-square"></i></button>
                                        <form method="post" action="{{ route('admin.instructors.destroy', $ins) }}">@csrf<button class="adm-btn adm-btn--sm adm-btn--icon adm-btn--danger" title="Delete" data-confirm="Delete instructor “{{ $ins->name }}”?"><i class="fa-regular fa-trash-can"></i></button></form>
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

@push('js')
<script>
(function ($) {
    var $f = $('#insForm');
    function photo(src) {
        $('#iPhotoPreview').attr('src', src || '').prop('hidden', !src);
        $('#iPhotoEmpty').toggle(!src);
    }
    $('#iPhoto').on('change', function () {
        var file = this.files && this.files[0]; if (!file) { return; }
        var r = new FileReader(); r.onload = function (e) { photo(e.target.result); }; r.readAsDataURL(file);
    });
    $('[data-edit]').on('click', function () {
        var d = $(this).data('edit');
        $f.attr('action', d.url);
        $('#iName').val(d.name); $('#iTitle').val(d.title); $('#iBio').val(d.bio); $('#iStatus').prop('checked', !!d.status);
        photo(d.photo);
        $('[data-form-title]').text('Edit instructor'); $('[data-reset]').prop('hidden', false);
        $('html, body').animate({ scrollTop: $('#insFormCard').offset().top - 100 }, 250);
    });
    $('[data-reset]').on('click', function () {
        $f.attr('action', $f.data('store'))[0].reset(); photo(null);
        $('[data-form-title]').text('Add instructor'); $(this).prop('hidden', true);
    });
})(jQuery);
</script>
@endpush
