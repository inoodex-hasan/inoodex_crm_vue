<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    invoices: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status || '')

function applyFilters() {
    router.get('/dashboard/invoices', {
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

function deleteInvoice(id, number) {
    if (confirm(`Delete invoice ${number}? This will reverse journal entries if drafted.`)) {
        router.delete(`/dashboard/invoices/${id}`, {
            preserveScroll: true,
        })
    }
}

const statusBadgeClasses = {
    draft: 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
    sent: 'bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200/50 dark:border-sky-800/50',
    paid: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50',
    partial: 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/50',
    cancelled: 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50',
}
</script>

<template>
    <AdminLayout>
        <Head title="Invoices" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Student Invoices</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Issue tuition & service fee invoices with linked chart of account entries.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard/invoices/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Invoice
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-xs">
                <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-60 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search invoice #, student..."
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
                        <option value="sent">Sent</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                        <option value="cancelled">Cancelled</option>
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
                                <th class="px-5 py-3.5">Invoice #</th>
                                <th class="px-5 py-3.5">Student</th>
                                <th class="px-5 py-3.5">Application</th>
                                <th class="px-5 py-3.5">Date</th>
                                <th class="px-5 py-3.5">Due Date</th>
                                <th class="px-5 py-3.5">Amount</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="inv in invoices.data"
                                :key="inv.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4 font-mono font-bold text-primary">
                                    <Link :href="`/dashboard/invoices/${inv.id}`" class="hover:underline">
                                        {{ inv.invoice_number }}
                                    </Link>
                                </td>
                                <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">
                                    {{ inv.student ? (inv.student.first_name + ' ' + (inv.student.last_name || '')) : '-' }}
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-slate-600 dark:text-slate-400">
                                    {{ inv.application?.application_id || '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ inv.date ? new Date(inv.date).toLocaleDateString() : '-' }}
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <span :class="inv.due_date && new Date(inv.due_date) < new Date() && inv.status !== 'paid' ? 'text-rose-500 font-bold' : 'text-slate-500 dark:text-slate-400'">
                                        {{ inv.due_date ? new Date(inv.due_date).toLocaleDateString() : '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-mono font-bold text-slate-900 dark:text-white">
                                    BDT {{ Number(inv.total_amount || 0).toLocaleString() }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider',
                                            statusBadgeClasses[inv.status] || 'bg-slate-100 text-slate-600'
                                        ]"
                                    >
                                        {{ inv.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/invoices/${inv.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </Link>
                                        <a
                                            :href="`/dashboard/invoices/${inv.id}/pdf`"
                                            target="_blank"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/30 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Print / PDF
                                        </a>
                                        <button
                                            type="button"
                                            @click="deleteInvoice(inv.id, inv.invoice_number)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Invoice
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!invoices.data || invoices.data.length === 0">
                                <td colspan="8" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No invoices found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="invoices.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="invoices.links" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
