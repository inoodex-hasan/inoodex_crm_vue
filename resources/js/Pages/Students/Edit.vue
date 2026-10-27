<template>
    <Head :title="`Edit Student: ${student.first_name} ${student.last_name}`" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="`/dashboard/students/${student.id}`" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                        &larr; Back to Student Profile
                    </Link>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">
                        Edit Student Record
                    </h1>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-[#1b2e4b] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                :class="{ 'border-red-500': form.errors.first_name }"
                                required
                            />
                            <p v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</p>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                :class="{ 'border-red-500': form.errors.last_name }"
                                required
                            />
                            <p v-if="form.errors.last_name" class="text-red-500 text-xs mt-1">{{ form.errors.last_name }}</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.phone"
                                type="text"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                :class="{ 'border-red-500': form.errors.phone }"
                                required
                            />
                            <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                :class="{ 'border-red-500': form.errors.email }"
                                required
                            />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>

                        <!-- Passport -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Passport Number
                            </label>
                            <input
                                v-model="form.passport_number"
                                type="text"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>

                        <!-- Passport Expiry -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Passport Expiry Date
                            </label>
                            <input
                                v-model="form.passport_expiry_date"
                                type="date"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>

                        <!-- Country -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Country
                            </label>
                            <select
                                v-model="form.country_id"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="">Select Country</option>
                                <option v-for="c in countries" :key="c.id" :value="c.id">
                                    {{ c.name }}
                                </option>
                            </select>
                        </div>

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

                        <!-- Stage -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Current Stage
                            </label>
                            <select
                                v-model="form.current_stage"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="lead">Lead</option>
                                <option value="counseling">Counseling</option>
                                <option value="application">Application Submitted</option>
                                <option value="offer">Offer Received</option>
                                <option value="visa">Visa Processing</option>
                                <option value="enrolled">Enrolled</option>
                            </select>
                        </div>

                        <!-- Package Amount -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">
                                Package Amount ($ / BDT)
                            </label>
                            <input
                                v-model="form.package_amount"
                                type="number"
                                step="0.01"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            />
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <Link
                            :href="`/dashboard/students/${student.id}`"
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
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
    student: {
        type: Object,
        required: true,
    },
    countries: {
        type: Array,
        default: () => [],
    },
    universities: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    first_name: props.student.first_name,
    last_name: props.student.last_name,
    phone: props.student.phone,
    email: props.student.email,
    passport_number: props.student.passport_number || '',
    passport_expiry_date: props.student.passport_expiry_date || '',
    country_id: props.student.country_id || '',
    university_id: props.student.university_id || '',
    current_stage: props.student.current_stage || 'application',
    package_amount: props.student.package_amount || '',
});

function submit() {
    form.put(`/dashboard/students/${props.student.id}`);
}
</script>
