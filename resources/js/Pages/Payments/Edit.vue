<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    payment: Object,
    applications: Array,
    users: Array,
    accounts: Array,
})

const form = useForm({
    amount: props.payment.amount || '',
    payment_type: props.payment.payment_type || 'advance',
    payment_status: props.payment.payment_status || 'completed',
    payment_date: props.payment.payment_date ? props.payment.payment_date.substring(0, 10) : '',
    office_account_id: props.payment.office_account_id || '',
    notes: props.payment.notes || '',
})

function submit() {
    form.put(`/dashboard/payments/${props.payment.id}`)
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Edit Payment: ${payment.receipt_number}`" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Payment</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Modify details for receipt {{ payment.receipt_number }}.</p>
                </div>
                <Link
                    href="/dashboard/payments"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to Payments
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <!-- Info Summary -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block mb-0.5">Receipt Number</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ payment.receipt_number }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block mb-0.5">Student</span>
                        <span class="font-semibold text-slate-900 dark:text-white">
                            {{ payment.student ? (payment.student.first_name + ' ' + (payment.student.last_name || '')) : '-' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Amount (BDT) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.amount" class="text-xs text-rose-500 mt-1.5">{{ form.errors.amount }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Payment Type <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.payment_type"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="advance">Advance</option>
                            <option value="partial">Partial</option>
                            <option value="final">Final</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Payment Status <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.payment_status"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Collection Date
                        </label>
                        <input
                            v-model="form.payment_date"
                            type="date"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Office Bank Account
                        </label>
                        <select
                            v-model="form.office_account_id"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="">-- Select Office Bank Account --</option>
                            <option
                                v-for="acc in accounts"
                                :key="acc.id"
                                :value="acc.id"
                            >
                                {{ acc.account_name }} &bull; {{ acc.provider_name || acc.account_type }}
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Notes / Reference
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        ></textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <Link
                        href="/dashboard/payments"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-xs disabled:opacity-50"
                    >
                        {{ form.processing ? 'Updating...' : 'Update Payment' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
