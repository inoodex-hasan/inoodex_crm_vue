<template>
    <div
        v-if="currencies?.length"
        class="hidden md:flex relative h-9 min-w-[240px] max-w-[320px] overflow-hidden items-center justify-center bg-gray-50/80 dark:bg-[#1b2e4b]/50 border border-gray-100 dark:border-gray-800 rounded-full px-4 text-xs select-none"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
    >
        <transition name="slide-fade" mode="out-in">
            <div :key="currentIndex" class="flex items-center space-x-2 rtl:space-x-reverse whitespace-nowrap">
                <span class="rounded bg-primary px-1.5 py-0.5 text-[10px] font-black text-white uppercase">
                    {{ currentCurrency.code }}
                </span>
                <span class="font-bold text-gray-500 dark:text-gray-400">1 {{ currentCurrency.code }} =</span>
                <span class="font-black text-emerald-600 dark:text-emerald-400">
                    {{ Number(currentCurrency.exchange_rate).toFixed(2) }}
                </span>
                <span class="font-bold text-gray-400">BDT</span>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    currencies: {
        type: Array,
        default: () => [],
    },
});

const currentIndex = ref(0);
const paused = ref(false);
let timer = null;

const currentCurrency = computed(() => {
    return props.currencies[currentIndex.value] || {};
});

onMounted(() => {
    if (props.currencies?.length > 1) {
        timer = setInterval(() => {
            if (!paused.value) {
                currentIndex.value = (currentIndex.value + 1) % props.currencies.length;
            }
        }, 3500);
    }
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.4s ease;
}
.slide-fade-enter-from {
    transform: translateY(12px);
    opacity: 0;
}
.slide-fade-leave-to {
    transform: translateY(-12px);
    opacity: 0;
}
</style>
