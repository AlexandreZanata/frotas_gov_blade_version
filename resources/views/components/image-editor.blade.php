<div x-data="imageEditor()"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-show="isOpen"
     x-on:keydown.escape="close">

    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-show="isOpen" x-transition></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full mx-auto"
             x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Editar Imagem
                </h3>
                <button x-on:click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Controls -->
                <div class="flex flex-wrap gap-4 mb-6 justify-center">
                    <!-- Zoom -->
                    <div class="flex items-center gap-2">
                        <x-input-label for="zoom" value="Zoom" class="text-sm whitespace-nowrap" />
                        <input type="range"
                               id="zoom"
                               x-model="zoom"
                               min="0.1"
                               max="3"
                               step="0.1"
                               class="w-24 sm:w-32">
                        <span class="text-sm text-gray-600 dark:text-gray-400 w-8" x-text="zoom.toFixed(1)"></span>
                    </div>

                    <!-- Rotate -->
                    <div class="flex items-center gap-2">
                        <x-input-label for="rotate" value="Rotação" class="text-sm whitespace-nowrap" />
                        <input type="range"
                               id="rotate"
                               x-model="rotation"
                               min="0"
                               max="360"
                               step="1"
                               class="w-24 sm:w-32">
                        <span class="text-sm text-gray-600 dark:text-gray-400 w-12" x-text="rotation + '°'"></span>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button x-on:click="reset"
                                class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                            Redefinir
                        </button>
                        <button x-on:click="crop"
                                class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                                x-bind:disabled="!isImageLoaded">
                            Cortar
                        </button>
                    </div>
                </div>

                <!-- Image Container -->
                <div class="relative bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden max-h-96">
                    <img id="editor-image"
                         x-ref="image"
                         class="max-w-full max-h-96 mx-auto"
                         x-bind:style="{
                            transform: `scale(${zoom}) rotate(${rotation}deg)`,
                            transition: 'transform 0.3s ease'
                         }">

                    <!-- Crop Overlay -->
                    <div x-show="isCropping"
                         x-ref="cropOverlay"
                         class="absolute border-2 border-blue-500 bg-blue-500 bg-opacity-20 cursor-move"
                         x-on:mousedown="startDrag($event)"
                         x-on:touchstart="startDrag($event)">
                        <div class="absolute -top-1 -left-1 w-3 h-3 bg-blue-500 rounded-full cursor-nw-resize"
                             x-on:mousedown="startResize($event, 'nw')"
                             x-on:touchstart="startResize($event, 'nw')"></div>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full cursor-ne-resize"
                             x-on:mousedown="startResize($event, 'ne')"
                             x-on:touchstart="startResize($event, 'ne')"></div>
                        <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-blue-500 rounded-full cursor-sw-resize"
                             x-on:mousedown="startResize($event, 'sw')"
                             x-on:touchstart="startResize($event, 'sw')"></div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-blue-500 rounded-full cursor-se-resize"
                             x-on:mousedown="startResize($event, 'se')"
                             x-on:touchstart="startResize($event, 'se')"></div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 text-center">
                    <p>Arraste para mover a área de corte • Use as alças para redimensionar</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button x-on:click="close"
                        class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                    Cancelar
                </button>
                <button x-on:click="save"
                        class="px-4 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                        x-bind:disabled="!isImageLoaded">
                    Salvar Imagem
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function imageEditor() {
        return {
            isOpen: false,
            isImageLoaded: false,
            isCropping: false,
            zoom: 1,
            rotation: 0,
            originalFile: null,
            targetInput: null,
            cropArea: { x: 0, y: 0, width: 200, height: 200 },
            isDragging: false,
            isResizing: false,
            resizeDirection: null,
            startPos: { x: 0, y: 0 },

            init() {
                // Configurações iniciais para mobile
                this.setupMobileEvents();
            },

            setupMobileEvents() {
                // Prevenir zoom duplo-tap em mobile
                this.$refs.image?.addEventListener('touchstart', (e) => {
                    if (e.touches.length > 1) {
                        e.preventDefault();
                    }
                }, { passive: false });
            },

            open(file, inputElement) {
                this.originalFile = file;
                this.targetInput = inputElement;
                this.isOpen = true;
                this.isImageLoaded = false;

                // Carrega a imagem
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.$refs.image.src = e.target.result;
                    this.$refs.image.onload = () => {
                        this.isImageLoaded = true;
                        this.initializeCropArea();
                    };
                };
                reader.readAsDataURL(file);
            },

            close() {
                this.isOpen = false;
                this.reset();
            },

            reset() {
                this.zoom = 1;
                this.rotation = 0;
                this.initializeCropArea();
            },

            initializeCropArea() {
                const img = this.$refs.image;
                const container = img.parentElement;

                // Define área de corte como 80% da imagem
                const size = Math.min(img.width, img.height) * 0.8;
                this.cropArea = {
                    x: (img.width - size) / 2,
                    y: (img.height - size) / 2,
                    width: size,
                    height: size
                };

                this.updateCropOverlay();
                this.isCropping = true;
            },

            updateCropOverlay() {
                if (this.$refs.cropOverlay) {
                    this.$refs.cropOverlay.style.left = this.cropArea.x + 'px';
                    this.$refs.cropOverlay.style.top = this.cropArea.y + 'px';
                    this.$refs.cropOverlay.style.width = this.cropArea.width + 'px';
                    this.$refs.cropOverlay.style.height = this.cropArea.height + 'px';
                }
            },

            startDrag(e) {
                e.preventDefault();
                this.isDragging = true;
                this.startPos = this.getEventPosition(e);

                const moveHandler = (e) => this.drag(e);
                const upHandler = () => this.stopDrag();

                document.addEventListener('mousemove', moveHandler);
                document.addEventListener('touchmove', moveHandler, { passive: false });
                document.addEventListener('mouseup', upHandler);
                document.addEventListener('touchend', upHandler);
            },

            drag(e) {
                if (!this.isDragging) return;
                e.preventDefault();

                const currentPos = this.getEventPosition(e);
                const deltaX = currentPos.x - this.startPos.x;
                const deltaY = currentPos.y - this.startPos.y;

                this.cropArea.x += deltaX;
                this.cropArea.y += deltaY;

                // Limita dentro da imagem
                const img = this.$refs.image;
                this.cropArea.x = Math.max(0, Math.min(img.width - this.cropArea.width, this.cropArea.x));
                this.cropArea.y = Math.max(0, Math.min(img.height - this.cropArea.height, this.cropArea.y));

                this.updateCropOverlay();
                this.startPos = currentPos;
            },

            stopDrag() {
                this.isDragging = false;
            },

            startResize(e, direction) {
                e.preventDefault();
                e.stopPropagation();

                this.isResizing = true;
                this.resizeDirection = direction;
                this.startPos = this.getEventPosition(e);

                const moveHandler = (e) => this.resize(e);
                const upHandler = () => this.stopResize();

                document.addEventListener('mousemove', moveHandler);
                document.addEventListener('touchmove', moveHandler, { passive: false });
                document.addEventListener('mouseup', upHandler);
                document.addEventListener('touchend', upHandler);
            },

            resize(e) {
                if (!this.isResizing) return;
                e.preventDefault();

                const currentPos = this.getEventPosition(e);
                const deltaX = currentPos.x - this.startPos.x;
                const deltaY = currentPos.y - this.startPos.y;

                const minSize = 50; // Tamanho mínimo

                switch (this.resizeDirection) {
                    case 'nw':
                        this.cropArea.x += deltaX;
                        this.cropArea.y += deltaY;
                        this.cropArea.width -= deltaX;
                        this.cropArea.height -= deltaY;
                        break;
                    case 'ne':
                        this.cropArea.y += deltaY;
                        this.cropArea.width += deltaX;
                        this.cropArea.height -= deltaY;
                        break;
                    case 'sw':
                        this.cropArea.x += deltaX;
                        this.cropArea.width -= deltaX;
                        this.cropArea.height += deltaY;
                        break;
                    case 'se':
                        this.cropArea.width += deltaX;
                        this.cropArea.height += deltaY;
                        break;
                }

                // Garante tamanho mínimo
                if (this.cropArea.width < minSize) this.cropArea.width = minSize;
                if (this.cropArea.height < minSize) this.cropArea.height = minSize;

                // Limita dentro da imagem
                const img = this.$refs.image;
                this.cropArea.x = Math.max(0, Math.min(img.width - this.cropArea.width, this.cropArea.x));
                this.cropArea.y = Math.max(0, Math.min(img.height - this.cropArea.height, this.cropArea.y));
                this.cropArea.width = Math.min(img.width - this.cropArea.x, this.cropArea.width);
                this.cropArea.height = Math.min(img.height - this.cropArea.y, this.cropArea.height);

                this.updateCropOverlay();
                this.startPos = currentPos;
            },

            stopResize() {
                this.isResizing = false;
                this.resizeDirection = null;
            },

            getEventPosition(e) {
                const rect = this.$refs.image.getBoundingClientRect();
                return {
                    x: (e.clientX || e.touches[0].clientX) - rect.left,
                    y: (e.clientY || e.touches[0].clientY) - rect.top
                };
            },

            crop() {
                // Aplica o corte atual (para visualização)
                this.isCropping = false;
            },

            async save() {
                if (!this.isImageLoaded) return;

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // Configura o canvas com as dimensões do corte
                canvas.width = this.cropArea.width;
                canvas.height = this.cropArea.height;

                // Aplica transformações (zoom e rotação seriam mais complexas - simplificando)
                ctx.drawImage(
                    this.$refs.image,
                    this.cropArea.x, this.cropArea.y, this.cropArea.width, this.cropArea.height,
                    0, 0, this.cropArea.width, this.cropArea.height
                );

                // Converte para blob e atualiza o input de arquivo
                canvas.toBlob((blob) => {
                    const file = new File([blob], this.originalFile.name, {
                        type: this.originalFile.type,
                        lastModified: Date.now()
                    });

                    // Atualiza o input de arquivo
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    this.targetInput.files = dataTransfer.files;

                    // Fecha o editor
                    this.close();

                    // Mostra mensagem de sucesso
                    this.showSuccessMessage();
                }, this.originalFile.type, 0.95);
            },

            showSuccessMessage() {
                // Você pode implementar uma notificação toast aqui
                console.log('Imagem editada com sucesso!');
            }
        }
    }

    // Função global para abrir o editor
    window.openImageEditor = function(file, inputElement) {
        const editor = document.querySelector('[x-data="imageEditor()"]');
        if (editor) {
            editor.__x.$data.open(file, inputElement);
        }
    };
</script>

<style>
    [x-cloak] { display: none !important; }

    /* Estilos para melhor experiência mobile */
    @media (max-width: 640px) {
        [x-data="imageEditor()"] .max-w-4xl {
            margin: 0;
            max-width: 100%;
            height: 100vh;
        }

        [x-data="imageEditor()"] .flex-wrap {
            justify-content: center;
        }

        [x-data="imageEditor()"] .w-24,
        [x-data="imageEditor()"] .w-32 {
            width: 80px;
        }
    }

    /* Melhorar a experiência tátil */
    @media (hover: none) and (pointer: coarse) {
        [x-data="imageEditor()"] .cursor-move,
        [x-data="imageEditor()"] .cursor-nw-resize,
        [x-data="imageEditor()"] .cursor-ne-resize,
        [x-data="imageEditor()"] .cursor-sw-resize,
        [x-data="imageEditor()"] .cursor-se-resize {
            touch-action: none;
        }
    }
</style>
