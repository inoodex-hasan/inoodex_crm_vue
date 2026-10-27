<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    application: Object,
})

const form = useForm({
    vfs_result: props.application.vfs_result || 'submitted',
    vfs_note: props.application.vfs_note || '',
})

function submit() {
    form.post(`/dashboard/vfs-checklist/${props.application.id}/vfs-result`)
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Update VFS Outcome - ${application.application_id}`" />

        <div class="max-w-3xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Update VFS Outcome</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Application {{ application.application_id }}</p>
                </div>
                <Link
                    href="/dashboard/vfs-checklist/overview"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"
                >
                    Back to Overview
                </Link>
            </div>

            <!-- Student Summary Header -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="block text-slate-400 font-bold uppercase mb-0.5">Student</span>
                    <span class="font-semibold text-slate-900 dark:text-white">{{ application.student?.first_name }} {{ application.student?.last_name }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase mb-0.5">Passport</span>
                    <span class="font-mono font-medium text-slate-900 dark:text-white">{{ application.student?.passport_number || 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase mb-0.5">University</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ application.university?.name || 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase mb-0.5">VFS Date</span>
                    <span class="font-medium text-primary">{{ application.vfs_appointment_date ? new Date(application.vfs_appointment_date).toLocaleDateString() : 'Not Set' }}</span>
                </div>
            </div>

            <!-- Edit Form -->
            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        VFS Result <span class="text-rose-500">*</span>
                    </label>
                    <select
                        v-model="form.vfs_result"
                        required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                    >
                        <option value="submitted">Submitted</option>
                        <option value="not_submitted">Not Submitted</option>
                        <option value="not_attended">Not Attended</option>
                        <option value="rejected">Rejected</option>
                        <option value="approved">Approved</option>
                    </select>
                    <p v-if="form.errors.vfs_result" class="text-xs text-rose-500 mt-1.5">{{ form.errors.vfs_result }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        VFS Notes / Decision Details / Remarks
                    </label>
                    <textarea
                        v-model="form.vfs_note"
                        rows="5"
                        placeholder="Provide reasons, consular remarks, or updates..."
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <Link
                        href="/dashboard/vfs-checklist/overview"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        {{ form.processing ? 'Saving...' : 'Update VFS Status' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
