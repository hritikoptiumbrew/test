@component('mail::message')
# {{ $title }}

{{ $msg }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
