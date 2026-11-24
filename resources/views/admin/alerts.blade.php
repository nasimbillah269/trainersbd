{{-- Success / error / info flashes are shown as toasts by the admin layout. This partial only lists validation errors. --}}
@if($errors->any())
    <div class="adm-alert adm-alert--danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div>
            <strong>Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
