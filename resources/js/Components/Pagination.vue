<template>
    <div v-if="resolvedLinks && resolvedLinks.length > 3" class="flex flex-wrap items-center justify-between gap-4 py-3 text-xs">
        <div class="text-gray-500 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-700 dark:text-gray-200">{{ resolvedFrom }}</span>
            to <span class="font-semibold text-gray-700 dark:text-gray-200">{{ resolvedTo }}</span>
            of <span class="font-semibold text-gray-700 dark:text-gray-200">{{ resolvedTotal }}</span> results
        </div>

        <div class="flex flex-wrap items-center gap-1">
            <template v-for="(link, index) in resolvedLinks" :key="index">
                <div
                    v-if="!link.url"
                    class="px-3 py-1.5 rounded border border-gray-200 dark:border-gray-700 text-gray-400 select-none opacity-50"
                    v-html="cleanLabel(link.label)"
                />
                <Link
                    v-else
                    :href="link.url"
                    :class="[
                        'px-3 py-1.5 rounded border font-semibold transition',
                        link.active
                            ? 'bg-primary text-white border-primary shadow-xs'
                            : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark/40'
                    ]"
                    v-html="cleanLabel(link.label)"
                    preserve-scroll
                />
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    pagination: {
        type: Object,
        default: null,
    },
    links: {
        type: Array,
        default: () => [],
    },
    from: {
        type: Number,
        default: 0,
    },
    to: {
        type: Number,
        default: 0,
    },
    total: {
        type: Number,
        default: 0,
    },
});

const resolvedLinks = computed(() => props.pagination?.links || props.links || []);
const resolvedFrom = computed(() => props.pagination?.from ?? props.from ?? 0);
const resolvedTo = computed(() => props.pagination?.to ?? props.to ?? 0);
const resolvedTotal = computed(() => props.pagination?.total ?? props.total ?? 0);

function cleanLabel(label) {
    if (!label) return '';
    return label
        .replace('&laquo; Previous', '← Prev')
        .replace('Next &raquo;', 'Next →');
}
</script>
