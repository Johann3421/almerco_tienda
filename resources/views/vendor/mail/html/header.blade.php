@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
{{-- <img src="{{ asset('assets/img/favicon.png') }}" class="logo" alt="{{ $slot }} Logo"> --}}
<img src="https://grupoalmerco.com.pe/favicon.png" class="logo" alt="{{ $slot }} Logo">
@endif
</a>
</td>
</tr>
