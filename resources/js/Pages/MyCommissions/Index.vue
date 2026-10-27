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
const status = ref(props.filters?.status || '')

function applyFilters() {
    router.get('/dashboard/my-commissions', {
        search: search.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    status.value = ''
    applyFilters()
}

function cancelClaim(id) {
    if (confirm('Are you sure you want to cancel this pending commission claim?')) {
        router.post(`/dashboard/my-commissions/${id}/cancel`, {}, {
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
        <Head title="My Commissions" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">My Commission Dashboard</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Track your claimed earnings, approvals, and payout disbursements.</p>
                </div>
                <Link
                    href="/dashboard/my-commissions/claimable"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Claim Commission
                </Link>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-slate-400">Draft / Cancelled</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.draft || 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-amber-500">Under Review</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ (stats?.claimed || 0) + (stats?.under_review || 0) }}
                    </p>
                    <span class="text-xs font-mono text-slate-500 block mt-1">
                        BDT {{ Number(stats?.total_claimed_amount || 0).toLocaleString() }}
                    </span>
                </div>
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-emerald-500">Approved</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.approved || 0 }}</p>
                    <span class="text-xs font-mono text-slate-500 block mt-1">
                        BDT {{ Number(stats?.total_approved_amount || 0).toLocaleString() }}
                    </span>
                </div>
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-primary">Paid to Account</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ stats?.paid || 0 }}</p>
                    <span class="text-xs font-mono text-primary font-bold block mt-1">
                        BDT {{ Number(stats?.total_paid_amount || 0).toLocaleString() }}
                    </span>
                </div>
            </div>

            <!-- Rejected Warning Banner -->
            <div v-if="stats && stats.rejected > 0" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="text-sm text-rose-800 dark:text-rose-200">
                    You have <b>{{ stats.rejected }}</b> rejected claim(s). Click view details to read accountant feedback and resubmit adjustments.
                </div>
            </div>

            <!-- Filter Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-xs">
                <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-60 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search application ID, student..."
                            class="w-full ltr:pr-9 rtl:pl-9 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                            </svg>
                        </span>
                    </div>
                    <select
                        v-model="status"
                        class="form-select !w-auto max-w-fit shrink-0 px-3 py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    >
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="claimed">Claimed</option>
                        <option value="under_review">Under Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="paid">Paid</option>
                    </select>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button
                            type="submit"
                            class="px-3.5 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary-dark transition shadow-xs"
                        >
                            Filter
                        </button>
                        <button
                            type="button"
                            @click="resetFilters"
                            class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition"
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
                                <th class="px-5 py-3.5">University</th>
                                <th class="px-5 py-3.5">Proposed</th>
                                <th class="px-5 py-3.5">Approved Amount</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="c in commissions.data"
                                :key="c.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4 font-mono font-bold text-primary">
                                    {{ c.application?.application_id || '-' }}
                                </td>
                                <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">
                                    {{ c.application?.student ? (c.application.student.first_name + ' ' + (c.application.student.last_name || '')) : '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ c.application?.university?.name || '-' }}
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
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/my-commissions/${c.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </Link>
                                        <button
                                            v-if="c.workflow_status === 'claimed'"
                                            type="button"
                                            @click="cancelClaim(c.id)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancel Claim
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!commissions.data || commissions.data.length === 0">
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No commissions claimed yet.
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
