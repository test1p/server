@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# こんにちは。
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# こんにちは。
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
「{{ $actionText }}ボタン」がうまく機能しない場合、以下のURLをコピー＆ペーストして直接アクセスしてください。
 [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent

@endisset
@endcomponent