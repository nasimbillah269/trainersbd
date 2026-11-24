@extends('admin.layouts.app')
@php
    $isNew = !$course->exists;
    $curriculum = $course->exists
        ? $course->modules->map(fn ($m) => [
            'id' => $m->id, 'title' => $m->title,
            'lessons' => $m->lessons->map(fn ($l) => ['id' => $l->id, 'title' => $l->title, 'video' => $l->video, 'duration' => $l->duration, 'is_preview' => (bool) $l->is_preview])->values(),
        ])->values()
        : collect();
    if (old('curriculum')) { $curriculum = collect(json_decode(old('curriculum'), true) ?: []); }
    $val = fn ($field, $default = null) => old($field, $course->{$field} ?? $default);
    $err = fn ($f) => $errors->has($f) ? 'is-invalid' : '';
@endphp
@section('title')<title>{{ $isNew ? 'Add Course' : 'Edit Course' }} | {{ general()->title }} Admin</title>@endsection

@section('contents')
<form method="post" action="{{ $isNew ? route('admin.courses.store') : route('admin.courses.update', $course) }}" enctype="multipart/form-data" id="courseForm" novalidate>
    @csrf
    <input type="hidden" name="curriculum" id="curriculumInput">

    <div class="adm-page-head">
        <div>
            @php($isFree = $val('type', 'paid') === 'free')
            <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                @if($isFree)<li><a href="{{ route('admin.free-courses.index') }}">Free Courses</a></li>@else<li><a href="{{ route('admin.courses.index') }}">Courses</a></li>@endif
                <li>{{ $isNew ? 'Add' : 'Edit' }}</li></ul>
            <h1>{{ $isNew ? ($isFree ? 'Add free course' : 'Add new course') : \Illuminate\Support\Str::limit($course->title, 60) }}
                @if(!$isNew && $isFree)<span class="adm-badge adm-badge--free" style="vertical-align:middle;font-size:.75rem">Free · YouTube</span>@endif
            </h1>
            @unless($isNew)<p><span class="adm-badge adm-badge--{{ $course->status }}">{{ $course->status === 'active' ? 'Published' : 'Draft' }}</span> &nbsp;Last updated {{ $course->updated_at->diffForHumans() }}</p>@endunless
        </div>
        <div class="adm-page-head__actions">
            @unless($isNew)<a href="{{ $course->url() }}" target="_blank" class="adm-btn"><i class="fa-regular fa-eye"></i> Preview</a>@endunless
            <button type="submit" class="adm-btn adm-btn--primary"><i class="fa-solid fa-floppy-disk"></i> {{ $isNew ? 'Create Course' : 'Save Changes' }}</button>
        </div>
    </div>

    @include('admin.alerts')

    <div class="row">
        {{-- ================= MAIN ================= --}}
        <div class="col-xl-8">
            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-circle-info"></i> Basic information</h3></div>
                <div class="adm-card__body">
                    <div class="adm-field">
                        <label class="adm-label" for="title">Course title <span class="req">*</span></label>
                        <input type="text" class="adm-input {{ $err('title') }}" id="title" name="title" value="{{ $val('title') }}" required maxlength="220" placeholder="e.g. Complete Web Development Bootcamp">
                        @error('title')<div class="adm-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="slug">URL slug</label>
                        <div class="adm-input-group">
                            <span>{{ rtrim(url('/course'), '/') }}/</span>
                            <input type="text" class="adm-input {{ $err('slug') }}" id="slug" name="slug" value="{{ $val('slug') }}" placeholder="auto-generated-from-title" maxlength="250">
                        </div>
                        @error('slug')<div class="adm-error">{{ $message }}</div>@else<div class="adm-hint">Leave empty to create it from the title. Use lowercase letters, numbers and dashes.</div>@enderror
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="subtitle">Short description</label>
                        <textarea class="adm-textarea {{ $err('subtitle') }}" id="subtitle" name="subtitle" rows="2" maxlength="500" placeholder="One or two sentences shown under the title and on course cards">{{ $val('subtitle') }}</textarea>
                    </div>
                    <div class="adm-field mb-0">
                        <label class="adm-label" for="description">Full description</label>
                        <textarea class="tinyEditor" id="description" name="description">{{ $val('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-list-check"></i> Outcomes &amp; requirements</h3></div>
                <div class="adm-card__body">
                    <div class="row">
                        <div class="col-md-6 adm-field">
                            <label class="adm-label" for="learn_points">What students will learn</label>
                            <textarea class="adm-textarea" id="learn_points" name="learn_points" rows="7" placeholder="One point per line">{{ old('learn_points', implode("\n", $course->learn_points ?? [])) }}</textarea>
                            <div class="adm-hint">One point per line. Shown as a checklist.</div>
                        </div>
                        <div class="col-md-6 adm-field">
                            <label class="adm-label" for="requirements">Requirements</label>
                            <textarea class="adm-textarea" id="requirements" name="requirements" rows="7" placeholder="One requirement per line">{{ old('requirements', implode("\n", $course->requirements ?? [])) }}</textarea>
                            <div class="adm-hint">One requirement per line.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card__head">
                    <div>
                        <h3 class="adm-card__title"><i class="fa-solid fa-sitemap"></i> Curriculum</h3>
                        <p class="adm-card__sub">Course &rarr; Module &rarr; Lesson. Paste a YouTube link for each lesson. Drag <i class="fa-solid fa-grip-vertical"></i> to reorder.</p>
                    </div>
                    <div class="d-flex" style="gap:.5rem">
                        <span class="adm-badge adm-badge--plain" id="currStats">0 modules · 0 lessons</span>
                        <button type="button" class="adm-btn adm-btn--soft adm-btn--sm" id="addModule"><i class="fa-solid fa-plus"></i> Add module</button>
                    </div>
                </div>
                <div class="adm-card__body">
                    <div class="adm-alert adm-alert--info" id="paidPreviewHint" style="margin-bottom:1rem">
                        <i class="fa-solid fa-lock-open"></i>
                        <div>For <b>paid</b> courses only lessons marked <b>Preview</b> can be watched on the website. For <b>free</b> courses every lesson with a YouTube link is playable.</div>
                    </div>
                    <div class="adm-curr" id="curriculum"></div>
                    <div class="adm-curr-empty" id="currEmpty" hidden>
                        <i class="fa-solid fa-folder-plus fa-2x mb-2" style="color:var(--a-primary)"></i>
                        <p class="mb-2">No modules yet. Start by adding the first module of this course.</p>
                        <button type="button" class="adm-btn adm-btn--primary adm-btn--sm" data-add-module><i class="fa-solid fa-plus"></i> Add first module</button>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-magnifying-glass-chart"></i> SEO</h3></div>
                <div class="adm-card__body">
                    <div class="adm-field">
                        <label class="adm-label" for="meta_title">Meta title</label>
                        <input type="text" class="adm-input" id="meta_title" name="meta_title" value="{{ $val('meta_title') }}" maxlength="200" placeholder="Defaults to the course title">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="meta_description">Meta description</label>
                        <textarea class="adm-textarea" id="meta_description" name="meta_description" rows="2" maxlength="300" placeholder="Defaults to the short description">{{ $val('meta_description') }}</textarea>
                    </div>
                    <div class="adm-field mb-0">
                        <label class="adm-label" for="meta_keywords">Meta keywords</label>
                        <input type="text" class="adm-input" id="meta_keywords" name="meta_keywords" value="{{ $val('meta_keywords') }}" maxlength="300" placeholder="Comma separated">
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="col-xl-4">
            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-paper-plane"></i> Publish</h3></div>
                <div class="adm-card__body">
                    <div class="adm-field">
                        <label class="adm-label">Visibility</label>
                        <div class="adm-seg">
                            <label><input type="radio" name="status" value="active" @checked($val('status', 'active') === 'active')><i class="fa-solid fa-globe"></i><span><strong>Published</strong><small>Visible on website</small></span></label>
                            <label><input type="radio" name="status" value="draft" @checked($val('status') === 'draft')><i class="fa-regular fa-eye-slash"></i><span><strong>Draft</strong><small>Hidden</small></span></label>
                        </div>
                    </div>
                    <label class="adm-switch mb-3">
                        <input type="checkbox" name="featured" value="1" @checked(old('featured', $course->featured))>
                        <span class="adm-switch__ui"></span> Featured (shown first on home page)
                    </label>
                    <button type="submit" class="adm-btn adm-btn--primary adm-btn--block"><i class="fa-solid fa-floppy-disk"></i> {{ $isNew ? 'Create Course' : 'Save Changes' }}</button>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-tag"></i> Pricing</h3></div>
                <div class="adm-card__body">
                    <div class="adm-field">
                        <div class="adm-seg">
                            <label><input type="radio" name="type" value="paid" @checked($val('type', 'paid') === 'paid')><i class="fa-solid fa-bangladeshi-taka-sign"></i><span><strong>Paid</strong><small>Manual payment</small></span></label>
                            <label><input type="radio" name="type" value="free" @checked($val('type') === 'free')><i class="fa-brands fa-youtube"></i><span><strong>Free</strong><small>YouTube course</small></span></label>
                        </div>
                    </div>
                    <div data-paid-only>
                        <div class="row">
                            <div class="col-6 adm-field">
                                <label class="adm-label" for="price">Sale price <span class="req">*</span></label>
                                <div class="adm-input-group"><span>৳</span><input type="number" min="0" step="1" class="adm-input {{ $err('price') }}" id="price" name="price" value="{{ $val('price') + 0 ?: '' }}" placeholder="4500"></div>
                                @error('price')<div class="adm-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6 adm-field">
                                <label class="adm-label" for="old_price">Regular price</label>
                                <div class="adm-input-group"><span>৳</span><input type="number" min="0" step="1" class="adm-input" id="old_price" name="old_price" value="{{ $val('old_price') + 0 ?: '' }}" placeholder="8000"></div>
                            </div>
                        </div>
                        <div class="adm-hint" id="discountHint" style="margin-top:-.6rem;margin-bottom:1rem"></div>
                    </div>
                    <div class="adm-field mb-0">
                        <label class="adm-label" for="badge">Badge</label>
                        <input type="text" class="adm-input" id="badge" name="badge" value="{{ $val('badge') }}" maxlength="40" list="badgeList" placeholder="e.g. Bestseller, New">
                        <datalist id="badgeList"><option>Bestseller</option><option>Popular</option><option>New</option><option>Free</option><option>Hot</option></datalist>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-regular fa-image"></i> Thumbnail</h3></div>
                <div class="adm-card__body">
                    <label class="adm-upload">
                        <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" id="thumbInput">
                        @if($course->thumbnailUrl())
                            <img src="{{ $course->thumbnailUrl() }}" alt="" id="thumbPreview">
                            <span class="adm-upload__change"><i class="fa-solid fa-camera"></i> Change</span>
                        @else
                            <img src="" alt="" id="thumbPreview" hidden>
                            <span class="adm-upload__empty" id="thumbEmpty"><i class="fa-solid fa-cloud-arrow-up"></i><b>Click to upload</b>JPG, PNG or WebP, up to 4MB.<br>Resized to 1200×750 automatically.</span>
                        @endif
                    </label>
                    @error('thumbnail')<div class="adm-error">{{ $message }}</div>@enderror
                    <div class="adm-field mt-3 mb-0">
                        <label class="adm-label" for="thumbnail_alt">Image ALT text</label>
                        <input type="text" class="adm-input" id="thumbnail_alt" name="thumbnail_alt" value="{{ $val('thumbnail_alt') }}" maxlength="200" placeholder="Describe the image for SEO">
                    </div>
                    @if($course->thumbnail)
                        <label class="adm-switch mt-3"><input type="checkbox" name="remove_thumbnail" value="1"><span class="adm-switch__ui"></span> Remove current image</label>
                    @endif
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-folder-tree"></i> Details</h3></div>
                <div class="adm-card__body">
                    <div class="adm-field">
                        <label class="adm-label" for="category_id">Category</label>
                        <select class="adm-select" id="category_id" name="category_id">
                            <option value="">— Select category —</option>
                            @foreach($categories as $c)<option value="{{ $c->id }}" @selected($val('category_id') == $c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                        <div class="adm-hint"><a href="{{ route('admin.course-categories.index') }}" target="_blank">Manage categories</a></div>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="instructor_id">Instructor</label>
                        <select class="adm-select" id="instructor_id" name="instructor_id">
                            <option value="">— Select instructor —</option>
                            @foreach($instructors as $i)<option value="{{ $i->id }}" @selected($val('instructor_id') == $i->id)>{{ $i->name }}{{ $i->title ? ' — ' . $i->title : '' }}</option>@endforeach
                        </select>
                        <div class="adm-hint"><a href="{{ route('admin.instructors.index') }}" target="_blank">Manage instructors</a></div>
                    </div>
                    <div class="row">
                        <div class="col-6 adm-field">
                            <label class="adm-label" for="level">Level</label>
                            <input type="text" class="adm-input" id="level" name="level" value="{{ $val('level') }}" list="levelList" placeholder="Beginner">
                            <datalist id="levelList"><option>Beginner</option><option>Intermediate</option><option>Advanced</option><option>All Levels</option><option>Beginner to Advanced</option></datalist>
                        </div>
                        <div class="col-6 adm-field">
                            <label class="adm-label" for="language">Language</label>
                            <input type="text" class="adm-input" id="language" name="language" value="{{ $val('language', 'Bangla') }}" placeholder="Bangla">
                        </div>
                        <div class="col-6 adm-field">
                            <label class="adm-label" for="duration">Total duration</label>
                            <input type="text" class="adm-input" id="duration" name="duration" value="{{ $val('duration') }}" placeholder="24h 30m">
                        </div>
                        <div class="col-6 adm-field">
                            <label class="adm-label" for="students_count">Students (display)</label>
                            <input type="number" min="0" class="adm-input" id="students_count" name="students_count" value="{{ $val('students_count', 0) }}">
                        </div>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="preview_video">Preview video (YouTube)</label>
                        <input type="url" class="adm-input {{ $err('preview_video') }}" id="preview_video" name="preview_video" value="{{ $val('preview_video') }}" placeholder="https://www.youtube.com/watch?v=…">
                        @error('preview_video')<div class="adm-error">{{ $message }}</div>@else<div class="adm-hint">Plays when visitors click the course image.</div>@enderror
                    </div>
                    <div class="adm-field mb-0">
                        <label class="adm-label" for="sort">Sort order</label>
                        <input type="number" class="adm-input" id="sort" name="sort" value="{{ $val('sort', 0) }}">
                        <div class="adm-hint">Lower numbers appear first.</div>
                    </div>
                </div>
            </div>

            @unless($isNew)
                <div class="adm-card">
                    <div class="adm-card__body">
                        <button type="submit" form="deleteCourse" class="adm-btn adm-btn--danger adm-btn--block" data-confirm="Delete this course permanently? Modules, lessons and reviews will be removed too."><i class="fa-regular fa-trash-can"></i> Delete course</button>
                    </div>
                </div>
            @endunless
        </div>
    </div>

    <div class="adm-savebar">
        <small><i class="fa-regular fa-lightbulb"></i> Changes are saved when you click Save, including the curriculum.</small>
        <button type="submit" class="adm-btn adm-btn--primary"><i class="fa-solid fa-floppy-disk"></i> {{ $isNew ? 'Create Course' : 'Save Changes' }}</button>
    </div>
</form>

@unless($isNew)
    <form method="post" action="{{ route('admin.courses.destroy', $course) }}" id="deleteCourse" class="d-none">@csrf</form>
@endunless

<template id="moduleTpl">
    <div class="adm-module" data-module>
        <div class="adm-module__head">
            <span class="adm-handle" data-module-handle title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></span>
            <span class="adm-module__num" data-module-num>1</span>
            <input type="text" class="adm-input adm-input--sm adm-module__title" data-module-title placeholder="Module title, e.g. Getting started" maxlength="200">
            <button type="button" class="adm-btn adm-btn--xs adm-btn--icon" data-collapse title="Collapse"><i class="fa-solid fa-chevron-up"></i></button>
            <button type="button" class="adm-btn adm-btn--xs adm-btn--icon adm-btn--danger" data-remove-module title="Remove module"><i class="fa-regular fa-trash-can"></i></button>
        </div>
        <ul class="adm-lessons" data-lessons></ul>
        <div class="adm-module__foot">
            <button type="button" class="adm-btn adm-btn--xs adm-btn--soft" data-add-lesson><i class="fa-solid fa-plus"></i> Add lesson</button>
        </div>
    </div>
</template>
<template id="lessonTpl">
    <li class="adm-lesson" data-lesson>
        <span class="adm-handle" data-lesson-handle title="Drag to reorder"><i class="fa-solid fa-grip-lines"></i></span>
        <input type="text" class="adm-input adm-input--sm" data-l="title" placeholder="Lesson title" maxlength="220">
        <input type="url" class="adm-input adm-input--sm" data-l="video" placeholder="YouTube link (optional)" maxlength="200">
        <input type="text" class="adm-input adm-input--sm" data-l="duration" placeholder="10:30" maxlength="20">
        <label class="adm-switch" title="Free preview"><input type="checkbox" data-l="is_preview"><span class="adm-switch__ui"></span> Preview</label>
        <button type="button" class="adm-btn adm-btn--xs adm-btn--icon adm-btn--danger" data-remove-lesson title="Remove lesson"><i class="fa-solid fa-xmark"></i></button>
    </li>
</template>
@endsection

@push('js')
<script>
(function ($) {
    var data = @json($curriculum);
    var $wrap = $('#curriculum');
    var ytRe = /(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/))([A-Za-z0-9_-]{11})|^([A-Za-z0-9_-]{11})$/;

    function addLesson($module, l) {
        l = l || {};
        var $l = $($('#lessonTpl').html());
        $l.attr('data-id', l.id || '');
        $l.find('[data-l=title]').val(l.title || '');
        $l.find('[data-l=video]').val(l.video || '');
        $l.find('[data-l=duration]').val(l.duration || '');
        $l.find('[data-l=is_preview]').prop('checked', !!l.is_preview);
        $module.find('[data-lessons]').append($l);
        return $l;
    }
    function addModule(m) {
        m = m || {};
        var $m = $($('#moduleTpl').html());
        $m.attr('data-id', m.id || '');
        $m.find('[data-module-title]').val(m.title || '');
        (m.lessons || []).forEach(function (l) { addLesson($m, l); });
        $wrap.append($m);
        makeLessonsSortable($m);
        refresh();
        return $m;
    }
    function refresh() {
        var mods = $wrap.children('[data-module]');
        mods.each(function (i) { $(this).find('[data-module-num]').text(String(i + 1).padStart(2, '0')); });
        $('#currEmpty').prop('hidden', mods.length > 0);
        $('#currStats').text(mods.length + ' modules · ' + $wrap.find('[data-lesson]').length + ' lessons');
    }
    function makeLessonsSortable($m) {
        $m.find('[data-lessons]').sortable({ handle: '[data-lesson-handle]', connectWith: '[data-lessons]', placeholder: 'adm-sort-ph', tolerance: 'pointer', stop: refresh });
    }

    data.forEach(addModule);
    $wrap.sortable({ handle: '[data-module-handle]', placeholder: 'adm-sort-ph', stop: refresh });
    refresh();

    $('#addModule, [data-add-module]').on('click', function () {
        var $m = addModule({ lessons: [{}] });
        $m.find('[data-module-title]').trigger('focus');
        $('html, body').animate({ scrollTop: $m.offset().top - 120 }, 300);
    });
    $wrap.on('click', '[data-add-lesson]', function () {
        addLesson($(this).closest('[data-module]')).find('[data-l=title]').trigger('focus');
        refresh();
    });
    $wrap.on('click', '[data-remove-lesson]', function () {
        $(this).closest('[data-lesson]').remove(); refresh();
    });
    $wrap.on('click', '[data-remove-module]', function () {
        var $m = $(this).closest('[data-module]');
        var n = $m.find('[data-lesson]').length;
        if (n && !confirm('Remove this module and its ' + n + ' lesson(s)?')) { return; }
        $m.remove(); refresh();
    });
    $wrap.on('click', '[data-collapse]', function () {
        var $m = $(this).closest('[data-module]');
        $m.find('[data-lessons], .adm-module__foot').slideToggle(150);
        $(this).find('i').toggleClass('fa-chevron-up fa-chevron-down');
    });
    $wrap.on('blur', '[data-l=video]', function () {
        var v = $.trim(this.value);
        this.classList.toggle('is-invalid', v !== '' && !ytRe.test(v));
    });

    $('#courseForm').on('submit', function (e) {
        if (window.tinymce) { tinymce.triggerSave(); }
        var bad = $wrap.find('[data-l=video].is-invalid');
        if (bad.length) { e.preventDefault(); alert('Please fix the highlighted YouTube links.'); bad.first().trigger('focus'); return; }
        var out = [];
        $wrap.children('[data-module]').each(function () {
            var $m = $(this);
            var lessons = [];
            $m.find('[data-lesson]').each(function () {
                var $l = $(this);
                lessons.push({ id: $l.attr('data-id') || null, title: $l.find('[data-l=title]').val(), video: $l.find('[data-l=video]').val(), duration: $l.find('[data-l=duration]').val(), is_preview: $l.find('[data-l=is_preview]').is(':checked') });
            });
            out.push({ id: $m.attr('data-id') || null, title: $m.find('[data-module-title]').val(), lessons: lessons });
        });
        $('#curriculumInput').val(JSON.stringify(out));
        $(this).find('[type=submit]').prop('disabled', true).first().html('<i class="fa-solid fa-spinner fa-spin"></i> Saving…');
    });

    // Paid / free toggle
    function syncType() {
        var free = $('input[name=type]:checked').val() === 'free';
        $('[data-paid-only]').toggle(!free);
        $('#paidPreviewHint').toggle(!free);
    }
    $('input[name=type]').on('change', syncType); syncType();

    // Discount hint
    function syncDiscount() {
        var p = parseFloat($('#price').val()) || 0, o = parseFloat($('#old_price').val()) || 0;
        $('#discountHint').html(o > p && p > 0 ? '<i class="fa-solid fa-tag" style="color:#12B76A"></i> Students see <b>' + Math.round((1 - p / o) * 100) + '% off</b>' : '');
    }
    $('#price, #old_price').on('input', syncDiscount); syncDiscount();

    // Slug preview from title (only when empty / untouched)
    var slugTouched = !!$('#slug').val();
    $('#slug').on('input', function () { slugTouched = true; });
    $('#title').on('input', function () {
        if (slugTouched) { return; }
        $('#slug').attr('placeholder', this.value.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-') || 'auto-generated-from-title');
    });

    // Thumbnail preview
    $('#thumbInput').on('change', function () {
        var f = this.files && this.files[0];
        if (!f) { return; }
        var reader = new FileReader();
        reader.onload = function (ev) { $('#thumbPreview').attr('src', ev.target.result).prop('hidden', false); $('#thumbEmpty').remove(); };
        reader.readAsDataURL(f);
    });
})(jQuery);
</script>
@endpush
