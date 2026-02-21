@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'SecureVault' || trim($slot) === 'Laravel')
{{-- SecureVault inline SVG logo for email --}}
<div style="display: inline-block; text-align: center;">
    <div style="display: inline-block; width: 48px; height: 48px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px; padding: 8px; margin-bottom: 8px;">
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="width: 32px; height: 32px;">
            <path d="M16 4c-3.7 0-6.7 2.3-6.7 4.7V12c-.7 0-1.3.6-1.3 1.3v8c0 .7.6 1.3 1.3 1.3h13.4c.7 0 1.3-.6 1.3-1.3v-8c0-.7-.6-1.3-1.3-1.3V8.7C22.7 6.3 19.7 4 16 4zm4 8h-8V8.7c0-1.5 1.8-2.7 4-2.7s4 1.2 4 2.7V12zm-4 2.7a2 2 0 0 1 1 3.7v1.6a1 1 0 1 1-2 0v-1.6a2 2 0 0 1 1-3.7z" fill="white" fill-opacity="0.95"/>
        </svg>
    </div>
    <div style="font-size: 20px; font-weight: 700; color: #1d4ed8; letter-spacing: -0.5px;">SecureVault</div>
</div>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
