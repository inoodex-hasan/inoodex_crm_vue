<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    name: String,
    extension: String,
    fileUrl: String,
    downloadUrl: String,
})

const isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(props.extension?.toLowerCase())
const isPdf = props.extension?.toLowerCase() === 'pdf'
</script>

<template>
    <AdminLayout>
        <Head :title="`Document Preview - ${name}`" />

        <div class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Document Preview</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 break-all">{{ name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        :href="downloadUrl"
                        class="px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        Download File
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden p-4">
                <div v-if="isImage" class="flex justify-center p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl">
                    <img :src="fileUrl" :alt="name" class="max-h-[80vh] object-contain rounded-lg shadow-sm" />
                </div>

                <div v-else-if="isPdf" class="space-y-4">
                    <iframe :src="fileUrl" class="w-full h-[80vh] rounded-xl border border-slate-200 dark:border-slate-800" :title="name"></iframe>
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-xs text-slate-500">
                        <span>If the PDF does not render inline, you can open or download it directly.</span>
                        <div class="flex items-center gap-2">
                            <a :href="fileUrl" target="_blank" class="font-semibold text-primary hover:underline">Open in New Tab</a>
                            <span>&bull;</span>
                            <a :href="downloadUrl" class="font-semibold text-slate-700 dark:text-slate-300 hover:underline">Download</a>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-16 px-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl">
                    <p class="text-base font-semibold text-slate-800 dark:text-slate-200">Inline preview is not available for this file type ({{ extension }}).</p>
                    <p class="text-xs text-slate-400 mt-1">Supported preview types: PDF, JPG, PNG, WEBP, GIF.</p>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <a :href="fileUrl" target="_blank" class="px-4 py-2 text-sm font-medium border border-slate-300 dark:border-slate-700 rounded-xl">Open File</a>
                        <a :href="downloadUrl" class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-xl">Download</a>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
