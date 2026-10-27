<template>
  <AdminLayout title="Reconciliation Workspace">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center space-x-3">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Bank Reconciliation</h2>
            <span
              :class="reconciliation.status === 'closed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800'"
              class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase"
            >
              {{ reconciliation.status }}
            </span>
          </div>
          <p class="text-sm text-slate-500">{{ reconciliation.account?.name }} ({{ reconciliation.account?.account_number }}) - As of {{ reconciliation.statement_date }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <button
            v-if="reconciliation.status === 'draft' && isZeroDifference"
            @click="closeReconciliation"
            class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
          >
            Close & Finalize Statement
          </button>
          <Link
            :href="route('admin.bank-reconciliations.index')"
            class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700"
          >
            Back to List
          </Link>
        </div>
      </div>

      <!-- Balance Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
          <span class="text-xs font-semibold uppercase text-slate-500">Bank Statement Ending Balance</span>
          <div class="text-xl font-bold text-slate-900 dark:text-white mt-1 font-mono">
            ৳{{ Number(reconciliation.statement_balance).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
          <span class="text-xs font-semibold uppercase text-slate-500">Reconciled System Balance</span>
          <div class="text-xl font-bold text-slate-900 dark:text-white mt-1 font-mono">
            ৳{{ Number(reconciliation.system_balance).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
          <span class="text-xs font-semibold uppercase text-slate-500">Unreconciled Difference</span>
          <div class="text-xl font-bold mt-1 font-mono" :class="isZeroDifference ? 'text-emerald-600' : 'text-rose-600'">
            ৳{{ Number(reconciliation.difference).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
          </div>
        </div>
      </div>

      <!-- Matched Items -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
          <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
            Matched Transactions ({{ reconciliation.items?.length || 0 }})
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/30 uppercase font-semibold text-slate-500">
              <tr>
                <th class="px-4 py-2.5">Date</th>
                <th class="px-4 py-2.5">Voucher</th>
                <th class="px-4 py-2.5">Description</th>
                <th class="px-4 py-2.5 text-right">Debit</th>
                <th class="px-4 py-2.5 text-right">Credit</th>
                <th class="px-4 py-2.5 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
              <tr v-for="item in reconciliation.items" :key="item.id">
                <td class="px-4 py-3">{{ item.journal_entry_item?.journal_entry?.date }}</td>
                <td class="px-4 py-3 font-mono text-primary-600 font-semibold">{{ item.journal_entry_item?.journal_entry?.reference_number }}</td>
                <td class="px-4 py-3">{{ item.journal_entry_item?.description }}</td>
                <td class="px-4 py-3 text-right font-mono">{{ Number(item.journal_entry_item?.debit) > 0 ? ('৳' + Number(item.journal_entry_item?.debit).toLocaleString()) : '—' }}</td>
                <td class="px-4 py-3 text-right font-mono">{{ Number(item.journal_entry_item?.credit) > 0 ? ('৳' + Number(item.journal_entry_item?.credit).toLocaleString()) : '—' }}</td>
                <td class="px-4 py-3 text-right">
                  <button
                    v-if="reconciliation.status === 'draft'"
                    @click="unmatchItem(item)"
                    class="px-2 py-0.5 text-xs text-rose-600 border border-rose-200 rounded hover:bg-rose-50"
                  >
                    Unmatch
                  </button>
                </td>
              </tr>
              <tr v-if="!reconciliation.items || reconciliation.items.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-slate-400 italic">No matched transactions in this session yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Unreconciled Journal Transactions -->
      <div v-if="reconciliation.status === 'draft'" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
          <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
            Unmatched Ledger Transactions ({{ unreconciledItems?.length || 0 }})
          </h3>
          <span class="text-xs text-slate-400">Match against your bank statement records</span>
        </div>

        <div class="overflow-x-auto max-h-96">
          <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/30 uppercase font-semibold text-slate-500 sticky top-0">
              <tr>
                <th class="px-4 py-2.5">Date</th>
                <th class="px-4 py-2.5">Voucher Reference</th>
                <th class="px-4 py-2.5">Narration</th>
                <th class="px-4 py-2.5 text-right">Debit</th>
                <th class="px-4 py-2.5 text-right">Credit</th>
                <th class="px-4 py-2.5 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
              <tr v-for="item in unreconciledItems" :key="item.id">
                <td class="px-4 py-3 whitespace-nowrap">{{ item.journal_entry?.date }}</td>
                <td class="px-4 py-3 font-mono font-semibold text-primary-600">{{ item.journal_entry?.reference_number }}</td>
                <td class="px-4 py-3">{{ item.description }}</td>
                <td class="px-4 py-3 text-right font-mono">{{ Number(item.debit) > 0 ? ('৳' + Number(item.debit).toLocaleString()) : '—' }}</td>
                <td class="px-4 py-3 text-right font-mono">{{ Number(item.credit) > 0 ? ('৳' + Number(item.credit).toLocaleString()) : '—' }}</td>
                <td class="px-4 py-3 text-right">
                  <button
                    @click="matchItem(item)"
                    class="px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-300 rounded hover:bg-emerald-100"
                  >
                    Match ✓
                  </button>
                </td>
              </tr>
              <tr v-if="!unreconciledItems || unreconciledItems.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-slate-400 italic">All ledger items have been matched!</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  reconciliation: Object,
  unreconciledItems: Array,
})

const isZeroDifference = computed(() => {
  return Math.abs(Number(props.reconciliation.difference)) < 0.01
})

const matchItem = async (item) => {
  await window.axios.post(route('admin.bank-reconciliations.match', props.reconciliation.id), {
    journal_entry_item_id: item.id
  })
  router.reload({ preserveScroll: true })
}

const unmatchItem = async (item) => {
  await window.axios.post(route('admin.bank-reconciliations.unmatch', props.reconciliation.id), {
    reconciliation_item_id: item.id
  })
  router.reload({ preserveScroll: true })
}

const closeReconciliation = () => {
  if (confirm('Finalize and lock this bank reconciliation? This action cannot be undone.')) {
    router.post(route('admin.bank-reconciliations.close', props.reconciliation.id))
  }
}
</script>
