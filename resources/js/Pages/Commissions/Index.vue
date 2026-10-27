<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    commissions: Object,
    stats: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const workflowStatus = ref(props.filters?.workflow_status || '')

function applyFilters() {
    router.get('/dashboard/commissions', {
        search: search.value || undefined,
        workflow_status: workflowStatus.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    workflowStatus.value = ''
    applyFilters()
}

function markAsPaid(id) {
    if (confirm('Mark this approved commission claim as paid?')) {
        router.post(`/dashboard/commissions/${id}/mark-paid`, {}, {
            preserveScroll: true,
        })
    }
}

function deleteCommission(id) {
    if (confirm('Are you sure you want to delete this commission record?')) {
        router.delete(`/dashboard/commissions/${id}`, {
            preserveScroll: true,
        })
    }
}

const statusBadgeClasses = {
    draft: 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
    claimed: 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/50',
    under_review: 'bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200/50 dark:border-sky-800/50',
    approved: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50',
    rejected: 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50',
    paid: 'bg-primary/10 text-primary border border-primary/20',
}
</script>

<template>
    <AdminLayout>
        <Head title="Commission Management" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Commission Management</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Review staff commission claims, approve payouts, and manage audit trails.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard/commissions/pending"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300 rounded-xl border border-amber-200/60 dark:border-amber-800/60 transition shadow-xs"
                    >
                        Pending Review
                        <span v-if="stats && stats.pending_review > 0" class="px-2 py-0.5 rounded-full text-xs font-black bg-rose-500 text-white">
                            {{ stats.pending_review }}
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-sky-500">Pending</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.pending_review || 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-amber-500">Claimed</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.claimed || 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-slate-500">Under Review</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.under_review || 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-emerald-500">Approved</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.approved || 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-rose-500">Rejected</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.rejected || 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-primary">Paid</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.paid || 0 }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3 shadow-xs">
                <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-64 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search employee, student..."
                            class="w-full ltr:pr-9 rtl:pl-9 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                            </svg>
                        </span>
                    </div>
                    <select
                        v-model="workflowStatus"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="claimed">Claimed</option>
                        <option value="under_review">Under Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="paid">Paid</option>
                    </select>
                    <div class="flex items-center gap-2 shrink-0">
                        <button
                            type="submit"
                            class="px-3 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-xs shrink-0"
                        >
                            Filter
                        </button>
                        <button
                            type="button"
                            @click="resetFilters"
                            class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition shrink-0"
                        >
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                                <th class="px-5 py-3.5">Application</th>
                                <th class="px-5 py-3.5">Student</th>
                                <th class="px-5 py-3.5">Employee</th>
                                <th class="px-5 py-3.5">Proposed</th>
                                <th class="px-5 py-3.5">Approved Amount</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Claimed Date</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="c in commissions.data"
                                :key="c.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4">
                                    <Link
                                        v-if="c.application"
                                        :href="`/dashboard/applications/${c.application.id}`"
                                        class="font-mono font-bold text-primary hover:underline"
                                    >
                                        {{ c.application.application_id }}
                                    </Link>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td class="px-5 py-4 text-slate-900 dark:text-white font-medium">
                                    {{ c.application?.student ? (c.application.student.first_name + ' ' + (c.application.student.last_name || '')) : '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ c.user?.name || '-' }}
                                </td>
                                <td class="px-5 py-4 font-mono text-slate-700 dark:text-slate-300">
                                    BDT {{ Number(c.proposed_amount || 0).toLocaleString() }}
                                </td>
                                <td class="px-5 py-4 font-mono font-semibold text-slate-900 dark:text-white">
                                    <span v-if="c.amount > 0">BDT {{ Number(c.amount).toLocaleString() }}</span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider',
                                            statusBadgeClasses[c.workflow_status] || 'bg-slate-100 text-slate-600'
                                        ]"
                                    >
                                        {{ c.workflow_status ? c.workflow_status.replace('_', ' ') : '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ c.claimed_at ? new Date(c.claimed_at).toLocaleDateString() : '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/commissions/${c.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </Link>
                                        <Link
                                            v-if="c.workflow_status === 'claimed' || c.workflow_status === 'under_review'"
                                            :href="`/dashboard/commissions/${c.id}/review`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            Review Commission
                                        </Link>
                                        <button
                                            v-if="c.workflow_status === 'approved'"
                                            type="button"
                                            @click="markAsPaid(c.id)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Mark as Paid
                                        </button>
                                        <button
                                            type="button"
                                            @click="deleteCommission(c.id)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Commission
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!commissions.data || commissions.data.length === 0">
                                <td colspan="8" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No commission records found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="commissions.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="commissions.links" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
