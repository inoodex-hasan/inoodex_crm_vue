<template>
    <Head title="Capture New Lead" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link href="/dashboard/marketing/leads" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                        &larr; Back to Leads
                    </Link>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">
                        Capture Prospective Student Lead
                    </h1>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-[#1b2e4b] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Student Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Student Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Student Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.student_name"
                                type="text"
                                placeholder="Full Name"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                :class="{ 'border-red-500': form.errors.student_name }"
                                required
                            />
                            <p v-if="form.errors.student_name" class="text-red-500 text-xs mt-1">
                                {{ form.errors.student_name }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Contact Phone <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.phone"
                                type="text"
                                placeholder="e.g. +8801700000000"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                :class="{ 'border-red-500': form.errors.phone }"
                                required
                            />
                            <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Email Address
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="student@example.com"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>

                        <!-- Current Education -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Current Academic Level / Degree
                            </label>
                            <input
                                v-model="form.current_education"
                                type="text"
                                placeholder="e.g. HSC (GPA 5.0) / BBA"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>

                        <!-- Preferred Country -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Preferred Study Destination
                            </label>
                            <select
                                v-model="form.preferred_country"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="">Select Destination Country</option>
                                <option v-for="c in countries" :key="c.id" :value="c.id">
                                    {{ c.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Preferred Course -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Intended Course / Major
                            </label>
                            <select
                                v-model="form.preferred_course"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="">Select Intended Course</option>
                                <option v-for="crs in courses" :key="crs.id" :value="crs.id">
                                    {{ crs.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Lead Source -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Acquisition Source
                            </label>
                            <select
                                v-model="form.source"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="facebook">Facebook Ad / Page</option>
                                <option value="website">Direct Website Form</option>
                                <option value="walk_in">Office Walk-In</option>
                                <option value="referral">Student / Agent Referral</option>
                                <option value="phone">Inbound Phone Call</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Priority Level
                            </label>
                            <select
                                v-model="form.priority"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="low">Low (General Inquiry)</option>
                                <option value="medium">Medium (Ready for Counseling)</option>
                                <option value="high">High (Immediate Intake / Passport Ready)</option>
                            </select>
                        </div>

                        <!-- Next Follow-up Date -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Scheduled Next Follow-Up Date & Time
                            </label>
                            <input
                                v-model="form.next_follow_up_at"
                                type="datetime-local"
                                class="w-full md:w-1/2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>

                        <!-- Initial Counseling Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Initial Counseling Remarks & Background
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                placeholder="Student interests, budget constraints, English score, family sponsor info..."
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <Link
                            href="/dashboard/marketing/leads"
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark/40 transition"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs flex items-center space-x-1"
                        >
                            <span>{{ form.processing ? 'Saving...' : 'Save Lead' }}</span>
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

defineProps({
    countries: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    student_name: '',
    phone: '',
    email: '',
    current_education: '',
    preferred_country: '',
    preferred_course: '',
    source: 'facebook',
    priority: 'medium',
    next_follow_up_at: '',
    notes: '',
});

function submit() {
    form.post('/dashboard/marketing/leads');
}
</script>
