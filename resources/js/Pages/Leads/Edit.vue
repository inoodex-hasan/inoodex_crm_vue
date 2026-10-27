<template>
    <Head :title="`Edit Lead: ${lead.student_name}`" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="`/dashboard/marketing/leads/${lead.id}`" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                        &larr; Back to Lead
                    </Link>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">
                        Edit Lead Information
                    </h1>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-[#1b2e4b] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Student Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Student Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.student_name"
                                type="text"
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

                        <!-- Priority -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Priority Level
                            </label>
                            <select
                                v-model="form.priority"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Lifecycle Status
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="new">New</option>
                                <option value="contacted">Contacted</option>
                                <option value="counseling">Counseling in Progress</option>
                                <option value="converted">Converted to Student</option>
                                <option value="lost">Lost / Not Interested</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <Link
                            :href="`/dashboard/marketing/leads/${lead.id}`"
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Lead' }}
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
    lead: {
        type: Object,
        required: true,
    },
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
    student_name: props.lead.student_name,
    phone: props.lead.phone,
    email: props.lead.email || '',
    current_education: props.lead.current_education || '',
    preferred_country: props.lead.preferred_country || '',
    preferred_course: props.lead.preferred_course || '',
    source: props.lead.source || '',
    priority: props.lead.priority || 'medium',
    status: props.lead.status || 'new',
    next_follow_up_at: props.lead.next_follow_up_at || '',
    notes: '',
});

function submit() {
    form.put(`/dashboard/marketing/leads/${props.lead.id}`);
}
</script>
