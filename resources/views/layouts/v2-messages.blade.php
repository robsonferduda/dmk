@php
    $iconesV2 = ['success' => 'bi-check-circle', 'danger' => 'bi-x-circle', 'warning' => 'bi-exclamation-triangle', 'info' => 'bi-info-circle'];
@endphp

@if(\Session::has('flash_notification'))
    @foreach (Session::get('flash_notification') as $message)
        <div class="alert alert-{{ $message['level'] }} alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
            <i class="bi {{ $iconesV2[$message['level']] ?? 'bi-info-circle' }} mt-1"></i>
            <div>{!! $message['message'] !!}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endforeach
@endif
{{ Session::forget('flash_notification') }}

@if ($errors->any())
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle me-1"></i> Atenção!</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{!! $error !!}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif
