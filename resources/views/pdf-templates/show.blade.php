<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Visualizar Modelo de PDF" subtitle="Detalhes do template" hide-title-mobile icon="template" />
    </x-slot>
    <x-slot name="pageActions">
        <div class="flex items-center gap-2">
            <a href="{{ route('pdf-templates.edit', $pdfTemplate) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium">
                <x-icon name="edit" class="w-4 h-4" /> <span>Editar</span>
            </a>
            <a href="{{ route('pdf-templates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-navy-100 text-sm font-medium">
                <x-icon name="arrow-left" class="w-4 h-4" /> <span>Voltar</span>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informações do Template -->
        <div class="space-y-6">
            <x-ui.card title="Informações Básicas">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Nome do Template</label>
                        <p class="text-base font-semibold text-gray-900 dark:text-navy-100">{{ $pdfTemplate->name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Criado em</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Atualizado em</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Configurações de Cabeçalho">
                <div class="space-y-4">
                    @if($pdfTemplate->header_image)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-2">Imagem do Cabeçalho</label>
                            <img src="{{ Storage::url($pdfTemplate->header_image) }}" alt="Header" class="max-h-24 border rounded-lg shadow-sm">
                        </div>
                    @endif
                    @if($pdfTemplate->header_text)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Texto do Cabeçalho</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100 whitespace-pre-wrap bg-gray-50 dark:bg-navy-700 p-3 rounded-lg">{{ $pdfTemplate->header_text }}</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Alinhamento</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">
                                @switch($pdfTemplate->header_text_align)
                                    @case('L') Esquerda @break
                                    @case('C') Centro @break
                                    @case('R') Direita @break
                                    @default Centro
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Tamanho da Fonte</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->header_font_size ?? 12 }}px</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Configurações de Rodapé">
                <div class="space-y-4">
                    @if($pdfTemplate->footer_image)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-2">Imagem do Rodapé</label>
                            <img src="{{ Storage::url($pdfTemplate->footer_image) }}" alt="Footer" class="max-h-20 border rounded-lg shadow-sm">
                        </div>
                    @endif
                    @if($pdfTemplate->footer_text)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Texto do Rodapé</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100 whitespace-pre-wrap bg-gray-50 dark:bg-navy-700 p-3 rounded-lg">{{ $pdfTemplate->footer_text }}</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Alinhamento</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">
                                @switch($pdfTemplate->footer_text_align)
                                    @case('L') Esquerda @break
                                    @case('C') Centro @break
                                    @case('R') Direita @break
                                    @default Centro
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Tamanho da Fonte</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->footer_font_size ?? 10 }}px</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Configurações de Corpo e Estilo">
                <div class="space-y-4">
                    @if($pdfTemplate->body_text)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Texto do Corpo</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100 whitespace-pre-wrap bg-gray-50 dark:bg-navy-700 p-3 rounded-lg">{{ $pdfTemplate->body_text }}</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Família da Fonte</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100 capitalize">{{ $pdfTemplate->font_family ?? 'Helvetica' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Tamanho Título</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->font_size_title ?? 16 }}px</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Tamanho Texto</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->font_size_text ?? 12 }}px</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Estilo da Tabela</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100 capitalize">{{ $pdfTemplate->table_style ?? 'Grid' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Margem Superior</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->margin_top ?? 10 }}mm</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Margem Inferior</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->margin_bottom ?? 10 }}mm</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Margem Esquerda</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->margin_left ?? 10 }}mm</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-navy-300 mb-1">Margem Direita</label>
                            <p class="text-sm text-gray-900 dark:text-navy-100">{{ $pdfTemplate->margin_right ?? 10 }}mm</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 pt-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-navy-300">Mostrar Linhas:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pdfTemplate->show_table_lines ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $pdfTemplate->show_table_lines ? 'Sim' : 'Não' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-navy-300">Linhas Alternadas:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pdfTemplate->use_zebra_stripes ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $pdfTemplate->use_zebra_stripes ? 'Sim' : 'Não' }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Preview do Template -->
        <div class="lg:sticky lg:top-4 h-fit">
            <x-ui.card title="Preview do Template">
                <div class="bg-gray-100 dark:bg-navy-900 p-4 rounded-lg">
                    <div class="bg-white dark:bg-navy-800 shadow-lg mx-auto overflow-auto" style="width: 210mm; min-height: 297mm; transform: scale(0.5); transform-origin: top center;">
                        <!-- Header Preview -->
                        @if($pdfTemplate->header_text || $pdfTemplate->header_image)
                            <div class="p-8 border-b-2 border-gray-200" style="text-align: {{ $pdfTemplate->header_text_align === 'C' ? 'center' : ($pdfTemplate->header_text_align === 'R' ? 'right' : 'left') }}; font-size: {{ $pdfTemplate->header_font_size ?? 12 }}px">
                                @if($pdfTemplate->header_image)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($pdfTemplate->header_image) }}" class="max-h-20 mx-auto" />
                                    </div>
                                @endif
                                @if($pdfTemplate->header_text)
                                    <p class="text-gray-800 dark:text-navy-100 whitespace-pre-wrap">{{ $pdfTemplate->header_text }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Body Preview -->
                        <div class="p-8" style="margin: {{ $pdfTemplate->margin_top ?? 10 }}px {{ $pdfTemplate->margin_right ?? 10 }}px {{ $pdfTemplate->margin_bottom ?? 10 }}px {{ $pdfTemplate->margin_left ?? 10 }}px">
                            <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-navy-50" style="font-size: {{ $pdfTemplate->font_size_title ?? 16 }}px; font-family: {{ $pdfTemplate->font_family ?? 'helvetica' }}">
                                {{ $pdfTemplate->name }}
                            </h1>
                            @if($pdfTemplate->body_text)
                                <p class="text-gray-700 dark:text-navy-200 mb-4 whitespace-pre-wrap" style="font-size: {{ $pdfTemplate->font_size_text ?? 12 }}px">{{ $pdfTemplate->body_text }}</p>
                            @else
                                <p class="text-gray-500 dark:text-navy-300 mb-4 italic" style="font-size: {{ $pdfTemplate->font_size_text ?? 12 }}px">Texto do corpo do documento aparecerá aqui...</p>
                            @endif

                            <!-- Table Preview -->
                            <table class="w-full border-collapse {{ $pdfTemplate->show_table_lines ? 'border border-gray-300' : '' }}">
                                <thead>
                                    <tr style="background-color: {{ $pdfTemplate->table_header_bg ?? '#f3f4f6' }}">
                                        <th class="px-4 py-2 text-left {{ $pdfTemplate->show_table_lines ? 'border border-gray-300' : '' }}">Coluna 1</th>
                                        <th class="px-4 py-2 text-left {{ $pdfTemplate->show_table_lines ? 'border border-gray-300' : '' }}">Coluna 2</th>
                                        <th class="px-4 py-2 text-left {{ $pdfTemplate->show_table_lines ? 'border border-gray-300' : '' }}">Coluna 3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 1; $i <= 3; $i++)
                                        <tr class="{{ $pdfTemplate->use_zebra_stripes && $i % 2 === 0 ? 'bg-gray-50' : '' }}">
                                            <td class="px-4 py-2 {{ $pdfTemplate->show_table_lines ? 'border border-gray-300' : '' }}">Dado {{ $i }}-1</td>
                                            <td class="px-4 py-2 {{ $pdfTemplate->show_table_lines ? 'border border-gray-300' : '' }}">Dado {{ $i }}-2</td>
                                            <td class="px-4 py-2 {{ $pdfTemplate->show_table_lines ? 'border border-gray-300' : '' }}">Dado {{ $i }}-3</td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Preview -->
                        @if($pdfTemplate->footer_text || $pdfTemplate->footer_image)
                            <div class="p-4 border-t-2 border-gray-200 text-sm" style="text-align: {{ $pdfTemplate->footer_text_align === 'C' ? 'center' : ($pdfTemplate->footer_text_align === 'R' ? 'right' : 'left') }}; font-size: {{ $pdfTemplate->footer_font_size ?? 10 }}px">
                                @if($pdfTemplate->footer_text)
                                    <p class="text-gray-600 dark:text-navy-300 whitespace-pre-wrap">{{ $pdfTemplate->footer_text }}</p>
                                @endif
                                @if($pdfTemplate->footer_image)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($pdfTemplate->footer_image) }}" class="max-h-16 mx-auto" />
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>

