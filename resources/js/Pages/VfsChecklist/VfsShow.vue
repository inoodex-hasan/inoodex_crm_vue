<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    application: Object,
})

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
        <Head :title="`VFS Details - ${application.application_id}`" />

        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">VFS Record Details</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Application {{ application.application_id }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard/vfs-checklist/overview"
                        class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"
                    >
                        Back to Overview
                    </Link>
                    <Link
                        :href="`/dashboard/vfs-checklist/overview/${application.id}/edit`"
                        class="px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl"
                    >
                        Edit Status
                    </Link>
                </div>
            </div>

            <!-- Details Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Outcome Status</span>
                    <span
                        :class="statusBadges[application.vfs_result] || 'bg-slate-100 text-slate-700'"
                        class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border"
                    >
                        {{ application.vfs_result || 'Pending' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-1">Student Name</span>
                        <span class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ application.student?.first_name }} {{ application.student?.last_name }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-1">Passport Number</span>
                        <span class="text-base font-mono font-medium text-slate-900 dark:text-white">
                            {{ application.student?.passport_number || 'N/A' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-1">University</span>
                        <span class="text-base font-medium text-slate-900 dark:text-white">
                            {{ application.university?.name || 'N/A' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-1">Country</span>
                        <span class="text-base font-medium text-slate-900 dark:text-white">
                            {{ application.university?.country?.name || 'N/A' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-1">Appointment Date</span>
                        <span class="text-base font-medium text-slate-900 dark:text-white">
                            {{ application.vfs_appointment_date ? new Date(application.vfs_appointment_date).toLocaleDateString() : 'Not Set' }}
                        </span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span class="block text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">VFS Notes & Findings</span>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/80 dark:border-slate-800 text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line min-h-[80px]">
                        {{ application.vfs_note || 'No notes entered for this appointment record.' }}
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
