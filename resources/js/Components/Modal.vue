<template>
    <teleport to="body">
        <transition name="modal-fade">
            <div
                v-if="show"
                class="fixed inset-0 z-[9999] overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
                @click.self="closeOnBackdrop && $emit('close')"
            >
                <div
                    class="bg-white dark:bg-[#0e1726] w-full rounded-xl shadow-2xl border border-[#e0e6ed] dark:border-[#1b2e4b] transition-all transform overflow-hidden"
                    :class="maxWidthClass"
                >
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between bg-white dark:bg-[#0e1726]">
                        <h3 class="text-base font-bold text-[#0e1726] dark:text-white">
                            {{ title }}
                        </h3>
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="text-[#506690] hover:text-[#0e1726] dark:text-[#888ea8] dark:hover:text-white transition"
                            aria-label="Close modal"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 bg-white dark:bg-[#0e1726] text-[#0e1726] dark:text-[#d0d2d6]">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div
                        v-if="$slots.footer"
                        class="px-6 py-3.5 bg-[#f6f8fa] dark:bg-[#121e32] border-t border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-end space-x-2"
                    >
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirm',
    },
    maxWidth: {
        type: String,
        default: 'md', // sm, md, lg, xl, 2xl, 4xl
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

const maxWidthClass = computed(() => {
    switch (props.maxWidth) {
        case 'sm': return 'max-w-sm';
        case 'md': return 'max-w-md';
        case 'lg': return 'max-w-lg';
        case 'xl': return 'max-w-xl';
        case '2xl': return 'max-w-2xl';
        case '4xl': return 'max-w-4xl';
        default: return 'max-w-md';
    }
});

function handleKeydown(e) {
    if (e.key === 'Escape' && props.show) {
        emit('close');
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
</style>
