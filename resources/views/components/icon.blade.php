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
    @case('arrow-left')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        @break
    @case('save')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h11l3 3v13a1 1 0 01-1 1H5a1 1 0 01-1-1V5c0-.552.448-1 1-1z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 4v6h6V4M9 14h6"/></svg>
        @break
    @case('document')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        @break
    @case('template')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h3m-3 4h6m-6 4h6"/></svg>
        @break
    @case('users')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        @break
    @case('clipboard')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5h4m-2 2V3"/>
        </svg>
        @break
    @case('route')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7l4-4m0 0l4 4m-4-4v16m0 0l-4-4m4 4l4-4"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7l4-4m0 0l4 4m-4-4v16"/>
        </svg>
        @break
    @case('key')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
        @break
    @case('fuel')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-2-2M8 21H6a2 2 0 01-2-2V7a2 2 0 012-2h2m4 0h2a2 2 0 012 2v10m0 0a2 2 0 002 2h2"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 11h4m-4 4h4"/>
        </svg>
        @break
    @case('play-circle')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 8l6 4-6 4V8z"/>
        </svg>
        @break
    @case('play')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z"/>
        </svg>
        @break
    @case('check')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        @break
    @default
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
@endswitch
