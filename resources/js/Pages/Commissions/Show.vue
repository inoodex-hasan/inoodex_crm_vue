<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    commission: Object,
})

function markAsPaid() {
    if (confirm('Mark this approved commission claim as paid?')) {
        router.post(`/dashboard/commissions/${props.commission.id}/mark-paid`, {}, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Commission Details" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Commission Details</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Complete audit trail and payout history.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        v-if="commission.workflow_status === 'approved'"
                        type="button"
                        @click="markAsPaid"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition shadow-xs"
                    >
                        Mark as Paid
                    </button>
                    <Link
                        href="/dashboard/commissions"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Back to Commissions
                    </Link>
                </div>
            </div>

            <!-- Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Employee</span>
                        <span class="text-base font-bold text-slate-900 dark:text-white">{{ commission.user?.name }}</span>
                        <span class="text-xs text-slate-500 block">{{ commission.user?.email }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Workflow Status</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/10 text-primary">
                            {{ commission.workflow_status }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Proposed Amount</span>
                        <span class="font-mono text-lg font-semibold text-slate-700 dark:text-slate-300">
                            BDT {{ Number(commission.proposed_amount || 0).toLocaleString() }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Final Approved Amount</span>
                        <span class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">
                            BDT {{ Number(commission.amount || 0).toLocaleString() }}
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
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">
                            {{ commission.application?.student ? (commission.application.student.first_name + ' ' + (commission.application.student.last_name || '')) : '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Reviewed By</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">
                            {{ commission.reviewer?.name || 'Not yet reviewed' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Reviewed At</span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">
                            {{ commission.reviewed_at ? new Date(commission.reviewed_at).toLocaleString() : '-' }}
                        </span>
                    </div>
                </div>

                <div v-if="commission.review_notes" class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-xs uppercase font-bold text-slate-400 block mb-2">Review Notes</span>
                    <p class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-800 leading-relaxed">
                        {{ commission.review_notes }}
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
