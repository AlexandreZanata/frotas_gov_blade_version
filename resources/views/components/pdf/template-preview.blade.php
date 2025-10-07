@props([
    'formData' => null,
    'template' => null,
])

<div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-navy-900 dark:to-navy-800 p-6 rounded-xl shadow-inner overflow-hidden">
    <div class="flex items-center justify-center mb-4">
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-navy-300">
            <x-icon name="eye" class="w-4 h-4" />
            <span class="font-medium">Visualização em Tempo Real</span>
        </div>
    </div>

    <!-- Container com scroll para evitar vazamento -->
    <div class="overflow-auto max-h-[calc(100vh-200px)] rounded-lg shadow-2xl">
        <div class="flex items-center justify-center p-4 min-h-[600px]">
            <!-- Página A4 em escala -->
            <div class="bg-white shadow-2xl"
                 style="width: 210mm; min-height: 297mm; transform: scale(0.4); transform-origin: top center; margin-bottom: -400px;">

                <!-- Header -->
                <div x-show="formData?.header_text || formData?.header_image_preview"
                     class="px-8 pt-8 pb-4 border-b-2 border-gray-300"
                     :style="`text-align: ${formData?.header_text_align === 'C' ? 'center' : formData?.header_text_align === 'R' ? 'right' : 'left'};`">
                    <div x-show="formData?.header_image_preview" class="mb-3">
                        <img :src="formData?.header_image_preview"
                             alt="Header"
                             class="max-h-24"
                             :class="formData?.header_text_align === 'C' ? 'mx-auto' : formData?.header_text_align === 'R' ? 'ml-auto' : ''" />
                    </div>
                    <p x-show="formData?.header_text"
                       x-text="formData?.header_text"
                       class="text-gray-800 font-medium leading-relaxed"
                       :style="`font-size: ${formData?.header_font_size || 12}px;`"></p>
                </div>

                <!-- Body Content -->
                <div class="px-8 py-8"
                     :style="`padding-top: ${formData?.margin_top || 10}mm; padding-bottom: ${formData?.margin_bottom || 10}mm; padding-left: ${formData?.margin_left || 10}mm; padding-right: ${formData?.margin_right || 10}mm;`">

                    <!-- Title -->
                    <h1 class="font-bold text-gray-900 mb-6"
                        :style="`font-size: ${formData?.font_size_title || 16}px; font-family: ${formData?.font_family || 'helvetica'};`"
                        x-text="formData?.name || 'Título do Documento'"></h1>

                    <!-- Body Text -->
                    <div x-show="formData?.body_text" class="mb-6">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"
                           :style="`font-size: ${formData?.font_size_text || 12}px;`"
                           x-text="formData?.body_text || 'Texto do corpo do documento aparecerá aqui...'"></p>
                    </div>

                    <!-- Sample Table -->
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-800 mb-3">Exemplo de Tabela</h2>
                        <table class="w-full border-collapse"
                               :class="formData?.show_table_lines ? 'border-2 border-gray-400' : ''">
                            <thead>
                                <tr :style="`background-color: ${formData?.table_header_bg || '#f3f4f6'};`">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700"
                                        :class="formData?.show_table_lines ? 'border border-gray-400' : ''">Item</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700"
                                        :class="formData?.show_table_lines ? 'border border-gray-400' : ''">Descrição</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700"
                                        :class="formData?.show_table_lines ? 'border border-gray-400' : ''">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="i in 5" :key="i">
                                    <tr :class="formData?.use_zebra_stripes && i % 2 === 0 ? 'bg-gray-50' : 'bg-white'">
                                        <td class="px-4 py-2 text-gray-600"
                                            :class="formData?.show_table_lines ? 'border border-gray-300' : ''"
                                            x-text="`Item ${i}`"></td>
                                        <td class="px-4 py-2 text-gray-600"
                                            :class="formData?.show_table_lines ? 'border border-gray-300' : ''"
                                            x-text="`Descrição do item ${i}`"></td>
                                        <td class="px-4 py-2 text-gray-600"
                                            :class="formData?.show_table_lines ? 'border border-gray-300' : ''"
                                            x-text="`R$ ${(i * 100).toFixed(2)}`"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div x-show="formData?.footer_text || formData?.footer_image_preview"
                     class="px-8 pt-4 pb-8 border-t-2 border-gray-300"
                     :style="`text-align: ${formData?.footer_text_align === 'C' ? 'center' : formData?.footer_text_align === 'R' ? 'right' : 'left'};`">
                    <p x-show="formData?.footer_text"
                       x-text="formData?.footer_text"
                       class="text-gray-600 text-sm mb-2"
                       :style="`font-size: ${formData?.footer_font_size || 10}px;`"></p>
                    <div x-show="formData?.footer_image_preview" class="mt-2">
                        <img :src="formData?.footer_image_preview"
                             alt="Footer"
                             class="max-h-16"
                             :class="formData?.footer_text_align === 'C' ? 'mx-auto' : formData?.footer_text_align === 'R' ? 'ml-auto' : ''" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Badge -->
    <div class="mt-4 flex items-center justify-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full text-xs">
            <x-icon name="information-circle" class="w-4 h-4" />
            <span>Preview em escala reduzida (40%)</span>
        </div>
    </div>
</div>
