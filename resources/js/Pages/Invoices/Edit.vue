<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    invoice: Object,
    applications: Array,
    accounts: Array,
})

const form = useForm({
    application_id: props.invoice.application_id || '',
    date: props.invoice.date || new Date().toISOString().substring(0, 10),
    due_date: props.invoice.due_date || new Date().toISOString().substring(0, 10),
    invoice_number: props.invoice.invoice_number || '',
    status: props.invoice.status || 'draft',
    notes: props.invoice.notes || '',
    items: props.invoice.items && props.invoice.items.length > 0 
        ? props.invoice.items.map(i => ({
            chart_of_account_id: i.chart_of_account_id,
            description: i.description,
            quantity: Number(i.quantity),
            unit_price: Number(i.unit_price),
        }))
        : [
            {
                chart_of_account_id: props.accounts?.[0]?.id || '',
                description: 'Service Fee',
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
    form.put(`/dashboard/invoices/${props.invoice.id}`)
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Edit Invoice #${invoice.invoice_number}`" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Invoice</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Update invoice details and item line entries.</p>
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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-4">
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
                            type="date"
                            v-model="form.date"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.date" class="text-xs text-rose-500 mt-1.5">{{ form.errors.date }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Due Date <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="date"
                            v-model="form.due_date"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.due_date" class="text-xs text-rose-500 mt-1.5">{{ form.errors.due_date }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Invoice Number
                        </label>
                        <input
                            type="text"
                            v-model="form.invoice_number"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        >
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="paid">Paid</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="void">Void</option>
                        </select>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Line Items</h3>
                        <button
                            type="button"
                            @click="addItem"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-primary-dark transition uppercase tracking-wider"
                        >
                            + Add Line Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="flex flex-col md:flex-row gap-3 items-start md:items-center bg-slate-50/70 dark:bg-slate-800/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800"
                        >
                            <div class="w-full md:w-1/3">
                                <label class="block md:hidden text-xs font-bold mb-1">Account Head</label>
                                <select
                                    v-model="item.chart_of_account_id"
                                    required
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white"
                                >
                                    <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                        {{ acc.code }} - {{ acc.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="w-full md:flex-1">
                                <label class="block md:hidden text-xs font-bold mb-1">Description</label>
                                <input
                                    type="text"
                                    v-model="item.description"
                                    required
                                    placeholder="Item description"
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white"
                                />
                            </div>

                            <div class="w-24">
                                <label class="block md:hidden text-xs font-bold mb-1">Qty</label>
                                <input
                                    type="number"
                                    step="any"
                                    v-model="item.quantity"
                                    required
                                    placeholder="Qty"
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white text-right"
                                />
                            </div>

                            <div class="w-32">
                                <label class="block md:hidden text-xs font-bold mb-1">Unit Price</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    v-model="item.unit_price"
                                    required
                                    placeholder="Price"
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white text-right"
                                />
                            </div>

                            <div class="w-28 text-right font-semibold text-slate-800 dark:text-slate-200 text-sm">
                                ${{ (Number(item.quantity || 0) * Number(item.unit_price || 0)).toFixed(2) }}
                            </div>

                            <button
                                v-if="form.items.length > 1"
                                type="button"
                                @click="removeItem(index)"
                                class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg transition"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Subtotal / Total -->
                    <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="w-72 space-y-2">
                            <div class="flex justify-between text-base font-bold text-slate-900 dark:text-white">
                                <span>Grand Total:</span>
                                <span>${{ totalAmount.toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        Invoice Notes / Payment Terms
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        placeholder="Additional instructions or notes..."
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <Link
                        href="/dashboard/invoices"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        {{ form.processing ? 'Saving...' : 'Update Invoice' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
