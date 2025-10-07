@props([
    'name',
    'class' => 'w-5 h-5',
])
@php($classes = $class . ' inline-block')
@switch($name)
    @case('dashboard')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        @break
    @case('car')
        <!-- Ícone de carro estilizado -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13l1.2-3.6A2 2 0 016.06 8h11.88a2 2 0 011.86 1.4L21 13v5a1 1 0 01-1 1h-1.2a2.8 2.8 0 01-5.6 0H10.8a2.8 2.8 0 01-5.6 0H4a1 1 0 01-1-1v-5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 13h10M6.5 16.5h.01M17.5 16.5h.01" />
        </svg>
        @break
    @case('chevron-down')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        @break
    @case('chevron-left')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        @break
    @case('chevron-right')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        @break
    @case('sun')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m0 14v1m8-8h-1M5 12H4m13.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
        @break
    @case('moon')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
        @break
    @case('menu')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        @break
    @case('close')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        @break
    @case('plus')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        @break
    @case('category')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM16 13l5 5-5 5-5-5 5-5z"/></svg>
        @break
    @case('prefix')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h9l5 5v11a1 1 0 01-1 1H5a1 1 0 01-1-1V5c0-.552.448-1 1-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6M9 9h2"/></svg>
        @break
    @case('list')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        @break
    @case('eye')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
        @break
    @case('edit')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M4 20h4.586a1 1 0 00.707-.293l9.586-9.586a2 2 0 000-2.828l-2.172-2.172a2 2 0 00-2.828 0L4.293 14.121A1 1 0 004 14.828V20z"/></svg>
        @break
    @case('trash')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V4h6v3m2 0v13a1 1 0 01-1 1H8a1 1 0 01-1-1V7h12z"/></svg>
        @break
    @default
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
@endswitch
