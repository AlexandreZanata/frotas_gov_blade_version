<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Modelo de PDF" subtitle="Atualizar template personalizado" hide-title-mobile icon="template" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('pdf-templates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-navy-100 text-sm font-medium">
            <x-icon name="arrow-left" class="w-4 h-4" /> <span>Voltar</span>
        </a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="pdfTemplateEditor()">
        <!-- Formulário -->
        <div class="space-y-6">
            <x-ui.card title="Informações Básicas">
                <form @submit.prevent="submitForm" id="templateForm" method="POST" action="{{ route('pdf-templates.update', $pdfTemplate) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Nome do Template *</label>
                            <input type="text" name="name" x-model="formData.name" @input="updatePreview" required
                                   value="{{ old('name', $pdfTemplate->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 focus:ring-2 focus:ring-primary-500">
                        </div>

                        <!-- Tabs de Configuração -->
                        <div x-data="{ activeTab: 'header' }" class="mt-6">
                            <!-- Tab Navigation -->
                            <div class="flex border-b border-gray-200 dark:border-navy-600 overflow-x-auto">
                                <button type="button" @click="activeTab = 'header'" :class="activeTab === 'header' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap">Cabeçalho</button>
                                <button type="button" @click="activeTab = 'footer'" :class="activeTab === 'footer' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap">Rodapé</button>
                                <button type="button" @click="activeTab = 'body'" :class="activeTab === 'body' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap">Corpo</button>
                                <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap">Tabela</button>
                                <button type="button" @click="activeTab = 'style'" :class="activeTab === 'style' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap">Estilo</button>
                            </div>

                            <!-- Tab Content -->
                            <div class="mt-4 space-y-4">
                                <!-- Header Tab -->
                                <div x-show="activeTab === 'header'" class="space-y-3">
                                    @if($pdfTemplate->header_image)
                                        <div class="mb-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Imagem Atual</label>
                                            <img src="{{ Storage::url($pdfTemplate->header_image) }}" alt="Header" class="max-h-20 border rounded">
                                        </div>
                                    @endif
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Imagem do Cabeçalho {{ $pdfTemplate->header_image ? '(trocar)' : '' }}</label>
                                        <input type="file" name="header_image" accept="image/*" @change="handleImageUpload($event, 'header')"
                                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Texto do Cabeçalho</label>
                                        <textarea name="header_text" x-model="formData.header_text" @input="updatePreview" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">{{ old('header_text', $pdfTemplate->header_text) }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Alinhamento</label>
                                            <select name="header_text_align" x-model="formData.header_text_align" @change="updatePreview"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                                <option value="L" {{ old('header_text_align', $pdfTemplate->header_text_align) == 'L' ? 'selected' : '' }}>Esquerda</option>
                                                <option value="C" {{ old('header_text_align', $pdfTemplate->header_text_align) == 'C' ? 'selected' : '' }}>Centro</option>
                                                <option value="R" {{ old('header_text_align', $pdfTemplate->header_text_align) == 'R' ? 'selected' : '' }}>Direita</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Tamanho da Fonte</label>
                                            <input type="number" name="header_font_size" x-model="formData.header_font_size" @input="updatePreview" min="8" max="24"
                                                   value="{{ old('header_font_size', $pdfTemplate->header_font_size ?? 12) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer Tab -->
                                <div x-show="activeTab === 'footer'" class="space-y-3">
                                    @if($pdfTemplate->footer_image)
                                        <div class="mb-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Imagem Atual</label>
                                            <img src="{{ Storage::url($pdfTemplate->footer_image) }}" alt="Footer" class="max-h-16 border rounded">
                                        </div>
                                    @endif
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Imagem do Rodapé {{ $pdfTemplate->footer_image ? '(trocar)' : '' }}</label>
                                        <input type="file" name="footer_image" accept="image/*" @change="handleImageUpload($event, 'footer')"
                                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Texto do Rodapé</label>
                                        <textarea name="footer_text" x-model="formData.footer_text" @input="updatePreview" rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">{{ old('footer_text', $pdfTemplate->footer_text) }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Alinhamento</label>
                                            <select name="footer_text_align" x-model="formData.footer_text_align" @change="updatePreview"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                                <option value="L" {{ old('footer_text_align', $pdfTemplate->footer_text_align) == 'L' ? 'selected' : '' }}>Esquerda</option>
                                                <option value="C" {{ old('footer_text_align', $pdfTemplate->footer_text_align) == 'C' ? 'selected' : '' }}>Centro</option>
                                                <option value="R" {{ old('footer_text_align', $pdfTemplate->footer_text_align) == 'R' ? 'selected' : '' }}>Direita</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Tamanho da Fonte</label>
                                            <input type="number" name="footer_font_size" x-model="formData.footer_font_size" @input="updatePreview" min="6" max="16"
                                                   value="{{ old('footer_font_size', $pdfTemplate->footer_font_size ?? 10) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Body Tab -->
                                <div x-show="activeTab === 'body'" class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Texto do Corpo</label>
                                        <textarea name="body_text" x-model="formData.body_text" @input="updatePreview" rows="5"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">{{ old('body_text', $pdfTemplate->body_text) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Tamanho da Fonte</label>
                                        <input type="number" name="font_size_text" x-model="formData.font_size_text" @input="updatePreview" min="8" max="18"
                                               value="{{ old('font_size_text', $pdfTemplate->font_size_text ?? 12) }}"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                    </div>
                                </div>

                                <!-- Table Tab -->
                                <div x-show="activeTab === 'table'" class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Estilo da Tabela</label>
                                        <select name="table_style" x-model="formData.table_style" @change="updatePreview"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                            <option value="grid" {{ old('table_style', $pdfTemplate->table_style) == 'grid' ? 'selected' : '' }}>Grade Completa</option>
                                            <option value="simple" {{ old('table_style', $pdfTemplate->table_style) == 'simple' ? 'selected' : '' }}>Simples</option>
                                            <option value="minimal" {{ old('table_style', $pdfTemplate->table_style) == 'minimal' ? 'selected' : '' }}>Minimalista</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="show_table_lines" x-model="formData.show_table_lines" @change="updatePreview"
                                                   {{ old('show_table_lines', $pdfTemplate->show_table_lines) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded">
                                            <span class="text-sm text-gray-700 dark:text-navy-200">Mostrar Linhas</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="use_zebra_stripes" x-model="formData.use_zebra_stripes" @change="updatePreview"
                                                   {{ old('use_zebra_stripes', $pdfTemplate->use_zebra_stripes) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded">
                                            <span class="text-sm text-gray-700 dark:text-navy-200">Linhas Alternadas</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Style Tab -->
                                <div x-show="activeTab === 'style'" class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Família da Fonte</label>
                                            <select name="font_family" x-model="formData.font_family" @change="updatePreview"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                                <option value="helvetica" {{ old('font_family', $pdfTemplate->font_family) == 'helvetica' ? 'selected' : '' }}>Helvetica</option>
                                                <option value="times" {{ old('font_family', $pdfTemplate->font_family) == 'times' ? 'selected' : '' }}>Times</option>
                                                <option value="courier" {{ old('font_family', $pdfTemplate->font_family) == 'courier' ? 'selected' : '' }}>Courier</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Tamanho Título</label>
                                            <input type="number" name="font_size_title" x-model="formData.font_size_title" @input="updatePreview" min="12" max="24"
                                                   value="{{ old('font_size_title', $pdfTemplate->font_size_title ?? 16) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Margem Superior</label>
                                            <input type="number" name="margin_top" x-model="formData.margin_top" @input="updatePreview" min="5" max="30"
                                                   value="{{ old('margin_top', $pdfTemplate->margin_top ?? 10) }}"
                                                   class="w-full px-2 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Margem Inferior</label>
                                            <input type="number" name="margin_bottom" x-model="formData.margin_bottom" @input="updatePreview" min="5" max="30"
                                                   value="{{ old('margin_bottom', $pdfTemplate->margin_bottom ?? 10) }}"
                                                   class="w-full px-2 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Margem Esquerda</label>
                                            <input type="number" name="margin_left" x-model="formData.margin_left" @input="updatePreview" min="5" max="30"
                                                   value="{{ old('margin_left', $pdfTemplate->margin_left ?? 10) }}"
                                                   class="w-full px-2 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Margem Direita</label>
                                            <input type="number" name="margin_right" x-model="formData.margin_right" @input="updatePreview" min="5" max="30"
                                                   value="{{ old('margin_right', $pdfTemplate->margin_right ?? 10) }}"
                                                   class="w-full px-2 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-600">
                            <a href="{{ route('pdf-templates.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 hover:bg-gray-100 dark:hover:bg-navy-700 rounded-md transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md transition">
                                Atualizar Template
                            </button>
                        </div>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <!-- Preview em Tempo Real -->
        <div class="lg:sticky lg:top-4 h-fit">
            <x-ui.card title="Preview do PDF">
                <div class="bg-gray-100 dark:bg-navy-900 p-4 rounded-lg min-h-[600px]">
                    <div class="bg-white dark:bg-navy-800 shadow-lg mx-auto" style="width: 210mm; min-height: 297mm; transform: scale(0.5); transform-origin: top center;">
                        <!-- Header Preview -->
                        <div x-show="formData.header_text || formData.header_image" class="p-8 border-b-2 border-gray-200" :style="`text-align: ${formData.header_text_align === 'C' ? 'center' : formData.header_text_align === 'R' ? 'right' : 'left'}; font-size: ${formData.header_font_size}px`">
                            <div x-show="formData.header_image_preview" class="mb-2">
                                <img :src="formData.header_image_preview" class="max-h-20 mx-auto" />
                            </div>
                            <p x-text="formData.header_text" class="text-gray-800 dark:text-navy-100"></p>
                        </div>

                        <!-- Body Preview -->
                        <div class="p-8" :style="`margin: ${formData.margin_top}px ${formData.margin_right}px ${formData.margin_bottom}px ${formData.margin_left}px`">
                            <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-navy-50" :style="`font-size: ${formData.font_size_title}px; font-family: ${formData.font_family}`" x-text="formData.name || 'Título do Documento'"></h1>
                            <p class="text-gray-700 dark:text-navy-200 mb-4" :style="`font-size: ${formData.font_size_text}px`" x-text="formData.body_text || 'Texto do corpo do documento aparecerá aqui...'"></p>

                            <!-- Table Preview -->
                            <table class="w-full border-collapse" :class="formData.show_table_lines ? 'border border-gray-300' : ''">
                                <thead>
                                    <tr :style="`background-color: ${formData.table_header_bg || '#f3f4f6'}`">
                                        <th class="px-4 py-2 text-left" :class="formData.show_table_lines ? 'border border-gray-300' : ''">Coluna 1</th>
                                        <th class="px-4 py-2 text-left" :class="formData.show_table_lines ? 'border border-gray-300' : ''">Coluna 2</th>
                                        <th class="px-4 py-2 text-left" :class="formData.show_table_lines ? 'border border-gray-300' : ''">Coluna 3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="i in 3" :key="i">
                                        <tr :class="formData.use_zebra_stripes && i % 2 === 0 ? 'bg-gray-50' : ''">
                                            <td class="px-4 py-2" :class="formData.show_table_lines ? 'border border-gray-300' : ''" x-text="`Dado ${i}-1`"></td>
                                            <td class="px-4 py-2" :class="formData.show_table_lines ? 'border border-gray-300' : ''" x-text="`Dado ${i}-2`"></td>
                                            <td class="px-4 py-2" :class="formData.show_table_lines ? 'border border-gray-300' : ''" x-text="`Dado ${i}-3`"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Preview -->
                        <div x-show="formData.footer_text || formData.footer_image" class="p-4 border-t-2 border-gray-200 text-sm" :style="`text-align: ${formData.footer_text_align === 'C' ? 'center' : formData.footer_text_align === 'R' ? 'right' : 'left'}; font-size: ${formData.footer_font_size}px`">
                            <p x-text="formData.footer_text" class="text-gray-600 dark:text-navy-300"></p>
                            <div x-show="formData.footer_image_preview" class="mt-2">
                                <img :src="formData.footer_image_preview" class="max-h-16 mx-auto" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    @push('scripts')
    <script>
        function pdfTemplateEditor() {
            return {
                formData: {
                    name: '{{ old('name', $pdfTemplate->name) }}',
                    header_text: '{{ old('header_text', $pdfTemplate->header_text) }}',
                    header_text_align: '{{ old('header_text_align', $pdfTemplate->header_text_align ?? 'C') }}',
                    header_font_size: {{ old('header_font_size', $pdfTemplate->header_font_size ?? 12) }},
                    header_image_preview: null,
                    footer_text: '{{ old('footer_text', $pdfTemplate->footer_text) }}',
                    footer_text_align: '{{ old('footer_text_align', $pdfTemplate->footer_text_align ?? 'C') }}',
                    footer_font_size: {{ old('footer_font_size', $pdfTemplate->footer_font_size ?? 10) }},
                    footer_image_preview: null,
                    body_text: '{{ old('body_text', $pdfTemplate->body_text) }}',
                    font_size_text: {{ old('font_size_text', $pdfTemplate->font_size_text ?? 12) }},
                    font_size_title: {{ old('font_size_title', $pdfTemplate->font_size_title ?? 16) }},
                    font_family: '{{ old('font_family', $pdfTemplate->font_family ?? 'helvetica') }}',
                    table_style: '{{ old('table_style', $pdfTemplate->table_style ?? 'grid') }}',
                    table_header_bg: '{{ old('table_header_bg', $pdfTemplate->table_header_bg ?? '#f3f4f6') }}',
                    show_table_lines: {{ old('show_table_lines', $pdfTemplate->show_table_lines) ? 'true' : 'false' }},
                    use_zebra_stripes: {{ old('use_zebra_stripes', $pdfTemplate->use_zebra_stripes) ? 'true' : 'false' }},
                    margin_top: {{ old('margin_top', $pdfTemplate->margin_top ?? 10) }},
                    margin_bottom: {{ old('margin_bottom', $pdfTemplate->margin_bottom ?? 10) }},
                    margin_left: {{ old('margin_left', $pdfTemplate->margin_left ?? 10) }},
                    margin_right: {{ old('margin_right', $pdfTemplate->margin_right ?? 10) }}
                },
                debounceTimer: null,

                updatePreview() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        console.log('Preview atualizado');
                    }, 300);
                },

                handleImageUpload(event, type) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.formData[`${type}_image_preview`] = e.target.result;
                            this.updatePreview();
                        };
                        reader.readAsDataURL(file);
                    }
                },

                submitForm() {
                    document.getElementById('templateForm').submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

