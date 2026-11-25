@extends('frontend.layouts.app')

@php
    $allLessons = $course->modules->flatMap->lessons;
    $videoCount = $allLessons->filter(fn ($l) => $l->videoId())->count();
@endphp

@section('title', $course->title . ' | My Courses')
@section('body_class', 'page-account')
@push('css')<meta name="robots" content="noindex, nofollow">@endpush

@section('content')
<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">My Courses</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $course->title }}</li>
            </ol>
        </nav>
        <h1 class="page-head__title">{{ $course->title }}</h1>
        <p class="text-muted mb-0">
            @if($course->instructor)By {{ $course->instructor->name }} &middot; @endif
            {{ $course->modules->count() }} modules &middot; {{ $allLessons->count() }} lessons @if($course->duration)&middot; {{ $course->duration }}@endif
        </p>
    </div>
</section>

<section class="section pt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                @if($videoCount === 0)
                    <div class="summary-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        <p>The lesson videos for this course will be shared by our team. If you haven't received access yet, please contact us.</p>
                    </div>
                @endif

                @if($course->modules->isEmpty())
                    <p class="text-muted">The curriculum will be published soon.</p>
                @endif

                <div class="accordion curriculum" id="curriculumAcc">
                    @foreach($course->modules as $mi => $module)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $mi === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#mod{{ $module->id }}" aria-expanded="{{ $mi === 0 ? 'true' : 'false' }}">
                                    <span class="curriculum__index">{{ str_pad($mi + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="curriculum__title">{{ $module->title }}</span>
                                    <span class="curriculum__count">{{ $module->lessons->count() }} lessons</span>
                                </button>
                            </h2>
                            <div id="mod{{ $module->id }}" class="accordion-collapse collapse {{ $mi === 0 ? 'show' : '' }}">
                                <ul class="lesson-list">
                                    @foreach($module->lessons as $lesson)
                                        @php($vid = $lesson->videoId())
                                        <li class="{{ $vid ? 'is-playable' : 'is-locked' }}">
                                            @if($vid)
                                                <button type="button" class="lesson" data-video="{{ $vid }}" data-video-title="{{ $lesson->title }}">
                                                    <i class="bi bi-play-circle-fill"></i>
                                                    <span class="lesson__title">{{ $lesson->title }}</span>
                                                    <span class="lesson__time">{{ $lesson->duration }}</span>
                                                </button>
                                            @else
                                                <div class="lesson">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                    <span class="lesson__title">{{ $lesson->title }}</span>
                                                    <span class="lesson__time">{{ $lesson->duration }}</span>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card sticky-lg-top">
                    <span class="my-course__status is-ok"><i class="bi bi-check-circle-fill"></i> Enrolled</span>
                    <h2 class="panel__title mt-2">{{ $course->title }}</h2>
                    <p class="text-muted small">Click any lesson to watch it. Need help with this course? Contact our support team.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ $course->url() }}" class="btn btn-soft"><i class="bi bi-info-circle"></i> Course details</a>
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to My Courses</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
