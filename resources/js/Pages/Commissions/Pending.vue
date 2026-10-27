<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    commissions: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const role = ref(props.filters?.role || '')

function applyFilters() {
    router.get('/dashboard/commissions/pending', {
        search: search.value || undefined,
        role: role.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    role.value = ''
    applyFilters()
}
</script>

<template>
    <AdminLayout>
        <Head title="Pending Commission Claims" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Pending Commission Claims</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Claims awaiting accountant verification and approval.</p>
                </div>
                <Link
                    href="/dashboard/commissions"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to All Commissions
                </Link>
            </div>

            <!-- Filter Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-xs">
                <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-60 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search employee, student..."
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
                        v-model="role"
                        class="form-select !w-auto max-w-fit shrink-0 px-3 py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    >
                        <option value="">All Roles</option>
                        <option value="marketing">Marketing</option>
                        <option value="consultant">Consultant</option>
                        <option value="application">Application Staff</option>
                        <option value="creator">Creator</option>
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
                                <th class="px-5 py-3.5">Employee</th>
                                <th class="px-5 py-3.5">Proposed Amount</th>
                                <th class="px-5 py-3.5">Claimed Date</th>
                                <th class="px-5 py-3.5 text-right">Action</th>
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
                                <td class="px-5 py-4 text-slate-700 dark:text-slate-300">
                                    {{ c.user?.name || '-' }}
                                </td>
                                <td class="px-5 py-4 font-mono font-semibold text-slate-900 dark:text-white">
                                    BDT {{ Number(c.proposed_amount || 0).toLocaleString() }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ c.claimed_at ? new Date(c.claimed_at).toLocaleDateString() : '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/commissions/${c.id}/review`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            Review Claim
                                        </Link>
                                        <Link
                                            v-if="c.application"
                                            :href="`/dashboard/applications/${c.application.id}`"
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
                            <tr v-if="!commissions.data || commissions.data.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No pending commission claims found.
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
