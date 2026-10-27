<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    applications: Array,
    accounts: Array,
    selectedApplication: Object,
})

const form = useForm({
    application_id: props.selectedApplication?.id || '',
    date: new Date().toISOString().substring(0, 10),
    due_date: new Date(Date.now() + 14 * 86400000).toISOString().substring(0, 10),
    invoice_number: '',
    notes: '',
    items: [
        {
            chart_of_account_id: props.accounts?.[0]?.id || '',
            description: 'Tuition / Processing Service Fee',
            quantity: 1,
            unit_price: 0,
        }
    ]
})

function addItem() {
    form.items.push({
        chart_of_account_id: props.accounts?.[0]?.id || '',
        description: '',
        quantity: 1,
        unit_price: 0,
    })
}

function removeItem(index) {
    if (form.items.length > 1) {
        form.items.splice(index, 1)
    }
}

const totalAmount = computed(() => {
    return form.items.reduce((sum, item) => {
        return sum + (Number(item.quantity || 0) * Number(item.unit_price || 0))
    }, 0)
})

function submit() {
    form.post('/dashboard/invoices')
}
</script>

<template>
    <AdminLayout>
        <Head title="Create Student Invoice" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Create Invoice</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Generate an official payable invoice with itemized line entries.</p>
                </div>
                <Link
                    href="/dashboard/invoices"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to Invoices
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <!-- Top Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Select Student Application <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.application_id"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="">-- Select Application --</option>
                            <option
                                v-for="app in applications"
                                :key="app.id"
                                :value="app.id"
                            >
                                {{ app.application_id }} &bull; {{ app.student?.first_name }} {{ app.student?.last_name }} ({{ app.university?.name || 'No University' }})
                            </option>
                        </select>
                        <p v-if="form.errors.application_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.application_id }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Invoice Date <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.date"
                            type="date"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.date" class="text-xs text-rose-500 mt-1.5">{{ form.errors.date }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Due Date <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.due_date"
                            type="date"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.due_date" class="text-xs text-rose-500 mt-1.5">{{ form.errors.due_date }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Invoice Number (Optional)
                        </label>
                        <input
                            v-model="form.invoice_number"
                            type="text"
                            placeholder="Auto-generated if left blank"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Itemized Breakdown</h3>
                        <button
                            type="button"
                            @click="addItem"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-primary-dark transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Line Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(item, idx) in form.items"
                            :key="idx"
                            class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 items-end"
                        >
                            <div class="md:col-span-4">
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Account Category</label>
                                <select
                                    v-model="item.chart_of_account_id"
                                    required
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                                >
                                    <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                        {{ acc.code }} - {{ acc.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Description</label>
                                <input
                                    v-model="item.description"
                                    type="text"
                                    required
                                    placeholder="Description..."
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                                />
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Qty</label>
                                <input
                                    v-model="item.quantity"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    required
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-center font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                                />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Unit Price (BDT)</label>
                                <input
                                    v-model="item.unit_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                                />
                            </div>
                            <div class="md:col-span-1 flex items-center justify-end">
                                <button
                                    type="button"
                                    @click="removeItem(idx)"
                                    :disabled="form.items.length <= 1"
                                    class="p-2 text-slate-400 hover:text-rose-500 disabled:opacity-30 rounded-lg transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amount Display -->
                    <div class="flex justify-end p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Grand Total</span>
                            <span class="font-mono text-2xl font-black text-primary">BDT {{ totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Notes & Terms</label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        placeholder="Payment bank details, terms, or refund policies..."
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    ></textarea>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <Link
                        href="/dashboard/invoices"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-xs disabled:opacity-50"
                    >
                        {{ form.processing ? 'Generating...' : 'Save & Issue Invoice' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
