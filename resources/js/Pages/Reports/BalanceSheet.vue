<template>
  <AdminLayout title="Balance Sheet Statement">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Statement of Financial Position</h2>
          <p class="text-sm text-slate-500">Balance sheet as of {{ asOfDate ? new Date(asOfDate).toLocaleDateString() : 'Today' }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <a
            :href="route('admin.reports.balance-sheet.pdf', { as_of_date: asOfDate, from_date: fromDate })"
            target="_blank"
            class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 flex items-center gap-1.5"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export PDF
          </a>
          <Link
            :href="route('admin.reports.summary')"
            class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700"
          >
            Summary Report
          </Link>
        </div>
      </div>

      <!-- Date Filter -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <form @submit.prevent="applyFilter" class="flex flex-col sm:flex-row gap-4 items-end">
          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">From Date (Optional)</label>
            <input v-model="filterFrom" type="date" class="px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">As Of Date *</label>
            <input v-model="filterAsOf" type="date" required class="px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
          </div>
          <button type="submit" class="px-4 py-1.5 text-xs font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
            Generate Statement
          </button>
        </form>
      </div>

      <!-- Balance Verification Alert -->
      <div
        :class="isBalanced ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300' : 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-300'"
        class="p-4 rounded-xl border flex items-center justify-between text-sm font-semibold"
      >
        <div class="flex items-center space-x-2">
          <svg v-if="isBalanced" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <svg v-else class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <span>{{ isBalanced ? 'Balance Sheet is perfectly balanced (Assets = Liabilities + Equity + Retained Earnings)' : 'Imbalance Detected: Difference of ৳' + Number(Math.abs(totalAssets - (totalLiabilities + totalEquity + netProfit))).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
        </div>
        <span class="font-mono text-xs">Total: ৳{{ Number(totalAssets).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
      </div>

      <!-- Statement Two Column Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assets Column -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden h-fit">
          <div class="p-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider">1. Assets</h3>
            <span class="font-mono text-sm font-bold text-slate-900 dark:text-white">৳{{ Number(totalAssets).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
          </div>

          <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
              <tr v-for="item in data.asset" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/10">
                <td class="px-5 py-3">
                  <div class="font-medium text-slate-800 dark:text-white">{{ item.name }}</div>
                  <div class="font-mono text-xs text-slate-400">{{ item.code }}</div>
                </td>
                <td class="px-5 py-3 text-right font-mono font-medium text-slate-900 dark:text-white">
                  ৳{{ Number(item.balance || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                </td>
              </tr>
              <tr v-if="!data.asset || data.asset.length === 0">
                <td colspan="2" class="px-5 py-6 text-center text-slate-400 italic">No asset accounts found.</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50 dark:bg-slate-700/30 border-t border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
              <tr>
                <td class="px-5 py-3 uppercase text-xs">Total Assets</td>
                <td class="px-5 py-3 text-right font-mono text-primary-600 dark:text-primary-400">
                  ৳{{ Number(totalAssets).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Liabilities & Equity Column -->
        <div class="space-y-6">
          <!-- Liabilities Section -->
          <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
              <h3 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider">2. Liabilities</h3>
              <span class="font-mono text-sm font-bold text-slate-900 dark:text-white">৳{{ Number(totalLiabilities).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
            </div>

            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
              <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                <tr v-for="item in data.liability" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/10">
                  <td class="px-5 py-3">
                    <div class="font-medium text-slate-800 dark:text-white">{{ item.name }}</div>
                    <div class="font-mono text-xs text-slate-400">{{ item.code }}</div>
                  </td>
                  <td class="px-5 py-3 text-right font-mono font-medium text-slate-900 dark:text-white">
                    ৳{{ Number(item.balance || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                  </td>
                </tr>
                <tr v-if="!data.liability || data.liability.length === 0">
                  <td colspan="2" class="px-5 py-4 text-center text-slate-400 italic">No liability accounts found.</td>
                </tr>
              </tbody>
              <tfoot class="bg-slate-50 dark:bg-slate-700/30 border-t border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
                <tr>
                  <td class="px-5 py-3 uppercase text-xs">Total Liabilities</td>
                  <td class="px-5 py-3 text-right font-mono">
                    ৳{{ Number(totalLiabilities).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Equity & Retained Earnings Section -->
          <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
              <h3 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider">3. Equity & Reserves</h3>
              <span class="font-mono text-sm font-bold text-slate-900 dark:text-white">৳{{ Number(totalEquity + netProfit).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
            </div>

            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
              <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                <tr v-for="item in data.equity" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/10">
                  <td class="px-5 py-3">
                    <div class="font-medium text-slate-800 dark:text-white">{{ item.name }}</div>
                    <div class="font-mono text-xs text-slate-400">{{ item.code }}</div>
                  </td>
                  <td class="px-5 py-3 text-right font-mono font-medium text-slate-900 dark:text-white">
                    ৳{{ Number(item.balance || 0).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                  </td>
                </tr>
                <tr class="bg-emerald-50/40 dark:bg-emerald-900/10">
                  <td class="px-5 py-3">
                    <div class="font-semibold text-emerald-800 dark:text-emerald-400">Current Period Net Surplus / (Deficit)</div>
                    <div class="text-xs text-slate-400">Revenue minus Expenses</div>
                  </td>
                  <td class="px-5 py-3 text-right font-mono font-semibold text-emerald-700 dark:text-emerald-300">
                    ৳{{ Number(netProfit).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-slate-50 dark:bg-slate-700/30 border-t border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
                <tr>
                  <td class="px-5 py-3 uppercase text-xs">Total Liabilities & Equity</td>
                  <td class="px-5 py-3 text-right font-mono text-primary-600 dark:text-primary-400">
                    ৳{{ Number(totalLiabilities + totalEquity + netProfit).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  data: Object,
  asOfDate: String,
  fromDate: String,
  netProfit: [Number, String],
  totalAssets: [Number, String],
  totalLiabilities: [Number, String],
  totalEquity: [Number, String],
})

const filterAsOf = ref(props.asOfDate || '')
const filterFrom = ref(props.fromDate || '')

const isBalanced = computed(() => {
  const diff = Math.abs(Number(props.totalAssets) - (Number(props.totalLiabilities) + Number(props.totalEquity) + Number(props.netProfit)))
  return diff < 0.01
})

const applyFilter = () => {
  router.get(route('admin.reports.balance-sheet'), {
    as_of_date: filterAsOf.value,
    from_date: filterFrom.value || undefined,
  }, { preserveState: true })
}
</script>
