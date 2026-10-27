<template>
    <Head :title="`Lead: ${lead.student_name}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <Link href="/dashboard/marketing/leads" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                        &larr; Back to Leads
                    </Link>
                    <div class="flex items-center space-x-3 mt-1">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ lead.student_name }}
                        </h1>
                        <span
                            :class="[
                                'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider',
                                lead.priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' :
                                lead.priority === 'medium' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' :
                                'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                            ]"
                        >
                            {{ lead.priority || 'Normal' }} Priority
                        </span>
                        <span
                            :class="[
                                'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider',
                                lead.status === 'converted' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                'bg-gray-100 text-gray-700 dark:bg-dark dark:text-gray-300'
                            ]"
                        >
                            {{ lead.status || 'New' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <Link
                        :href="`/dashboard/marketing/leads/${lead.id}/edit`"
                        class="px-4 py-2 text-xs font-bold rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-dark/40 transition"
                    >
                        Edit Lead
                    </Link>
                    <Link
                        v-if="lead.status !== 'converted'"
                        :href="`/dashboard/students/create?lead_id=${lead.id}&name=${encodeURIComponent(lead.student_name)}&phone=${encodeURIComponent(lead.phone)}&email=${encodeURIComponent(lead.email || '')}`"
                        class="px-4 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-xs flex items-center space-x-1"
                    >
                        <span>✓ Convert to Student Profile</span>
                    </Link>
                </div>
            </div>

            <!-- 2-Column Info & Timeline Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Lead Profile Details -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2">
                            Prospect Details
                        </h3>

                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-gray-400 block font-medium">Contact Phone:</span>
                                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ lead.phone }}</span>
                            </div>

                            <div v-if="lead.email">
                                <span class="text-gray-400 block font-medium">Email Address:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ lead.email }}</span>
                            </div>

                            <div v-if="lead.current_education">
                                <span class="text-gray-400 block font-medium">Academic Background:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ lead.current_education }}</span>
                            </div>

                            <div>
                                <span class="text-gray-400 block font-medium">Preferred Destination:</span>
                                <span class="font-bold text-primary text-sm">
                                    {{ lead.country?.name || 'Not specified' }}
                                </span>
                            </div>

                            <div v-if="lead.course">
                                <span class="text-gray-400 block font-medium">Intended Program:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ lead.course?.name }}</span>
                            </div>

                            <div>
                                <span class="text-gray-400 block font-medium">Acquisition Channel:</span>
                                <span class="font-semibold capitalize text-gray-800 dark:text-gray-200">{{ lead.source || 'Direct' }}</span>
                            </div>

                            <div>
                                <span class="text-gray-400 block font-medium">Lead Captured By:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ lead.creator?.name || 'Staff' }}</span>
                            </div>

                            <div>
                                <span class="text-gray-400 block font-medium">Date Registered:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ formatDate(lead.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Follow-up Timeline & Notes -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Quick Follow-up Form -->
                    <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            Log Follow-Up & Counseling Activity
                        </h3>

                        <form @submit.prevent="submitFollowUp" class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Update Status</label>
                                    <select
                                        v-model="followUpForm.status"
                                        class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-1 focus:ring-primary"
                                    >
                                        <option value="new">New</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="counseling">Counseling in Progress</option>
                                        <option value="converted">Converted to Student</option>
                                        <option value="lost">Lost / Not Interested</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Next Follow-Up Due</label>
                                    <input
                                        v-model="followUpForm.next_follow_up_at"
                                        type="datetime-local"
                                        class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-1 focus:ring-primary"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Call / Meeting Summary</label>
                                <textarea
                                    v-model="followUpForm.notes"
                                    rows="2"
                                    placeholder="Discussed university requirements, student promised to send passport copy on Monday..."
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-1 focus:ring-primary"
                                    required
                                />
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="followUpForm.processing"
                                    class="px-4 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs"
                                >
                                    {{ followUpForm.processing ? 'Recording...' : 'Record Activity' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Follow-up Timeline List -->
                    <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            Activity & Follow-Up History
                        </h3>

                        <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200 dark:before:bg-gray-700">
                            <!-- Items from follow_up_history -->
                            <div
                                v-for="(log, idx) in parsedHistory"
                                :key="idx"
                                class="relative"
                            >
                                <div class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-primary ring-4 ring-white dark:ring-dark" />
                                <div class="text-xs">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-gray-900 dark:text-white">
                                            {{ log.author || 'Counselor' }}
                                        </span>
                                        <span class="text-gray-400 text-[10px]">
                                            {{ formatDate(log.timestamp || log.created_at) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-dark/40 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                                        {{ log.note || log.notes }}
                                    </p>
                                </div>
                            </div>

                            <!-- Initial Note -->
                            <div v-if="lead.notes" class="relative">
                                <div class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-gray-400 ring-4 ring-white dark:ring-dark" />
                                <div class="text-xs">
                                    <span class="font-bold text-gray-900 dark:text-white">Initial Note</span>
                                    <p class="mt-1 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-dark/40 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                                        {{ lead.notes }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="!parsedHistory.length && !lead.notes" class="text-gray-400 text-xs py-4">
                                No activity logged yet.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    lead: {
        type: Object,
        required: true,
    },
});

const parsedHistory = computed(() => {
    if (!props.lead.follow_up_history) return [];
    if (Array.isArray(props.lead.follow_up_history)) return props.lead.follow_up_history;
    try {
        return JSON.parse(props.lead.follow_up_history);
    } catch (e) {
        return [];
    }
});

const followUpForm = useForm({
    student_name: props.lead.student_name,
    phone: props.lead.phone,
    email: props.lead.email || '',
    current_education: props.lead.current_education || '',
    preferred_country: props.lead.preferred_country || '',
    preferred_course: props.lead.preferred_course || '',
    source: props.lead.source || '',
    priority: props.lead.priority || 'medium',
    status: props.lead.status || 'contacted',
    next_follow_up_at: props.lead.next_follow_up_at || '',
    notes: '',
});

function submitFollowUp() {
    followUpForm.put(`/dashboard/marketing/leads/${props.lead.id}`, {
        onSuccess: () => {
            followUpForm.notes = '';
        },
    });
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
</script>
