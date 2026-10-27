<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    commission: Object,
    otherClaims: Array,
})

const approveForm = useForm({
    amount: props.commission.proposed_amount || 0,
    review_notes: '',
})

const rejectForm = useForm({
    review_notes: '',
})

function submitApprove() {
    approveForm.post(`/dashboard/commissions/${props.commission.id}/approve`, {
        preserveScroll: true,
    })
}

function submitReject() {
    if (!rejectForm.review_notes) {
        alert('Please enter review notes explaining the rejection reason.')
        return
    }
    rejectForm.post(`/dashboard/commissions/${props.commission.id}/reject`, {
        preserveScroll: true,
    })
}
</script>

<template>
    <AdminLayout>
        <Head title="Review Commission Claim" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Review Commission Claim</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Verify application milestones, evaluate proposed amount, and make approval decision.</p>
                </div>
                <Link
                    href="/dashboard/commissions/pending"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to Pending
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Employee & Role Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-4">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Employee Details</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">Claimant Name</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ commission.user?.name || '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">Email</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ commission.user?.email || '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">Proposed Commission</span>
                                <span class="font-mono text-base font-bold text-primary">BDT {{ Number(commission.proposed_amount || 0).toLocaleString() }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">Claim Submission Date</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ commission.claimed_at ? new Date(commission.claimed_at).toLocaleString() : '-' }}</span>
                            </div>
                        </div>

                        <div v-if="commission.notes" class="pt-4 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-xs text-slate-400 block mb-1">Claimant Notes / Justification</span>
                            <p class="text-sm text-slate-700 dark:text-slate-300 italic bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                                {{ commission.notes }}
                            </p>
                        </div>
                    </div>

                    <!-- Application Info Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-4">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Application Information</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">Application ID</span>
                                <Link
                                    v-if="commission.application"
                                    :href="`/dashboard/applications/${commission.application.id}`"
                                    class="font-mono font-bold text-primary hover:underline"
                                >
                                    {{ commission.application.application_id }}
                                </Link>
                                <span v-else>-</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">Student</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">
                                    {{ commission.application?.student ? (commission.application.student.first_name + ' ' + (commission.application.student.last_name || '')) : '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">University</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ commission.application?.university?.name || '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 block mb-0.5">Course</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ commission.application?.course?.name || '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Other Claims on this application -->
                    <div v-if="otherClaims && otherClaims.length > 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-3">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Other Claims On This Application</h2>
                        <div class="space-y-2">
                            <div
                                v-for="oc in otherClaims"
                                :key="oc.id"
                                class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-sm"
                            >
                                <div>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ oc.user?.name }}</span>
                                    <span class="text-xs text-slate-400 ml-2">({{ oc.workflow_status }})</span>
                                </div>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">
                                    BDT {{ Number(oc.amount || oc.proposed_amount || 0).toLocaleString() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Decision Panels -->
                <div class="space-y-6">
                    <!-- Approve Form -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-emerald-200/80 dark:border-emerald-800/60 p-6 shadow-xs space-y-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-white">Approve Claim</h3>
                        </div>

                        <form @submit.prevent="submitApprove" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Approved Final Amount (BDT) <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    v-model="approveForm.amount"
                                    type="number"
                                    step="0.01"
                                    required
                                    class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-white"
                                />
                                <p v-if="approveForm.errors.amount" class="text-xs text-rose-500 mt-1">{{ approveForm.errors.amount }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Review Notes (Optional)
                                </label>
                                <textarea
                                    v-model="approveForm.review_notes"
                                    rows="3"
                                    placeholder="Add any verification remarks..."
                                    class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-white"
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                :disabled="approveForm.processing"
                                class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition shadow-xs disabled:opacity-50"
                            >
                                {{ approveForm.processing ? 'Approving...' : 'Approve for Payment' }}
                            </button>
                        </form>
                    </div>

                    <!-- Reject Form -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-rose-200/80 dark:border-rose-800/60 p-6 shadow-xs space-y-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-white">Reject Claim</h3>
                        </div>

                        <form @submit.prevent="submitReject" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Rejection Reason <span class="text-rose-500">*</span>
                                </label>
                                <textarea
                                    v-model="rejectForm.review_notes"
                                    rows="3"
                                    required
                                    placeholder="Explain why this claim was rejected so employee can correct..."
                                    class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 text-slate-900 dark:text-white"
                                ></textarea>
                                <p v-if="rejectForm.errors.review_notes" class="text-xs text-rose-500 mt-1">{{ rejectForm.errors.review_notes }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="rejectForm.processing"
                                class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm rounded-xl transition shadow-xs disabled:opacity-50"
                            >
                                {{ rejectForm.processing ? 'Rejecting...' : 'Reject Claim' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
