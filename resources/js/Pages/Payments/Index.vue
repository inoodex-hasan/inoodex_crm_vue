<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    payments: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const paymentType = ref(props.filters?.payment_type || '')
const paymentStatus = ref(props.filters?.payment_status || '')

function applyFilters() {
    router.get('/dashboard/payments', {
        search: search.value || undefined,
        payment_type: paymentType.value || undefined,
        payment_status: paymentStatus.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    paymentType.value = ''
    paymentStatus.value = ''
    applyFilters()
}

function deletePayment(id, receipt) {
    if (confirm(`Delete payment receipt "${receipt}"? This will reverse any journal entries.`)) {
        router.delete(`/dashboard/payments/${id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Payments Collection" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Fee Collections & Receipts</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Track student advance, partial and final tuition collections and print invoices.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        href="/dashboard/payments/report?output=preview"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Preview Report
                    </a>
                    <Link
                        href="/dashboard/payments/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Payment
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
                            placeholder="Search student, receipt, ID..."
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
                        v-model="paymentType"
                        class="form-select !w-auto max-w-fit shrink-0 px-3 py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    >
                        <option value="">All Types</option>
                        <option value="advance">Advance</option>
                        <option value="partial">Partial</option>
                        <option value="final">Final</option>
                    </select>
                    <select
                        v-model="paymentStatus"
                        class="form-select !w-auto max-w-fit shrink-0 px-3 py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
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
                                <th class="px-5 py-3.5">Receipt #</th>
                                <th class="px-5 py-3.5">Application</th>
                                <th class="px-5 py-3.5">Student</th>
                                <th class="px-5 py-3.5">Type</th>
                                <th class="px-5 py-3.5">Amount</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="p in payments.data"
                                :key="p.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4 font-mono font-bold text-slate-900 dark:text-white">
                                    {{ p.receipt_number }}
                                </td>
                                <td class="px-5 py-4 font-mono font-semibold text-primary">
                                    <Link v-if="p.application" :href="`/dashboard/applications/${p.application.id}`" class="hover:underline">
                                        {{ p.application.application_id }}
                                    </Link>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium text-slate-900 dark:text-white block">
                                        {{ p.student ? (p.student.first_name + ' ' + (p.student.last_name || '')) : '-' }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ p.student?.phone }}</span>
                                </td>
                                <td class="px-5 py-4 capitalize text-slate-600 dark:text-slate-300">
                                    {{ p.payment_type }}
                                </td>
                                <td class="px-5 py-4 font-mono font-bold text-slate-900 dark:text-white">
                                    BDT {{ Number(p.amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider',
                                            p.payment_status === 'completed'
                                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50'
                                                : 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50'
                                        ]"
                                    >
                                        {{ p.payment_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <a
                                            :href="`/dashboard/payments/${p.id}/invoice`"
                                            target="_blank"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Receipt PDF
                                        </a>
                                        <Link
                                            :href="`/dashboard/payments/${p.id}/edit`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Payment
                                        </Link>
                                        <button
                                            type="button"
                                            @click="deletePayment(p.id, p.receipt_number)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Payment
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!payments.data || payments.data.length === 0">
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No payments found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="payments.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="payments.links" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
