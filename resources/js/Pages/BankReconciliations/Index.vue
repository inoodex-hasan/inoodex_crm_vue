<template>
  <AdminLayout title="Bank Reconciliations">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Bank Reconciliations</h2>
          <p class="text-sm text-slate-500">Cross-match physical bank account statements against system general ledger entries.</p>
        </div>
        <Link
          :href="route('admin.bank-reconciliations.create')"
          class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center gap-1.5 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Start New Reconciliation
        </Link>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
              <tr>
                <th class="px-6 py-3">Statement Date</th>
                <th class="px-6 py-3">Bank / Office Account</th>
                <th class="px-6 py-3">Bank Balance</th>
                <th class="px-6 py-3">System Ledger Balance</th>
                <th class="px-6 py-3">Variance / Diff</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="r in reconciliations.data" :key="r.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/20">
                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                  {{ r.statement_date ? new Date(r.statement_date).toLocaleDateString() : '—' }}
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-slate-800 dark:text-slate-200">{{ r.account?.name }}</div>
                  <div class="text-xs text-slate-400 font-mono">{{ r.account?.account_number }}</div>
                </td>
                <td class="px-6 py-4 font-mono font-semibold text-slate-900 dark:text-white">
                  ৳{{ Number(r.statement_balance).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                </td>
                <td class="px-6 py-4 font-mono">
                  ৳{{ Number(r.system_balance).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                </td>
                <td class="px-6 py-4 font-mono font-semibold" :class="Number(r.difference) === 0 ? 'text-emerald-600' : 'text-amber-600'">
                  ৳{{ Number(r.difference).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="r.status === 'closed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800'"
                    class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase"
                  >
                    {{ r.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <TableDropdown>
                    <Link
                      :href="route('admin.bank-reconciliations.show', r.id)"
                      class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10 transition-colors"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      {{ r.status === 'closed' ? 'View Report' : 'Reconcile Account' }}
                    </Link>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="reconciliations.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">No bank reconciliation sessions created yet.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="reconciliations" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

defineProps({
  reconciliations: Object,
})
</script>
