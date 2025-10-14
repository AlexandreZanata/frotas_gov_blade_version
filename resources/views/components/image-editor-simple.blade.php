<div x-data="imageEditorSimple()"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-show="isOpen">

    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-show="isOpen" x-transition></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-auto"
             x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Editar Imagem
                </h3>
                <button type="button" @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-4">
                <div class="space-y-4">
                    <!-- Controls -->
                    <div class="flex flex-wrap gap-4 justify-center">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-700 dark:text-gray-300">Zoom</label>
                            <input type="range"
                                   x-model="zoom"
                                   min="0.5"
                                   max="2"
                                   step="0.1"
                                   class="w-20">
                            <span class="text-sm w-8" x-text="zoom.toFixed(1)"></span>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-700 dark:text-gray-300">Rotação</label>
                            <input type="range"
                                   x-model="rotation"
                                   min="0"
                                   max="360"
                                   step="90"
                                   class="w-20">
                            <span class="text-sm w-12" x-text="rotation + '°'"></span>
                        </div>

                        <button type="button" @click="reset()"
                                class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                            Redefinir
                        </button>
                    </div>

                    <!-- Image Preview -->
                    <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 flex justify-center">
                        <img x-ref="image"
                             class="max-w-full max-h-64 transition-transform duration-200"
                             :style="`transform: scale(${zoom}) rotate(${rotation}deg)`">
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                        Ajuste o zoom e rotação da imagem
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" @click="close()"
                        class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600">
                    Cancelar
                </button>
                <button type="button" @click="save()"
                        class="px-4 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                    Usar Esta Imagem
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function imageEditorSimple() {
        return {
            isOpen: false,
            zoom: 1,
            rotation: 0,
            originalFile: null,
            targetInput: null,

            open(file, inputElement) {
                this.originalFile = file;
                this.targetInput = inputElement;
                this.isOpen = true;

                // Load image
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.$refs.image.src = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            close() {
                this.isOpen = false;
                this.reset();
                // Clear the file input
                if (this.targetInput) {
                    this.targetInput.value = '';
                }
            },

            reset() {
                this.zoom = 1;
                this.rotation = 0;
            },

            save() {
                if (!this.originalFile) return;

                try {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const img = this.$refs.image;

                    // Set canvas size to original image size
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;

                    // Apply transformations
                    ctx.translate(canvas.width / 2, canvas.height / 2);
                    ctx.rotate(this.rotation * Math.PI / 180);
                    ctx.scale(this.zoom, this.zoom);
                    ctx.translate(-canvas.width / 2, -canvas.height / 2);

                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    // Convert to blob and update file input
                    canvas.toBlob((blob) => {
                        const file = new File([blob], this.originalFile.name, {
                            type: this.originalFile.type,
                            lastModified: Date.now()
                        });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.targetInput.files = dataTransfer.files;

                        this.close();

                        // Show success message
                        this.showSuccess();
                    }, this.originalFile.type, 0.9);

                } catch (error) {
                    console.error('Error processing image:', error);
                    alert('Erro ao processar a imagem. A imagem será enviada sem edição.');
                    this.close();
                }
            },

            showSuccess() {
                // Optional: Add a toast notification here
                console.log('Imagem processada com sucesso!');
            }
        }
    }

    // Global function to open editor
    window.openSimpleImageEditor = function(file, inputElement) {
        const editor = document.querySelector('[x-data="imageEditorSimple()"]');
        if (editor && editor.__x) {
            editor.__x.$data.open(file, inputElement);
            return true;
        }
        return false;
    };
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
