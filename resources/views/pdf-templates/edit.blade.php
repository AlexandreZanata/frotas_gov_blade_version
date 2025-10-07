<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Modelo de PDF" subtitle="Atualizar template personalizado" hide-title-mobile icon="template" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('pdf-templates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-navy-100 text-sm font-medium">
            <x-icon name="arrow-left" class="w-4 h-4" /> <span>Voltar</span>
        </a>
    </x-slot>

    <!-- Script ANTES do Alpine inicializar -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pdfTemplateEditor', () => ({
                formData: {
                    name: @json($pdfTemplate->name ?? ''),
                    header_text: @json($pdfTemplate->header_text ?? ''),
                    header_text_align: @json($pdfTemplate->header_text_align ?? 'C'),
                    header_font_size: {{ $pdfTemplate->header_font_size ?? 12 }},
                    header_image_preview: @json($pdfTemplate->header_image ? asset('storage/' . $pdfTemplate->header_image) : null),
                    footer_text: @json($pdfTemplate->footer_text ?? ''),
                    footer_text_align: @json($pdfTemplate->footer_text_align ?? 'C'),
                    footer_font_size: {{ $pdfTemplate->footer_font_size ?? 10 }},
                    footer_image_preview: @json($pdfTemplate->footer_image ? asset('storage/' . $pdfTemplate->footer_image) : null),
                    body_text: @json($pdfTemplate->body_text ?? ''),
                    font_size_text: {{ $pdfTemplate->font_size_text ?? 12 }},
                    font_size_title: {{ $pdfTemplate->font_size_title ?? 16 }},
                    font_family: @json($pdfTemplate->font_family ?? 'helvetica'),
                    table_style: @json($pdfTemplate->table_style ?? 'grid'),
                    table_header_bg: @json($pdfTemplate->table_header_bg ?? '#f3f4f6'),
                    show_table_lines: {{ $pdfTemplate->show_table_lines ? 'true' : 'false' }},
                    use_zebra_stripes: {{ $pdfTemplate->use_zebra_stripes ? 'true' : 'false' }},
                    margin_top: {{ $pdfTemplate->margin_top ?? 10 }},
                    margin_bottom: {{ $pdfTemplate->margin_bottom ?? 10 }},
                    margin_left: {{ $pdfTemplate->margin_left ?? 10 }},
                    margin_right: {{ $pdfTemplate->margin_right ?? 10 }}
                },
                debounceTimer: null,

                init() {
                    console.log('PDF Template Editor inicializado com dados existentes');
                    console.log('Dados carregados do banco:', this.formData);

                    // Forçar atualização do preview após carregar
                    this.$nextTick(() => {
                        this.updatePreview();
                    });
                },

                updatePreview() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        console.log('Preview atualizado em tempo real');
                    }, 100);
                },

                handleImageUpload(event, type) {
                    const file = event.target.files[0];
                    if (!file) {
                        console.warn('Nenhum arquivo selecionado');
                        return;
                    }

                    if (!file.type.startsWith('image/')) {
                        console.error('Arquivo não é uma imagem válida');
                        alert('Por favor, selecione apenas arquivos de imagem.');
                        return;
                    }

                    console.log(`Carregando imagem para ${type}:`, file.name);

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.formData[`${type}_image_preview`] = e.target.result;
                        console.log(`Imagem ${type} carregada com sucesso`);
                        this.updatePreview();
                    };
                    reader.onerror = (e) => {
                        console.error(`Erro ao carregar imagem ${type}:`, e);
                        alert('Erro ao carregar a imagem. Tente novamente.');
                    };
                    reader.readAsDataURL(file);
                },

                submitForm() {
                    console.log('Submetendo formulário de atualização...');
                    const form = document.getElementById('templateForm');

                    if (!form) {
                        console.error('Formulário não encontrado!');
                        return;
                    }

                    if (!this.formData.name || this.formData.name.trim() === '') {
                        alert('Por favor, preencha o nome do template.');
                        return;
                    }

                    console.log('Enviando formulário...');
                    form.submit();
                }
            }));
        });
    </script>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6" x-data="pdfTemplateEditor">
        <!-- Formulário - sem scroll interno -->
        <div class="space-y-6">
            <x-ui.card title="Informações Básicas">
                <form @submit.prevent="submitForm" id="templateForm" method="POST" action="{{ route('pdf-templates.update', $pdfTemplate) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">
                                Nome do Template *
                            </label>
                            <input type="text" name="name" x-model="formData.name" @input="updatePreview" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 focus:ring-2 focus:ring-primary-500">
                        </div>

                        <!-- Tabs de Configuração -->
                        <div x-data="{ activeTab: 'header' }" class="mt-6">
                            <!-- Tab Navigation -->
                            <div class="flex border-b border-gray-200 dark:border-navy-600 overflow-x-auto">
                                <button type="button" @click="activeTab = 'header'" :class="activeTab === 'header' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-navy-300'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition">
                                    <x-icon name="document-text" class="w-4 h-4 inline mr-1" /> Cabeçalho
                                </button>
                                <button type="button" @click="activeTab = 'footer'" :class="activeTab === 'footer' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-navy-300'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition">
                                    <x-icon name="document" class="w-4 h-4 inline mr-1" /> Rodapé
                                </button>
                                <button type="button" @click="activeTab = 'body'" :class="activeTab === 'body' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-navy-300'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition">
                                    <x-icon name="pencil" class="w-4 h-4 inline mr-1" /> Corpo
                                </button>
                                <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-navy-300'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition">
                                    <x-icon name="table" class="w-4 h-4 inline mr-1" /> Tabela
                                </button>
                                <button type="button" @click="activeTab = 'style'" :class="activeTab === 'style' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-navy-300'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition">
                                    <x-icon name="color-swatch" class="w-4 h-4 inline mr-1" /> Estilo
                                </button>
                            </div>

                            <!-- Tab Content -->
                            <div class="mt-4">
                                <!-- Header Tab -->
                                <div x-show="activeTab === 'header'" x-transition class="space-y-3">
                                    @if($pdfTemplate->header_image)
                                        <div class="mb-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <label class="block text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
                                                <x-icon name="photograph" class="w-4 h-4 inline mr-1" /> Imagem Atual
                                            </label>
                                            <img src="{{ Storage::url($pdfTemplate->header_image) }}" alt="Header" class="max-h-20 border rounded shadow-sm">
                                        </div>
                                    @endif
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Imagem do Cabeçalho {{ $pdfTemplate->header_image ? '(substituir)' : '' }}</label>
                                        <input type="file" name="header_image" accept="image/*" @change="handleImageUpload($event, 'header')"
                                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-navy-700 dark:file:text-primary-400">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Texto do Cabeçalho</label>
                                        <textarea name="header_text" x-model="formData.header_text" @input="updatePreview" rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100"></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Alinhamento</label>
                                            <select name="header_text_align" x-model="formData.header_text_align" @change="updatePreview"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                                <option value="L">Esquerda</option>
                                                <option value="C">Centro</option>
                                                <option value="R">Direita</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Tamanho da Fonte</label>
                                            <input type="number" name="header_font_size" x-model.number="formData.header_font_size" @input="updatePreview" min="8" max="24"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer Tab -->
                                <div x-show="activeTab === 'footer'" x-transition class="space-y-3">
                                    @if($pdfTemplate->footer_image)
                                        <div class="mb-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <label class="block text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
                                                <x-icon name="photograph" class="w-4 h-4 inline mr-1" /> Imagem Atual
                                            </label>
                                            <img src="{{ Storage::url($pdfTemplate->footer_image) }}" alt="Footer" class="max-h-16 border rounded shadow-sm">
                                        </div>
                                    @endif
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Imagem do Rodapé {{ $pdfTemplate->footer_image ? '(substituir)' : '' }}</label>
                                        <input type="file" name="footer_image" accept="image/*" @change="handleImageUpload($event, 'footer')"
                                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-navy-700 dark:file:text-primary-400">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Texto do Rodapé</label>
                                        <textarea name="footer_text" x-model="formData.footer_text" @input="updatePreview" rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100"></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Alinhamento</label>
                                            <select name="footer_text_align" x-model="formData.footer_text_align" @change="updatePreview"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                                <option value="L">Esquerda</option>
                                                <option value="C">Centro</option>
                                                <option value="R">Direita</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Tamanho da Fonte</label>
                                            <input type="number" name="footer_font_size" x-model.number="formData.footer_font_size" @input="updatePreview" min="6" max="16"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Body Tab -->
                                <div x-show="activeTab === 'body'" x-transition class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Texto do Corpo</label>
                                        <textarea name="body_text" x-model="formData.body_text" @input="updatePreview" rows="4"
                                                  placeholder="Digite o texto principal do documento..."
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Tamanho da Fonte</label>
                                        <input type="number" name="font_size_text" x-model.number="formData.font_size_text" @input="updatePreview" min="8" max="18"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                    </div>
                                </div>

                                <!-- Table Tab -->
                                <div x-show="activeTab === 'table'" x-transition class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Estilo da Tabela</label>
                                        <select name="table_style" x-model="formData.table_style" @change="updatePreview"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                            <option value="grid">Grade Completa</option>
                                            <option value="simple">Simples</option>
                                            <option value="minimal">Minimalista</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="show_table_lines" x-model="formData.show_table_lines" @change="updatePreview" class="w-4 h-4 text-primary-600 rounded">
                                            <span class="text-sm text-gray-700 dark:text-navy-200">Mostrar Linhas</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="use_zebra_stripes" x-model="formData.use_zebra_stripes" @change="updatePreview" class="w-4 h-4 text-primary-600 rounded">
                                            <span class="text-sm text-gray-700 dark:text-navy-200">Linhas Alternadas</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Style Tab -->
                                <div x-show="activeTab === 'style'" x-transition class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Família da Fonte</label>
                                            <select name="font_family" x-model="formData.font_family" @change="updatePreview"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                                <option value="helvetica">Helvetica</option>
                                                <option value="times">Times</option>
                                                <option value="courier">Courier</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Tamanho Título</label>
                                            <input type="number" name="font_size_title" x-model.number="formData.font_size_title" @input="updatePreview" min="12" max="24"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">Margens (mm)</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-navy-300 mb-1">Superior</label>
                                                <input type="number" name="margin_top" x-model.number="formData.margin_top" @input="updatePreview" min="5" max="30"
                                                       class="w-full px-2 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-navy-300 mb-1">Inferior</label>
                                                <input type="number" name="margin_bottom" x-model.number="formData.margin_bottom" @input="updatePreview" min="5" max="30"
                                                       class="w-full px-2 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-navy-300 mb-1">Esquerda</label>
                                                <input type="number" name="margin_left" x-model.number="formData.margin_left" @input="updatePreview" min="5" max="30"
                                                       class="w-full px-2 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-navy-300 mb-1">Direita</label>
                                                <input type="number" name="margin_right" x-model.number="formData.margin_right" @input="updatePreview" min="5" max="30"
                                                       class="w-full px-2 py-2 border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-600 mt-6">
                            <a href="{{ route('pdf-templates.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 hover:bg-gray-100 dark:hover:bg-navy-700 rounded-md transition">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md transition shadow-sm">
                                <x-icon name="save" class="w-4 h-4" />
                                Atualizar Template
                            </button>
                        </div>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <!-- Preview em Tempo Real - Sticky -->
        <div class="xl:sticky xl:top-4 h-fit">
            <x-ui.card title="Preview do PDF" class="border-2 border-primary-200 dark:border-primary-800">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-navy-900 dark:to-navy-800 p-4 rounded-xl shadow-inner">
                    <div class="flex items-center justify-center mb-3">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-navy-300">
                            <x-icon name="eye" class="w-4 h-4" />
                            <span class="font-medium">Visualização em Tempo Real</span>
                        </div>
                    </div>

                    <!-- Container com scroll e escala responsiva -->
                    <div class="overflow-auto rounded-lg shadow-2xl bg-gray-200 dark:bg-navy-900 p-2 sm:p-4" style="max-height: calc(100vh - 280px);">
                        <!-- Wrapper para centralizar e aplicar escala -->
                        <div class="flex items-start justify-center min-h-[300px]">
                            <!-- Página A4 com escala responsiva: mobile=25%, tablet=35%, desktop=45% -->
                            <div class="bg-white shadow-2xl origin-top transition-transform"
                                 style="width: 210mm; min-height: 297mm;
                                        transform: scale(0.25);
                                        margin-bottom: -75%;"
                                 x-data="{ scale: 0.45 }"
                                 x-init="
                                    const updateScale = () => {
                                        const width = window.innerWidth;
                                        if (width < 640) scale = 0.25;
                                        else if (width < 1024) scale = 0.35;
                                        else if (width < 1280) scale = 0.40;
                                        else scale = 0.45;
                                        $el.style.transform = `scale(${scale})`;
                                        $el.style.marginBottom = `${-100 + (scale * 100)}%`;
                                    };
                                    updateScale();
                                    window.addEventListener('resize', updateScale);
                                 ">
                                <!-- Header -->
                                <div x-show="formData.header_text || formData.header_image_preview"
                                     class="border-b-2 border-gray-300"
                                     :style="`text-align: ${formData.header_text_align === 'C' ? 'center' : formData.header_text_align === 'R' ? 'right' : 'left'}; padding: ${formData.margin_top || 10}mm ${formData.margin_right || 10}mm 5mm ${formData.margin_left || 10}mm;`">
                                    <div x-show="formData.header_image_preview" class="mb-3 flex"
                                         :class="formData.header_text_align === 'C' ? 'justify-center' : formData.header_text_align === 'R' ? 'justify-end' : 'justify-start'">
                                        <img :src="formData.header_image_preview"
                                             alt="Header"
                                             class="max-w-full h-auto"
                                             style="max-height: 80px; object-fit: contain;"
                                             onerror="this.style.display='none'; console.error('Erro ao carregar imagem do header')" />
                                    </div>
                                    <p x-show="formData.header_text"
                                       x-text="formData.header_text"
                                       class="text-gray-800 font-medium leading-relaxed"
                                       :style="`font-size: ${formData.header_font_size || 12}px;`"></p>
                                </div>

                                <!-- Body Content -->
                                <div class="px-8 py-6"
                                     :style="`padding-left: ${formData.margin_left || 10}mm; padding-right: ${formData.margin_right || 10}mm;`">

                                    <!-- Title -->
                                    <h1 class="font-bold text-gray-900 mb-4"
                                        :style="`font-size: ${formData.font_size_title || 16}px; font-family: ${formData.font_family || 'helvetica'};`"
                                        x-text="formData.name || 'Título do Documento'"></h1>

                                    <!-- Body Text -->
                                    <div x-show="formData.body_text" class="mb-6">
                                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"
                                           :style="`font-size: ${formData.font_size_text || 12}px;`"
                                           x-text="formData.body_text"></p>
                                    </div>

                                    <!-- Sample Table -->
                                    <div class="mt-6">
                                        <h2 class="text-base font-semibold text-gray-800 mb-2">Exemplo de Tabela</h2>
                                        <table class="w-full border-collapse text-sm"
                                               :class="formData.show_table_lines ? 'border-2 border-gray-400' : ''">
                                            <thead>
                                                <tr :style="`background-color: ${formData.table_header_bg || '#f3f4f6'};`">
                                                    <th class="px-3 py-2 text-left font-semibold text-gray-700"
                                                        :class="formData.show_table_lines ? 'border border-gray-400' : ''">Item</th>
                                                    <th class="px-3 py-2 text-left font-semibold text-gray-700"
                                                        :class="formData.show_table_lines ? 'border border-gray-400' : ''">Descrição</th>
                                                    <th class="px-3 py-2 text-left font-semibold text-gray-700"
                                                        :class="formData.show_table_lines ? 'border border-gray-400' : ''">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="i in 5" :key="i">
                                                    <tr :class="formData.use_zebra_stripes && i % 2 === 0 ? 'bg-gray-50' : 'bg-white'">
                                                        <td class="px-3 py-1.5 text-gray-600"
                                                            :class="formData.show_table_lines ? 'border border-gray-300' : ''"
                                                            x-text="`Item ${i}`"></td>
                                                        <td class="px-3 py-1.5 text-gray-600"
                                                            :class="formData.show_table_lines ? 'border border-gray-300' : ''"
                                                            x-text="`Descrição do item ${i}`"></td>
                                                        <td class="px-3 py-1.5 text-gray-600"
                                                            :class="formData.show_table_lines ? 'border border-gray-300' : ''"
                                                            x-text="`R$ ${(i * 100).toFixed(2)}`"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div x-show="formData.footer_text || formData.footer_image_preview"
                                     class="border-t-2 border-gray-300 absolute bottom-0 left-0 right-0"
                                     :style="`text-align: ${formData.footer_text_align === 'C' ? 'center' : formData.footer_text_align === 'R' ? 'right' : 'left'}; padding: 5mm ${formData.margin_right || 10}mm ${formData.margin_bottom || 10}mm ${formData.margin_left || 10}mm;`">
                                    <p x-show="formData.footer_text"
                                       x-text="formData.footer_text"
                                       class="text-gray-600 text-sm mb-1"
                                       :style="`font-size: ${formData.footer_font_size || 10}px;`"></p>
                                    <div x-show="formData.footer_image_preview" class="mt-2 flex"
                                         :class="formData.footer_text_align === 'C' ? 'justify-center' : formData.footer_text_align === 'R' ? 'justify-end' : 'justify-start'">
                                        <img :src="formData.footer_image_preview"
                                             alt="Footer"
                                             class="max-w-full h-auto"
                                             style="max-height: 50px; object-fit: contain;"
                                             onerror="this.style.display='none'; console.error('Erro ao carregar imagem do footer')" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Badge -->
                    <div class="mt-3 flex items-center justify-center">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full text-xs">
                            <x-icon name="information-circle" class="w-4 h-4" />
                            <span class="hidden sm:inline">Preview em escala adaptativa</span>
                            <span class="sm:hidden">Preview reduzido</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
