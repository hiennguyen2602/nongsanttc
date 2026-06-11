@php
    $messengerIconPath = public_path('images/icons/logo-messenger.png');
    $messengerIconVersion = file_exists($messengerIconPath) ? filemtime($messengerIconPath) : 1;
@endphp
<img
    src="{{ asset('images/icons/logo-messenger.png') }}?v={{ $messengerIconVersion }}"
    alt=""
    class="{{ $iconClass ?? 'block h-full w-full object-cover' }}"
    width="36"
    height="36"
    aria-hidden="true"
    decoding="async"
>
