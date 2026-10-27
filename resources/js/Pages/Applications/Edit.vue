<template>
    <Head :title="`Edit Application: ${application.application_id}`" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="`/dashboard/applications/${application.id}`" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                        &larr; Back to Application
                    </Link>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">
                        Edit Application & Progress
                    </h1>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-[#1b2e4b] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- University -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                University
                            </label>
                            <select
                                v-model="form.university_id"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="">Select University</option>
                                <option v-for="u in universities" :key="u.id" :value="u.id">
                                    {{ u.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Stage / Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Admission Stage <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                required
                            >
                                <option value="counseling">Counseling in Progress</option>
                                <option value="payment">Payment Complete</option>
                                <option value="application">Application Submitted</option>
                                <option value="offer">Offer Received</option>
                                <option value="visa">Visa Processing</option>
                                <option value="enrolled">Enrolled</option>
                            </select>
                        </div>

                        <!-- Visa Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Visa Progress
                            </label>
                            <select
                                v-model="form.visa_status"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="not_applied">Not Applied</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- VFS Appointment Date -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                VFS Appointment Date
                            </label>
                            <input
                                v-model="form.vfs_appointment_date"
                                type="date"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>

                        <!-- Offer Letter Flag -->
                        <div class="flex items-center space-x-2 pt-4">
                            <input
                                v-model="form.offer_letter_received"
                                type="checkbox"
                                id="offer_letter"
                                class="w-4 h-4 rounded text-primary border-gray-300 focus:ring-primary/20"
                            />
                            <label for="offer_letter" class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300 cursor-pointer">
                                Offer Letter Received from University
                            </label>
                        </div>

                        <!-- File Submission Flag -->
                        <div class="flex items-center space-x-2 pt-4">
                            <input
                                v-model="form.file_submission"
                                type="checkbox"
                                id="file_sub"
                                class="w-4 h-4 rounded text-primary border-gray-300 focus:ring-primary/20"
                            />
                            <label for="file_sub" class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300 cursor-pointer">
                                Files Fully Submitted to Embassy / VFS
                            </label>
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Counselor Notes & Updates
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <Link
                            :href="`/dashboard/applications/${application.id}`"
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Application Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
    universities: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    university_id: props.application.university_id || '',
    status: props.application.status || 'application',
    visa_status: props.application.visa_status || 'not_applied',
    vfs_appointment_date: props.application.vfs_appointment_date || '',
    offer_letter_received: !!props.application.offer_letter_received,
    file_submission: !!props.application.file_submission,
    notes: props.application.notes || '',
});

function submit() {
    form.put(`/dashboard/applications/${props.application.id}`);
}
</script>
