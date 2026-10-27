<template>
    <Head title="Leads Management" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Lead Management
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Track, counsel, and convert prospective students into enrolled applicants.
                    </p>
                </div>
                <div>
                    <Link
                        href="/dashboard/marketing/leads/create"
                        class="px-4 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs flex items-center space-x-1"
                    >
                        <span>+ Capture New Lead</span>
                    </Link>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white dark:bg-[#1b2e4b] p-3.5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Search Input -->
                    <div class="relative w-60 shrink-0">
                        <input
                            v-model="filtersForm.search"
                            @keydown.enter="applyFilters"
                            type="text"
                            placeholder="Search name, phone..."
                            class="w-full ltr:pr-9 rtl:pl-9 px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary"
                        />
                        <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                    </div>

                    <!-- Priority Filter -->
                    <select
                        v-model="filtersForm.priority"
                        @change="applyFilters"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                        <option value="">All Priorities</option>
                        <option value="high">High Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="low">Low Priority</option>
                    </select>

                    <!-- Status Filter -->
                    <select
                        v-model="filtersForm.status"
                        @change="applyFilters"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                        <option value="">All Statuses</option>
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="counseling">Counseling</option>
                        <option value="converted">Converted</option>
                        <option value="lost">Lost</option>
                    </select>

                    <!-- Filter Action Buttons -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button
                            type="button"
                            @click="applyFilters"
                            class="py-2 px-3 text-xs font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs text-center"
                        >
                            Filter
                        </button>
                        <button
                            v-if="hasActiveFilters"
                            type="button"
                            @click="resetFilters"
                            class="py-2 px-3 text-xs font-semibold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-dark/30 transition text-center"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Leads Data Table -->
            <div class="bg-white dark:bg-[#1b2e4b] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-[#0e1726]/60 border-b border-gray-100 dark:border-gray-800 text-gray-400 font-semibold uppercase tracking-wider">
                                <th class="py-3 px-4">Student Name</th>
                                <th class="py-3 px-4">Contact Info</th>
                                <th class="py-3 px-4">Source</th>
                                <th class="py-3 px-4 text-center">Priority</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4">Next Follow-Up</th>
                                <th class="py-3 px-4">Collected By</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                            <tr
                                v-for="lead in leads.data"
                                :key="lead.id"
                                class="hover:bg-gray-50/60 dark:hover:bg-dark/20 transition"
                            >
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ lead.student_name }}
                                    </div>
                                    <div v-if="lead.current_education" class="text-[11px] text-gray-400 mt-0.5">
                                        Edu: {{ lead.current_education }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ lead.phone }}
                                    </div>
                                    <div v-if="lead.email" class="text-[11px] text-gray-400">
                                        {{ lead.email }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 capitalize font-medium">
                                    {{ lead.source || 'Direct' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                                            lead.priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' :
                                            lead.priority === 'medium' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' :
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                        ]"
                                    >
                                        {{ lead.priority || 'Normal' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                                            lead.status === 'converted' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                            lead.status === 'lost' ? 'bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-400' :
                                            'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'
                                        ]"
                                    >
                                        {{ lead.status || 'New' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div v-if="lead.next_follow_up_at" class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ formatDate(lead.next_follow_up_at) }}
                                    </div>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400 font-medium">
                                    {{ lead.creator?.name || 'Staff' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/marketing/leads/${lead.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </Link>

                                        <Link
                                            :href="`/dashboard/marketing/leads/${lead.id}/edit`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Lead
                                        </Link>

                                        <button
                                            type="button"
                                            @click="confirmDelete(lead)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Lead
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!leads?.data?.length">
                                <td colspan="8" class="py-12 text-center text-gray-400 text-sm">
                                    No leads found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    <Pagination
                        :links="leads.links"
                        :from="leads.from"
                        :to="leads.to"
                        :total="leads.total"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TableDropdown from '@/Components/TableDropdown.vue';

const props = defineProps({
    leads: {
        type: Object,
        required: true,
    },
    collectors: {
        type: Array,
        default: () => [],
    },
    canViewAllLeads: {
        type: Boolean,
        default: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '', priority: '', status: '' }),
    },
});

const filtersForm = reactive({
    search: props.filters.search || '',
    priority: props.filters.priority || '',
    status: props.filters.status || '',
});

const hasActiveFilters = computed(() => {
    return !!(filtersForm.search || filtersForm.priority || filtersForm.status);
});

function applyFilters() {
    router.get('/dashboard/marketing/leads', filtersForm, {
        preserveState: true,
        replace: true,
    });
}

function resetFilters() {
    filtersForm.search = '';
    filtersForm.priority = '';
    filtersForm.status = '';
    applyFilters();
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

function confirmDelete(lead) {
    if (confirm(`Are you sure you want to delete lead "${lead.student_name}"?`)) {
        router.delete(`/dashboard/marketing/leads/${lead.id}`);
    }
}
</script>
