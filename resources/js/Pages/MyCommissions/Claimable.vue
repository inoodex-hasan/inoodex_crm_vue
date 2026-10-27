<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    applications: Object,
})
</script>

<template>
    <AdminLayout>
        <Head title="Claimable Applications" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Claimable Applications</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Applications assigned to your workflow where you have not yet claimed commission.</p>
                </div>
                <Link
                    href="/dashboard/my-commissions"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to My Dashboard
                </Link>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                                <th class="px-5 py-3.5">Application ID</th>
                                <th class="px-5 py-3.5">Student</th>
                                <th class="px-5 py-3.5">University</th>
                                <th class="px-5 py-3.5">Course</th>
                                <th class="px-5 py-3.5">Intake</th>
                                <th class="px-5 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="app in applications.data"
                                :key="app.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4 font-mono font-bold text-primary">
                                    {{ app.application_id }}
                                </td>
                                <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">
                                    {{ app.student ? (app.student.first_name + ' ' + (app.student.last_name || '')) : '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ app.university?.name || '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ app.course?.name || '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ app.intake?.name || '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/my-commissions/applications/${app.id}/claim`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Claim Commission
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
                            <tr v-if="!applications.data || applications.data.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No claimable applications available at this time.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="applications.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="applications.links" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
