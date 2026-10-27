<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    campaign: Object,
})
</script>

<template>
    <AdminLayout>
        <Head :title="`Campaign: ${campaign.name}`" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ campaign.name }}</h1>
                    <span
                        :class="[
                            'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider',
                            campaign.boosting_status === 'on' ? 'bg-primary/10 text-primary border border-primary/20' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                        ]"
                    >
                        Boosting {{ campaign.boosting_status }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="`/dashboard/marketing/campaigns/${campaign.id}/edit`"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </Link>
                    <Link
                        href="/dashboard/marketing/campaigns"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Back to List
                    </Link>
                </div>
            </div>

            <!-- Details Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-xs uppercase font-bold text-slate-400 dark:text-slate-500">Start Date</span>
                        <p class="text-base font-semibold text-slate-800 dark:text-slate-200 mt-1">
                            {{ campaign.start_date ? new Date(campaign.start_date).toLocaleDateString() : 'Not Set' }}
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-xs uppercase font-bold text-slate-400 dark:text-slate-500">End Date</span>
                        <p :class="['text-base font-semibold mt-1', campaign.end_date && new Date(campaign.end_date) < new Date() ? 'text-rose-500' : 'text-slate-800 dark:text-slate-200']">
                            {{ campaign.end_date ? new Date(campaign.end_date).toLocaleDateString() : 'Not Set' }}
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-xs uppercase font-bold text-slate-400 dark:text-slate-500">Boosting Status</span>
                        <p class="text-base font-semibold text-primary capitalize mt-1">
                            {{ campaign.boosting_status }}
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-xs uppercase font-bold text-slate-400 dark:text-slate-500">Created By</span>
                        <p class="text-base font-semibold text-slate-800 dark:text-slate-200 mt-1">
                            {{ campaign.creator?.name || 'System' }}
                        </p>
                    </div>
                </div>

                <div v-if="campaign.notes" class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-400 mb-2">Campaign Strategy & Notes</h3>
                    <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                        {{ campaign.notes }}
                    </p>
                </div>
            </div>

            <!-- Quick Navigation to Creatives -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Link
                    href="/dashboard/marketing/videos"
                    class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-primary/40 dark:hover:border-primary/40 transition shadow-xs flex items-center justify-between group"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/40 text-sky-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">Video Creatives</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Manage video production stages & statuses</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>

                <Link
                    href="/dashboard/marketing/posters"
                    class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/40 dark:hover:border-emerald-500/40 transition shadow-xs flex items-center justify-between group"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-emerald-500 transition-colors">Poster Creatives</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Track banner design, editing, and upload status</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
