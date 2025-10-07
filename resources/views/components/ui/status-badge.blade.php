@props(['status'])

@php
    $name = $status?->name ?? 'Indefinido';
    $statusName = mb_strtolower($name, 'UTF-8');

    // NOVA PALETA DE CORES
    $colorClasses = match ($statusName) {
        'disponível' => 'bg-green-100 text-green-800 dark:bg-green-500 dark:text-white', // Verde Vivo
        'em manutenção' => 'bg-blue-100 text-blue-800 dark:bg-blue-500 dark:text-white', // Azul
        'em uso' => 'bg-purple-100 text-purple-800 dark:bg-purple-500 dark:text-white', // Roxo
        'em ocorrência', 'avariado', 'bloqueado' => 'bg-rose-100 text-rose-800 dark:bg-rose-500 dark:text-white', // Bloqueado (mantido)
        'inativo', 'vendido', 'baixado' => 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-100', // Neutro
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200', // Padrão
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold ' . $colorClasses]) }}>
    {{ $name }}
</span>
