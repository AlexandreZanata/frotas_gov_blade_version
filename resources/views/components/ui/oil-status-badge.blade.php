@props(['status'])

@php
    // Define as classes, texto e ícone padrão
    $classes = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    $text = 'N/A';
    $icon = 'question-mark-circle'; // Ícone padrão caso o status não seja reconhecido

    // Define as propriedades com base no status recebido
    switch ($status) {
        case 'em_dia':
            $classes .= ' bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300';
            $text = 'Em Dia';
            $icon = 'check-circle';
            break;
        case 'atencao':
            $classes .= ' bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-300';
            $text = 'Atenção';
            $icon = 'alert-circle';
            break;
        case 'critico':
            // Usando uma cor laranja personalizada para 'crítico'
            $classes .= ' bg-orange-100 text-orange-800 dark:bg-orange-800/20 dark:text-orange-300';
            $text = 'Crítico';
            $icon = 'alert-triangle';
            break;
        case 'vencido':
            $classes .= ' bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300';
            $text = 'Vencido';
            $icon = 'x-circle';
            break;
        case 'sem_registro':
            $classes .= ' bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-300';
            $text = 'Sem Registro';
            $icon = 'file-minus';
            break;
    }
@endphp

{{-- Renderiza o badge com as classes e o texto dinâmicos --}}
<span {{ $attributes->merge(['class' => $classes]) }}>
    <x-icon :name="$icon" class="w-3 h-3 mr-1.5" />
    {{ $text }}
</span>
