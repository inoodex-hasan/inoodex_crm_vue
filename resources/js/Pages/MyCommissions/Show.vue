<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    commission: Object,
})

function cancelClaim() {
    if (confirm('Cancel this pending commission claim?')) {
        router.post(`/dashboard/my-commissions/${props.commission.id}/cancel`)
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Claim Details" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Commission Claim Details</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Status, review trail, and audit information.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        v-if="commission.workflow_status === 'claimed'"
                        type="button"
                        @click="cancelClaim"
                        class="px-3.5 py-2 text-sm font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-400 rounded-xl transition"
                    >
                        Cancel Claim
                    </button>
                    <Link
                        href="/dashboard/my-commissions"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Back to My Commissions
                    </Link>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Status</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/10 text-primary">
                            {{ commission.workflow_status }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Claim Date</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                            {{ commission.claimed_at ? new Date(commission.claimed_at).toLocaleString() : '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Proposed Amount</span>
                        <span class="font-mono text-lg font-bold text-slate-800 dark:text-slate-200">
                            BDT {{ Number(commission.proposed_amount || 0).toLocaleString() }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Approved Amount</span>
                        <span class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">
                            {{ commission.amount > 0 ? ('BDT ' + Number(commission.amount).toLocaleString()) : 'Pending Review' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Application</span>
                        <Link
                            v-if="commission.application"
                            :href="`/dashboard/applications/${commission.application.id}`"
                            class="font-mono font-bold text-primary hover:underline text-sm"
                        >
                            {{ commission.application.application_id }}
                        </Link>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Student</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                            {{ commission.application?.student ? (commission.application.student.first_name + ' ' + (commission.application.student.last_name || '')) : '-' }}
                        </span>
                    </div>
                </div>

                <div v-if="commission.notes || commission.claim_notes" class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-xs uppercase font-bold text-slate-400 block mb-1.5">My Submission Notes</span>
                    <p class="text-sm text-slate-700 dark:text-slate-300 italic bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                        {{ commission.notes || commission.claim_notes }}
                    </p>
                </div>

                <div v-if="commission.review_notes" class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-xs uppercase font-bold text-slate-400 block mb-1.5">Reviewer Feedback</span>
                    <p class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                        {{ commission.review_notes }}
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
