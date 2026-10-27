<template>
    <Head title="Create University Application" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link href="/dashboard/applications" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                        &larr; Back to Applications
                    </Link>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">
                        Submit University Application
                    </h1>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-[#1b2e4b] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Student Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Applicant Student <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.student_id"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                :class="{ 'border-red-500': form.errors.student_id }"
                                required
                            >
                                <option value="">Select Enrolled Student</option>
                                <option v-for="s in students" :key="s.id" :value="s.id">
                                    {{ s.first_name }} {{ s.last_name }} ({{ s.email }} / {{ s.phone }})
                                </option>
                            </select>
                            <p v-if="form.errors.student_id" class="text-red-500 text-xs mt-1">{{ form.errors.student_id }}</p>
                        </div>

                        <!-- University Selection -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Target University
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

                        <!-- Admission Stage / Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Initial Stage <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                required
                            >
                                <option value="counseling">Counseling in Progress</option>
                                <option value="payment">Payment Complete</option>
                                <option value="application">Application Submitted</option>
                                <option value="offer">Offer Letter Received</option>
                                <option value="visa">Visa Processing</option>
                                <option value="enrolled">Fully Enrolled</option>
                            </select>
                        </div>

                        <!-- Priority Level -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Application Urgency
                            </label>
                            <select
                                v-model="form.application_priority"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="normal">Normal</option>
                                <option value="priority">Priority</option>
                                <option value="vip">VIP Intake</option>
                            </select>
                        </div>

                        <!-- Initial Visa Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Visa Progress Status
                            </label>
                            <select
                                v-model="form.visa_status"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="not_applied">Not Applied</option>
                                <option value="pending">Pending Embassy Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Notes & Application Comments
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                placeholder="Special requirements, scholarship details, conditions on offer..."
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <Link
                            href="/dashboard/applications"
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs flex items-center space-x-1"
                        >
                            <span>{{ form.processing ? 'Submitting...' : 'Submit Application' }}</span>
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
    students: {
        type: Array,
        default: () => [],
    },
    universities: {
        type: Array,
        default: () => [],
    },
    selected_student: {
        type: [Number, String, null],
        default: null,
    },
});

const form = useForm({
    student_id: props.selected_student || '',
    university_id: '',
    status: 'application',
    application_priority: 'normal',
    visa_status: 'not_applied',
    notes: '',
});

function submit() {
    form.post('/dashboard/applications');
}
</script>
