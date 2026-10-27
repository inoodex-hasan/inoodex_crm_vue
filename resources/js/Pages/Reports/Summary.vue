<template>
  <AdminLayout title="Financial Summary Report">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Financial Summary Report</h2>
          <p class="text-sm text-slate-500">Comprehensive overview of collections, operational disbursements, and cash movements.</p>
        </div>
        <div class="flex items-center space-x-3">
          <a
            :href="route('admin.reports.download-pdf', filterParams)"
            target="_blank"
            class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 flex items-center gap-1.5"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download PDF
          </a>
          <Link
            :href="route('admin.reports.balance-sheet')"
            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            Balance Sheet
          </Link>
        </div>
      </div>

      <!-- Filters Card -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Start Date</label>
            <input v-model="form.start_date" type="date" class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">End Date</label>
            <input v-model="form.end_date" type="date" class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Office Account</label>
            <select v-model="form.account_id" class="form-select w-full px-3 py-1.5 ltr:pr-9 rtl:pl-9 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
              <option :value="null">All Accounts</option>
              <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.account_name || a.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Transaction Type</label>
            <select v-model="form.transaction_type" class="form-select w-full px-3 py-1.5 ltr:pr-9 rtl:pl-9 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
              <option value="">All Transactions</option>
              <option value="income">Income Collections Only</option>
              <option value="expense">Expenses Only</option>
              <option value="transfer">Transfers Only</option>
            </select>
          </div>

          <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 py-1.5 px-3 text-xs font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
              Apply Filters
            </button>
            <button @click="resetFilters" type="button" class="py-1.5 px-3 text-xs font-medium text-slate-600 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200">
              Reset
            </button>
          </div>
        </form>
      </div>

      <!-- KPI Overview Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Collections</span>
          <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">
            ৳{{ Number(summary.total_income || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
          </div>
          <span class="text-xs text-slate-400 mt-1 block">{{ summary.income_count || 0 }} receipts logged</span>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Disbursements</span>
          <div class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-2">
            ৳{{ Number(summary.total_expense || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
          </div>
          <span class="text-xs text-slate-400 mt-1 block">{{ summary.expense_count || 0 }} vouchers processed</span>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Net Operational Surplus</span>
          <div class="text-2xl font-bold mt-2" :class="(summary.total_income - summary.total_expense) >= 0 ? 'text-primary-600 dark:text-primary-400' : 'text-amber-600 dark:text-amber-400'">
            ৳{{ Number(summary.total_income - summary.total_expense).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
          </div>
          <span class="text-xs text-slate-400 mt-1 block">Income minus Expenses</span>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Account Transfers</span>
          <div class="text-2xl font-bold text-slate-900 dark:text-white mt-2">
            {{ summary.transfer_count || 0 }}
          </div>
          <span class="text-xs text-slate-400 mt-1 block">Inter-account journal transfers</span>
        </div>
      </div>

      <!-- Detail Breakdowns -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expenses List -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Recent Disbursements</h3>
            <span class="text-xs text-slate-500">{{ expenses.length }} items</span>
          </div>
          <div class="overflow-x-auto max-h-80">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
              <thead class="bg-slate-50 dark:bg-slate-700/50 sticky top-0 uppercase font-semibold text-slate-500">
                <tr>
                  <th class="px-3 py-2">Date</th>
                  <th class="px-3 py-2">Description</th>
                  <th class="px-3 py-2 text-right">Amount</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <tr v-for="exp in expenses.slice(0, 15)" :key="exp.id">
                  <td class="px-3 py-2 whitespace-nowrap">{{ exp.expense_date }}</td>
                  <td class="px-3 py-2 font-medium text-slate-800 dark:text-white truncate max-w-xs">{{ exp.description }}</td>
                  <td class="px-3 py-2 text-right font-mono font-semibold text-rose-600 dark:text-rose-400">৳{{ Number(exp.amount).toLocaleString() }}</td>
                </tr>
                <tr v-if="expenses.length === 0">
                  <td colspan="3" class="px-3 py-6 text-center text-slate-400">No expenses recorded for this timeframe.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Payments List -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Recent Fee Collections</h3>
            <span class="text-xs text-slate-500">{{ payments.length }} items</span>
          </div>
          <div class="overflow-x-auto max-h-80">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
              <thead class="bg-slate-50 dark:bg-slate-700/50 sticky top-0 uppercase font-semibold text-slate-500">
                <tr>
                  <th class="px-3 py-2">Date</th>
                  <th class="px-3 py-2">Method</th>
                  <th class="px-3 py-2 text-right">Amount</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <tr v-for="pay in payments.slice(0, 15)" :key="pay.id">
                  <td class="px-3 py-2 whitespace-nowrap">{{ pay.payment_date }}</td>
                  <td class="px-3 py-2 uppercase font-medium text-slate-800 dark:text-white">{{ pay.payment_method }}</td>
                  <td class="px-3 py-2 text-right font-mono font-semibold text-emerald-600 dark:text-emerald-400">৳{{ Number(pay.amount).toLocaleString() }}</td>
                </tr>
                <tr v-if="payments.length === 0">
                  <td colspan="3" class="px-3 py-6 text-center text-slate-400">No collections recorded for this timeframe.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  summary: Object,
  month: String,
  year: String,
  expenses: Array,
  transfers: Array,
  budgets: Array,
  payments: Array,
  accounts: Array,
  accountId: [String, Number],
  transactionType: String,
  startDate: String,
  endDate: String,
})

const form = reactive({
  start_date: props.startDate || '',
  end_date: props.endDate || '',
  account_id: props.accountId || null,
  transaction_type: props.transactionType || '',
})

const filterParams = computed(() => {
  return Object.fromEntries(
    Object.entries(form).filter(([_, v]) => v !== '' && v !== null)
  )
})

const applyFilters = () => {
  router.get(route('admin.reports.summary'), filterParams.value, { preserveState: true })
}

const resetFilters = () => {
  form.start_date = ''
  form.end_date = ''
  form.account_id = null
  form.transaction_type = ''
  router.get(route('admin.reports.summary'))
}
</script>
