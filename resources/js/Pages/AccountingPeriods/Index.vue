<template>
  <AdminLayout title="Accounting Periods">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Accounting Periods</h2>
          <p class="text-sm text-slate-500">Fiscal years, quarters, and monthly financial locking boundaries.</p>
        </div>
        <Link
          :href="route('admin.accounting-periods.create')"
          class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center gap-1.5 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          New Fiscal Period
        </Link>
      </div>

      <!-- Periods Table -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
              <tr>
                <th class="px-6 py-3">Period Title</th>
                <th class="px-6 py-3">Classification</th>
                <th class="px-6 py-3">Start Date</th>
                <th class="px-6 py-3">End Date</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Remarks</th>
                <th class="px-6 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="period in periods" :key="period.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/20">
                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                  {{ period.name }}
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 uppercase">
                    {{ period.type?.replace('_', ' ') }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs">
                  {{ period.start_date ? new Date(period.start_date).toLocaleDateString() : '—' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs">
                  {{ period.end_date ? new Date(period.end_date).toLocaleDateString() : '—' }}
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="period.status === 'open' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700'"
                    class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase"
                  >
                    {{ period.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-xs text-slate-400 max-w-xs truncate">
                  {{ period.remarks || '—' }}
                </td>
                <td class="px-6 py-4 text-center">
                  <TableDropdown>
                    <button
                      @click="toggleStatus(period)"
                      class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                      :class="period.status === 'open' ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                      {{ period.status === 'open' ? 'Lock / Close Period' : 'Reopen Period' }}
                    </button>
                    <Link
                      :href="route('admin.accounting-periods.edit', period.id)"
                      class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                    >
                      <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Edit Period
                    </Link>
                    <button
                      @click="deletePeriod(period)"
                      class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete Period
                    </button>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="periods.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                  No accounting periods defined yet. Click "New Fiscal Period" to establish a calendar.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
  periods: Array,
})

const toggleStatus = (period) => {
  const nextStatus = period.status === 'open' ? 'closed' : 'open'
  router.put(route('admin.accounting-periods.update', period.id), { status: nextStatus }, { preserveScroll: true })
}

const deletePeriod = (period) => {
  if (confirm(`Are you sure you want to delete period "${period.name}"?`)) {
    router.delete(route('admin.accounting-periods.destroy', period.id), { preserveScroll: true })
  }
}
</script>
