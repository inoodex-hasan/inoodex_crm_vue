<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    categories: Array,
    accounts: Array,
    pendings_salaries: Array,
})

const form = useForm({
    chart_of_account_id: props.categories?.[0]?.id || '',
    office_account_id: props.accounts?.[0]?.id || '',
    title: '',
    amount: '',
    expense_date: new Date().toISOString().substring(0, 10),
    description: '',
})

function submit() {
    form.post('/dashboard/expenses')
}
</script>

<template>
    <AdminLayout>
        <Head title="Record Office Expense" />

        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Record Expense</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Post an expense voucher with double-entry ledger impact.</p>
                </div>
                <Link
                    href="/dashboard/expenses"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to Expenses
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Expense Category <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.chart_of_account_id"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.code }} - {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.chart_of_account_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.chart_of_account_id }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Payment Source Account <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.office_account_id"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                {{ acc.account_name }} &bull; {{ acc.provider_name || acc.account_type }} (Bal: BDT {{ Number(acc.current_balance || 0).toLocaleString() }})
                            </option>
                        </select>
                        <p v-if="form.errors.office_account_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.office_account_id }}</p>
                    </div>

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
                            placeholder="e.g. 12500.00"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.amount" class="text-xs text-rose-500 mt-1.5">{{ form.errors.amount }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Expense Date <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.expense_date"
                            type="date"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.expense_date" class="text-xs text-rose-500 mt-1.5">{{ form.errors.expense_date }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Title / Vendor / Payee <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            placeholder="e.g. Office Internet Bill - Oct 2026 (AmberIT)"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.title" class="text-xs text-rose-500 mt-1.5">{{ form.errors.title }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Description & Voucher Memo
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Additional details, voucher receipt numbers, or approval trail..."
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        ></textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <Link
                        href="/dashboard/expenses"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-xs disabled:opacity-50"
                    >
                        {{ form.processing ? 'Posting...' : 'Save Expense' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
