<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    application: Object,
    rejectedClaim: Object,
})

const form = useForm({
    proposed_amount: props.rejectedClaim?.proposed_amount || '',
    claim_notes: props.rejectedClaim?.claim_notes || '',
})

function submit() {
    form.post(`/dashboard/my-commissions/applications/${props.application.id}/claim`)
}
</script>

<template>
    <AdminLayout>
        <Head title="Submit Commission Claim" />

        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Submit Commission Claim</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Request payout for successful enrollment milestone.</p>
                </div>
                <Link
                    href="/dashboard/my-commissions/claimable"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to Claimable List
                </Link>
            </div>

            <!-- Application Summary Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Application Reference</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block mb-0.5">Application ID</span>
                        <span class="font-mono font-bold text-primary">{{ application.application_id }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block mb-0.5">Student</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ application.student ? (application.student.first_name + ' ' + (application.student.last_name || '')) : '-' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block mb-0.5">University</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ application.university?.name || '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block mb-0.5">Course</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ application.course?.name || '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Rejection Notice if applicable -->
            <div v-if="rejectedClaim" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm">
                    <p class="font-bold text-rose-800 dark:text-rose-200">Previous Claim Rejected</p>
                    <p class="text-rose-700 dark:text-rose-300 mt-1">
                        <b>Accountant Feedback:</b> {{ rejectedClaim.review_notes }}
                    </p>
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">
                        Please adjust your proposed amount or provide additional justification below.
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        Proposed Amount (BDT) <span class="text-rose-500">*</span>
                    </label>
                    <input
                        v-model="form.proposed_amount"
                        type="number"
                        step="0.01"
                        required
                        placeholder="e.g. 5000.00"
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    />
                    <p v-if="form.errors.proposed_amount" class="text-xs text-rose-500 mt-1.5">{{ form.errors.proposed_amount }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        Notes & Justification
                    </label>
                    <textarea
                        v-model="form.claim_notes"
                        rows="4"
                        placeholder="Detail your contribution in counseling, documentation, or conversion..."
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    ></textarea>
                    <p v-if="form.errors.claim_notes" class="text-xs text-rose-500 mt-1.5">{{ form.errors.claim_notes }}</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-xs text-slate-500 space-y-1">
                    <p class="font-bold text-slate-700 dark:text-slate-300">Policy Notice:</p>
                    <p>Submitted claims enter the Accountant Review queue. Approved claims will be credited upon tuition disbursement.</p>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <Link
                        href="/dashboard/my-commissions/claimable"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-xs disabled:opacity-50"
                    >
                        {{ form.processing ? 'Submitting...' : 'Submit Claim for Review' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
