<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    applications: Array,
    users: Array,
    selected_application_id: [String, Number],
    accounts: Array,
})

const form = useForm({
    application_id: props.selected_application_id || '',
    invoice_id: '',
    amount: '',
    payment_type: 'advance',
    payment_status: 'completed',
    payment_date: new Date().toISOString().substring(0, 10),
    office_account_id: props.accounts?.[0]?.id || '',
    notes: '',
})

const availableInvoices = ref([])
const selectedAppDetails = ref(null)

function onApplicationChange() {
    form.invoice_id = ''
    if (!form.application_id) {
        availableInvoices.value = []
        selectedAppDetails.value = null
        return
    }

    // Fetch invoices for application
    fetch(`/dashboard/applications/${form.application_id}/invoices`)
        .then(res => res.json())
        .then(data => {
            availableInvoices.value = data || []
            if (data && data.length > 0) {
                form.invoice_id = data[0].id
            }
        })
        .catch(() => {
            availableInvoices.value = []
        })
}

// Watch initial selected_application_id
if (form.application_id) {
    onApplicationChange()
}

function submit() {
    form.post('/dashboard/payments')
}
</script>

<template>
    <AdminLayout>
        <Head title="Create Payment" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Record Payment</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Receive student fee payment, generate official receipt, and record bank ledger credit.</p>
                </div>
                <Link
                    href="/dashboard/payments"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to Payments
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Application -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Student Application <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.application_id"
                            @change="onApplicationChange"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="">-- Choose Application --</option>
                            <option
                                v-for="app in applications"
                                :key="app.id"
                                :value="app.id"
                            >
                                {{ app.application_id }} &bull; {{ app.student?.first_name }} {{ app.student?.last_name }} ({{ app.student?.phone || 'No Phone' }})
                            </option>
                        </select>
                        <p v-if="form.errors.application_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.application_id }}</p>
                    </div>

                    <!-- Linked Invoice -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Linked Invoice <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.invoice_id"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="">-- Choose Invoice --</option>
                            <option
                                v-for="inv in availableInvoices"
                                :key="inv.id"
                                :value="inv.id"
                            >
                                {{ inv.invoice_number }} &bull; BDT {{ Number(inv.total_amount).toLocaleString() }} ({{ inv.status }})
                            </option>
                        </select>
                        <p v-if="form.errors.invoice_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.invoice_id }}</p>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Collection Amount (BDT) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            required
                            placeholder="e.g. 25000.00"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.amount" class="text-xs text-rose-500 mt-1.5">{{ form.errors.amount }}</p>
                    </div>

                    <!-- Payment Type -->
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

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Payment Status <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.payment_status"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="completed">Completed (Confirmed)</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <!-- Date -->
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

                    <!-- Office Account -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Deposit To Office Bank Account <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.office_account_id"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="">-- Select Office Bank Account --</option>
                            <option
                                v-for="acc in accounts"
                                :key="acc.id"
                                :value="acc.id"
                            >
                                {{ acc.account_name }} &bull; {{ acc.provider_name || acc.account_type }} (Balance: BDT {{ Number(acc.current_balance || 0).toLocaleString() }})
                            </option>
                        </select>
                        <p v-if="form.errors.office_account_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.office_account_id }}</p>
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Notes / Transaction ID
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            placeholder="Bank deposit slip reference, cheque number, or gateway transaction ID..."
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
                        {{ form.processing ? 'Saving...' : 'Record Payment' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
