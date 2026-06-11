<x-mail::message>
# Tin nhắn liên hệ mới

**Họ tên:** {{ $message->name }}  
**Điện thoại:** {{ $message->phone }}  
@if ($message->email)
**Email:** {{ $message->email }}  
@endif
@if ($message->subject)
**Chủ đề:** {{ $message->subject }}  
@endif

**Nội dung:**

{{ $message->message }}

<x-mail::button :url="$messageUrl">
Xem tin nhắn
</x-mail::button>

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
