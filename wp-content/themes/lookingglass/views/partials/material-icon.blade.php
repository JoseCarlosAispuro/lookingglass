@php
    $name = $name ?? '';
    $class = $class ?? '';
@endphp
@if($name)
    <span class="material-symbols-outlined {{ $class }}" aria-hidden="true">{{ $name }}</span>
@endif
@php
    unset($name);
    unset($class);
@endphp
