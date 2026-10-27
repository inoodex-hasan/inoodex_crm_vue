<template>
    <Head :title="`Application: ${application.application_id}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <Link href="/dashboard/applications" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                        &larr; Back to Applications
                    </Link>
                    <div class="flex items-center space-x-3 mt-1">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ application.application_id }}
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                            {{ application.status }}
                        </span>
                        <span
                            v-if="application.visa_status"
                            :class="[
                                'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider',
                                application.visa_status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                application.visa_status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' :
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'
                            ]"
                        >
                            Visa: {{ application.visa_status }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <Link
                        :href="`/dashboard/vfs-checklist/${application.id}`"
                        class="px-4 py-2 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white transition shadow-xs flex items-center space-x-1"
                    >
                        <span>📋 VFS Document Checklist</span>
                    </Link>

                    <Link
                        :href="`/dashboard/applications/${application.id}/edit`"
                        class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-dark/40 transition"
                    >
                        Edit Application
                    </Link>

                    <a
                        :href="`/dashboard/applications/${application.id}/download-pdf`"
                        target="_blank"
                        class="px-4 py-2 text-xs font-semibold rounded-lg border border-emerald-500 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition"
                    >
                        📄 PDF Summary
                    </a>
                </div>
            </div>

            <!-- Two-Column Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Application Details & Student Snippet -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Student Summary Card -->
                    <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 pb-2 border-b border-gray-100 dark:border-gray-800">
                            Applicant Profile
                        </h3>

                        <div class="space-y-2 text-xs">
                            <div class="font-bold text-gray-900 dark:text-white text-sm">
                                {{ application.student?.first_name }} {{ application.student?.last_name }}
                            </div>
                            <div class="text-gray-600 dark:text-gray-400">
                                📞 {{ application.student?.phone }}
                            </div>
                            <div class="text-gray-600 dark:text-gray-400">
                                ✉ {{ application.student?.email }}
                            </div>
                            <div v-if="application.student?.passport_number" class="text-gray-600 dark:text-gray-400 font-mono">
                                Passport: {{ application.student?.passport_number }}
                            </div>
                            <div class="pt-2">
                                <Link
                                    :href="`/dashboard/students/${application.student_id}`"
                                    class="text-primary font-bold text-xs hover:underline"
                                >
                                    Open Full Student Profile &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Institutional Details Card -->
                    <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 pb-2 border-b border-gray-100 dark:border-gray-800">
                            Institution & Program
                        </h3>

                        <div class="space-y-2 text-xs">
                            <div>
                                <span class="text-gray-400 block">University:</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ application.university?.name || '—' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Country:</span>
                                <span class="font-semibold text-primary">{{ application.university?.country?.name || '—' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Course / Major:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ application.course?.name || 'General Admission' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Intake:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ application.intake?.intake_name || application.intake_month || '—' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Assigned Officer:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ application.creator?.name || 'Staff' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Milestone Progress & Financial Record -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Milestones Card -->
                    <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 pb-2 border-b border-gray-100 dark:border-gray-800">
                            Admissions & Visa Milestones
                        </h3>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                            <div class="p-3 rounded-lg bg-gray-50/60 dark:bg-dark/30 border border-gray-100 dark:border-gray-800">
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Offer Letter</span>
                                <span
                                    :class="application.offer_letter_received ? 'text-emerald-600 font-bold' : 'text-gray-400'"
                                    class="text-sm block mt-1"
                                >
                                    {{ application.offer_letter_received ? '✓ Received' : 'Pending' }}
                                </span>
                            </div>

                            <div class="p-3 rounded-lg bg-gray-50/60 dark:bg-dark/30 border border-gray-100 dark:border-gray-800">
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">VFS Appointment</span>
                                <span
                                    :class="application.vfs_appointment_date ? 'text-purple-600 font-bold' : 'text-gray-400'"
                                    class="text-sm block mt-1"
                                >
                                    {{ application.vfs_appointment_date || 'Not booked' }}
                                </span>
                            </div>

                            <div class="p-3 rounded-lg bg-gray-50/60 dark:bg-dark/30 border border-gray-100 dark:border-gray-800">
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">File Submission</span>
                                <span
                                    :class="application.file_submission ? 'text-blue-600 font-bold' : 'text-gray-400'"
                                    class="text-sm block mt-1"
                                >
                                    {{ application.file_submission ? '✓ Submitted' : 'Pending' }}
                                </span>
                            </div>

                            <div class="p-3 rounded-lg bg-gray-50/60 dark:bg-dark/30 border border-gray-100 dark:border-gray-800">
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Visa Decision</span>
                                <span
                                    :class="application.visa_status === 'approved' ? 'text-emerald-600 font-bold' : application.visa_status === 'rejected' ? 'text-red-600 font-bold' : 'text-gray-400'"
                                    class="text-sm block mt-1 capitalize"
                                >
                                    {{ application.visa_status || 'In progress' }}
                                </span>
                            </div>
                        </div>

                        <!-- Notes Area -->
                        <div v-if="application.notes" class="mt-4 p-3 rounded-lg bg-gray-50 dark:bg-dark/30 text-xs text-gray-700 dark:text-gray-300">
                            <strong>Counselor Remarks:</strong> {{ application.notes }}
                        </div>
                    </div>

                    <!-- Payments on this Application -->
                    <div class="bg-white dark:bg-[#1b2e4b] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <h3 class="font-bold text-gray-900 dark:text-white text-sm">
                                Payment Receipts
                            </h3>
                            <Link
                                :href="`/dashboard/payments/create?application_id=${application.id}`"
                                class="text-xs text-primary font-bold hover:underline"
                            >
                                + Record Payment
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-50/80 dark:bg-[#0e1726]/60 text-gray-400 uppercase font-semibold">
                                        <th class="py-2.5 px-4">Receipt #</th>
                                        <th class="py-2.5 px-4">Amount</th>
                                        <th class="py-2.5 px-4">Channel</th>
                                        <th class="py-2.5 px-4">Date</th>
                                        <th class="py-2.5 px-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                                    <tr v-for="pay in application.payments" :key="pay.id">
                                        <td class="py-2.5 px-4 font-mono font-bold text-primary">{{ pay.payment_number || ('PAY-' + pay.id) }}</td>
                                        <td class="py-2.5 px-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">${{ Number(pay.amount).toLocaleString() }}</td>
                                        <td class="py-2.5 px-4 capitalize">{{ pay.payment_method }}</td>
                                        <td class="py-2.5 px-4">{{ pay.payment_date }}</td>
                                        <td class="py-2.5 px-4 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">
                                                {{ pay.payment_status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="!application.payments?.length">
                                        <td colspan="5" class="p-6 text-center text-gray-400">
                                            No payment records logged for this application yet.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    application: {
        type: Object,
        required: true,
    },
});
</script>
