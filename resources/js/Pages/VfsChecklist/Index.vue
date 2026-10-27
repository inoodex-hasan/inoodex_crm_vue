<template>
    <Head title="VFS Visa Checklist" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        VFS Visa Document Checklists
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Track, verify, and complete embassy visa submission requirements per student application.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        href="/dashboard/vfs-checklist/templates"
                        class="px-3.5 py-2 text-xs font-semibold rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition"
                    >
                        Checklist Templates
                    </a>
                </div>
            </div>

            <!-- Search & Filters Panel -->
            <div class="bg-white dark:bg-[#1b2e4b] p-3 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="handleSearch" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-64 shrink-0">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Application, Student..."
                            class="w-full ltr:pr-9 rtl:pl-9 px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                        />
                        <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                    </div>
                    <button
                        type="submit"
                        class="px-3 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary/90 transition shadow-xs shrink-0"
                    >
                        Search
                    </button>
                    <button
                        v-if="searchQuery"
                        type="button"
                        @click="clearSearch"
                        class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 text-xs font-medium hover:bg-gray-50 dark:hover:bg-dark/40 transition shrink-0"
                    >
                        Clear
                    </button>
                </form>
            </div>

            <!-- Table of Applications -->
            <div class="bg-white dark:bg-[#1b2e4b] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-[#0e1726]/60 border-b border-gray-100 dark:border-gray-800 text-gray-400 font-semibold uppercase tracking-wider">
                                <th class="py-3 px-4">Application ID</th>
                                <th class="py-3 px-4">Student</th>
                                <th class="py-3 px-4">University & Country</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Verified / Total</th>
                                <th class="py-3 px-4 text-center">Checklist Progress</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                            <tr
                                v-for="app in applications.data"
                                :key="app.id"
                                class="hover:bg-gray-50/60 dark:hover:bg-dark/20 transition"
                            >
                                <td class="py-3 px-4 font-mono font-bold text-primary">
                                    {{ app.application_id }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ app.student?.name || 'N/A' }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ app.student?.email || '' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ app.university?.name || '—' }}
                                    </div>
                                    <div v-if="app.university?.country" class="text-[11px] text-primary font-semibold">
                                        {{ app.university.country }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                                            app.vfs_result === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                            app.vfs_result === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' :
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                        ]"
                                    >
                                        {{ app.vfs_result || app.status || 'Active' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-mono font-bold">
                                    <span class="text-emerald-600 dark:text-emerald-400">{{ app.checked_items }}</span>
                                    <span class="text-gray-400"> / {{ app.total_items }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                            <div
                                                :class="app.progress === 100 ? 'bg-emerald-500' : 'bg-primary'"
                                                class="h-full rounded-full transition-all duration-300"
                                                :style="{ width: app.progress + '%' }"
                                            />
                                        </div>
                                        <span class="font-bold text-[11px] w-8 text-right font-mono">
                                            {{ app.progress }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/vfs-checklist/${app.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            Open Checklist
                                        </Link>
                                        <Link
                                            :href="`/dashboard/applications/${app.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Application
                                        </Link>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!applications?.data?.length">
                                <td colspan="7" class="py-12 text-center text-gray-400 text-sm">
                                    No applications found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    <Pagination
                        :links="applications.links"
                        :from="applications.from"
                        :to="applications.to"
                        :total="applications.total"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TableDropdown from '@/Components/TableDropdown.vue';

const props = defineProps({
    applications: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const searchQuery = ref(props.filters.search || '');

function handleSearch() {
    router.get(
        '/dashboard/vfs-checklist',
        { search: searchQuery.value },
        { preserveState: true, replace: true }
    );
}

function clearSearch() {
    searchQuery.value = '';
    handleSearch();
}
</script>
