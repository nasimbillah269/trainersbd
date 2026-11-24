@extends('frontend.layouts.app')

@section('title', $code . ' | TrainersBD')

@section('content')
<section class="section">
    <div class="container">
        <div class="empty-state">
            <span class="empty-state__icon"><i class="bi {{ $icon ?? 'bi-exclamation-circle' }}"></i></span>
            <h2>{{ $code }} &middot; {{ $title }}</h2>
            <p>{{ $message }}</p>
            <a href="{{ route('index') }}" class="btn btn-primary btn-lg"><i class="bi bi-house"></i> Back to Home</a>
        </div>
    </div>
</section>
@endsection
