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
    @case('shield')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l8 3v6c0 5-3.5 9-8 11-4.5-2-8-6-8-11V5l8-3z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
        </svg>
        @break
    @case('building')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        @break
    @case('search')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        @break
    @case('map-pin')
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        @break
    @case('swap')
        <!-- Ícone de transferência/troca -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
        </svg>
        @break
    @case('clock')
        <!-- Ícone de relógio/pendente -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
        </svg>
        @break
    @case('x')
        <!-- Ícone de X/fechar/rejeitar -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        @break
    @case('check-circle')
        <!-- Ícone de check em círculo -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        @break
    @case('x-circle')
        <!-- Ícone de X em círculo -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        @break
    @case('calendar')
        <!-- Ícone de calendário -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        @break
    @case('arrow-right')
        <!-- Ícone de seta para direita -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        @break
    @case('refresh')
        <!-- Ícone de atualizar/devolver -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        @break
    @case('info')
        <!-- Ícone de informação -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        @break
    @case('alert')
        <!-- Ícone de alerta/problema -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        @break
    @case('exclamation')
        <!-- Ícone de exclamação/atenção -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        @break
    @case('bell')
        <!-- Ícone de notificação/sino -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @break
    @case('clipboard-check')
        <!-- Ícone de checklist -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        @break
    @case('filter')
        <!-- Ícone de filtro -->
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
        @break
    @default
        <svg {{ $attributes->merge(['class'=>$classes]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
@endswitch
