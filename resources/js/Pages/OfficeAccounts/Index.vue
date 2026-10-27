<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    accounts: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const accountType = ref(props.filters?.account_type || '')

function applyFilters() {
    router.get('/dashboard/office-accounts', {
        search: search.value || undefined,
        account_type: accountType.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    accountType.value = ''
    applyFilters()
}

function deleteAccount(id, name) {
    if (confirm(`Delete office account "${name}"?`)) {
        router.delete(`/dashboard/office-accounts/${id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Office Accounts" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Office Bank & Cash Accounts</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Manage bank accounts, mobile financial services (bKash/Nagad), and petty cash ledgers.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard/office-accounts/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Account
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
                            placeholder="Search name, account #..."
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
                        v-model="accountType"
                        class="form-select !w-auto max-w-fit shrink-0 px-3 py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    >
                        <option value="">All Types</option>
                        <option value="bank">Bank</option>
                        <option value="mfs">MFS (bKash/Nagad)</option>
                        <option value="cash">Cash in Hand</option>
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
                                <th class="px-5 py-3.5">Account Name</th>
                                <th class="px-5 py-3.5">Provider / Bank</th>
                                <th class="px-5 py-3.5">Account Number</th>
                                <th class="px-5 py-3.5">Branch</th>
                                <th class="px-5 py-3.5">Current Balance</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="acc in accounts.data"
                                :key="acc.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">
                                    {{ acc.account_name }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ acc.provider_name || 'N/A' }}
                                </td>
                                <td class="px-5 py-4 font-mono text-slate-600 dark:text-slate-300">
                                    {{ acc.account_number || '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ acc.branch_name || 'Main' }}
                                </td>
                                <td class="px-5 py-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    BDT {{ Number((acc.opening_balance || 0) + Number(acc.total_expense || 0) - Number(acc.total_income || 0)).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/office-accounts/${acc.id}/edit`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Account
                                        </Link>
                                        <button
                                            type="button"
                                            @click="deleteAccount(acc.id, acc.account_name)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Account
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!accounts.data || accounts.data.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No office accounts found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="accounts.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="accounts.links" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
