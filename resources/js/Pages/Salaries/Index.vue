<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    salaries: Object,
    stats: Object,
    selectedMonth: String,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status || '')
const month = ref(props.selectedMonth || '')

function applyFilters() {
    router.get('/dashboard/salaries', {
        search: search.value || undefined,
        status: status.value || undefined,
        month: month.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    status.value = ''
    month.value = ''
    applyFilters()
}

function deleteSalary(id, name) {
    if (confirm(`Delete salary record for "${name}"?`)) {
        router.delete(`/dashboard/salaries/${id}`, {
            preserveScroll: true,
        })
    }
}

const statusBadgeClasses = {
    pending: 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/50',
    partial: 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50',
    paid: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50',
}
</script>

<template>
    <AdminLayout>
        <Head title="Salary Management" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Staff Payroll & Salaries</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Manage monthly staff compensation, bank disbursements, and pay slips.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        :href="`/dashboard/salaries/export-excel?month=${month || ''}`"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Export Excel
                    </a>
                    <Link
                        href="/dashboard/salaries/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Salary
                    </Link>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-slate-400">Total Payroll</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        BDT {{ Number(stats?.total_salaries || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-emerald-500">Disbursed (Paid)</span>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                        BDT {{ Number(stats?.total_paid || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-rose-500">Pending Payable</span>
                    <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">
                        BDT {{ Number(stats?.total_pending || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                    <span class="text-xs uppercase font-bold text-amber-500">Partial Due</span>
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                        BDT {{ Number(stats?.total_partial || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-xs">
                <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-60 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search employee, email..."
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
                        <option value="pending">Pending</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                    </select>
                    <input
                        v-model="month"
                        type="month"
                        class="w-auto shrink-0 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    />
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
                                <th class="px-5 py-3.5">Employee</th>
                                <th class="px-5 py-3.5">Month</th>
                                <th class="px-5 py-3.5">Basic Salary</th>
                                <th class="px-5 py-3.5">Net Salary</th>
                                <th class="px-5 py-3.5">Paid Amount</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="s in salaries.data"
                                :key="s.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-900 dark:text-white block">
                                        {{ s.employee_name || s.user?.name || '-' }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ s.user?.email }}</span>
                                </td>
                                <td class="px-5 py-4 font-mono text-slate-600 dark:text-slate-300 text-xs">
                                    {{ s.salary_month || '-' }}
                                </td>
                                <td class="px-5 py-4 font-mono text-slate-700 dark:text-slate-300">
                                    BDT {{ Number(s.basic_salary || 0).toLocaleString() }}
                                </td>
                                <td class="px-5 py-4 font-mono font-bold text-slate-900 dark:text-white">
                                    BDT {{ Number(s.net_salary || 0).toLocaleString() }}
                                </td>
                                <td class="px-5 py-4 font-mono font-semibold text-emerald-600 dark:text-emerald-400">
                                    BDT {{ Number(s.paid_amount || 0).toLocaleString() }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider',
                                            statusBadgeClasses[s.payment_status] || 'bg-slate-100 text-slate-600'
                                        ]"
                                    >
                                        {{ s.payment_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/salaries/${s.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </Link>
                                        <Link
                                            :href="`/dashboard/salaries/${s.id}/edit`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Salary
                                        </Link>
                                        <button
                                            type="button"
                                            @click="deleteSalary(s.id, s.employee_name)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Salary
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!salaries.data || salaries.data.length === 0">
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No salary records found for this period.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="salaries.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="salaries.links" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
