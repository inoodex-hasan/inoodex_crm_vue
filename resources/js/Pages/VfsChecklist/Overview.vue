<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    applications: Array,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const vfsResult = ref(props.filters?.vfs_result || '')

function filter() {
    router.get('/dashboard/vfs-checklist/overview', {
        search: search.value,
        vfs_result: vfsResult.value,
    }, { preserveState: true })
}

function clearFilter() {
    search.value = ''
    vfsResult.value = ''
    router.get('/dashboard/vfs-checklist/overview')
}

const statusBadges = {
    submitted: 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200',
    not_submitted: 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200',
    not_attended: 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200',
    rejected: 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200',
    approved: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200',
}
</script>

<template>
    <AdminLayout>
        <Head title="VFS Overview & Tracking" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">VFS Overview & Tracking</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Monitor student appointment schedules and outcomes.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard/vfs-checklist"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Checklist Index
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3 shadow-xs">
                <form @submit.prevent="filter" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-64 shrink-0">
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Search Application, Student..."
                            class="w-full ltr:pr-9 rtl:pl-9 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white"
                        />
                        <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                    </div>
                    <select
                        v-model="vfsResult"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                        <option value="">All Statuses</option>
                        <option value="submitted">Submitted</option>
                        <option value="not_submitted">Not Submitted</option>
                        <option value="not_attended">Not Attended</option>
                        <option value="rejected">Rejected</option>
                        <option value="approved">Approved</option>
                    </select>
                    <div class="flex items-center gap-2 shrink-0">
                        <button
                            type="submit"
                            class="px-3 py-2 text-xs font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition shadow-xs shrink-0"
                        >
                            Filter
                        </button>
                        <button
                            v-if="search || vfsResult"
                            type="button"
                            @click="clearFilter"
                            class="px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg transition shrink-0"
                        >
                            Clear
                        </button>
                    </div>
                </form>
            </div>

            <!-- Applications Table -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="px-5 py-4">Application</th>
                                <th class="px-5 py-4">Student</th>
                                <th class="px-5 py-4">Passport</th>
                                <th class="px-5 py-4">Destination University</th>
                                <th class="px-5 py-4 text-center">Appointment Date</th>
                                <th class="px-5 py-4 text-center">Status</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="app in applications"
                                :key="app.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition"
                            >
                                <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">
                                    {{ app.application_id }}
                                    <div class="text-[10px] text-primary font-bold uppercase mt-0.5">
                                        Attempt #{{ app.total_vfs_attempts || 1 }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-900 dark:text-white">
                                        {{ app.student?.first_name }} {{ app.student?.last_name }}
                                    </div>
                                    <div class="text-xs text-slate-400">{{ app.student?.email }}</div>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs">{{ app.student?.passport_number || 'N/A' }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-900 dark:text-white">{{ app.university?.name || 'N/A' }}</div>
                                    <div class="text-xs text-slate-400">{{ app.university?.country?.name }}</div>
                                </td>
                                <td class="px-5 py-4 text-center text-xs">
                                    <span :class="{'text-rose-500 font-bold': !app.vfs_result && app.vfs_appointment_date}">
                                        {{ app.vfs_appointment_date ? new Date(app.vfs_appointment_date).toLocaleDateString() : 'Not Set' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        :class="statusBadges[app.vfs_result] || 'bg-slate-100 text-slate-700'"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider border"
                                    >
                                        {{ app.vfs_result || 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    <Link
                                        :href="`/dashboard/vfs-checklist/${app.id}`"
                                        class="text-xs font-semibold text-primary hover:underline"
                                    >
                                        Checklist
                                    </Link>
                                    <Link
                                        :href="`/dashboard/vfs-checklist/overview/${app.id}`"
                                        class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:underline"
                                    >
                                        Details
                                    </Link>
                                    <Link
                                        :href="`/dashboard/vfs-checklist/overview/${app.id}/edit`"
                                        class="text-xs font-semibold text-amber-600 hover:underline"
                                    >
                                        Edit
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="applications.length === 0">
                                <td colspan="7" class="text-center py-8 text-slate-400">
                                    No records found matching your filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
