@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# @lang('Ops...!')
@else
# @lang('')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
    {!! $line !!}
@endforeach

{{-- Action Button --}}
@isset($actionText)

<div style="width: 35%; float: left; text-align: right;">
    @component('mail::button', ['url' => $url_yes, 'color' => 'green'])
    {{ $text_yes }}
    @endcomponent
</div>
<div style="width: 35%; float: left; text-align: left;">
    @component('mail::button', ['url' => $url_not, 'color' => 'red'])
    {{ $text_not }}
    @endcomponent
</div>
<div style="clear: both;"></div>

@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{!! $line !!}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Saudações'),<br>{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)

<strong style="color: red; font-size:11px; text-align: center;">@lang("Esta é uma mensagem automática, favor não responder este e-mail.")</strong>
@endisset
@endcomponent