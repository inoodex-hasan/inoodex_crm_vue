<template>
  <AdminLayout title="Journal Entries">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Journal Entries</h2>
          <p class="text-sm text-slate-500">General ledger double-entry vouchers and audit journal entries.</p>
        </div>
        <div class="flex items-center space-x-3">
          <a
            :href="route('admin.journal-entries.report', currentFilters)"
            target="_blank"
            class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 flex items-center gap-1.5"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export PDF
          </a>
          <Link
            :href="route('admin.journal-entries.create')"
            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center gap-1.5"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Journal Voucher
          </Link>
        </div>
      </div>

      <!-- Filters Filter Card -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Start Date</label>
            <input v-model="filterForm.start_date" type="date" class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">End Date</label>
            <input v-model="filterForm.end_date" type="date" class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Reference No</label>
            <input v-model="filterForm.reference_number" type="text" placeholder="JV-..." class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Accounting Period</label>
            <select v-model="filterForm.period_id" class="form-select w-full px-3 py-1.5 ltr:pr-9 rtl:pl-9 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
              <option value="">All Periods</option>
              <option v-for="p in periods" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Student</label>
            <input v-model="filterForm.student_name" type="text" placeholder="Student name..." class="w-full px-3 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
          </div>

          <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 py-1.5 px-3 text-xs font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
              Filter
            </button>
            <button @click="resetFilters" type="button" class="py-1.5 px-3 text-xs font-medium text-slate-600 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200">
              Reset
            </button>
          </div>
        </form>
      </div>

      <!-- Journal Entries Table -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
              <tr>
                <th class="px-6 py-3">Date</th>
                <th class="px-6 py-3">Reference</th>
                <th class="px-6 py-3">Period</th>
                <th class="px-6 py-3">Application / Student</th>
                <th class="px-6 py-3">Amount</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="entry in entries.data" :key="entry.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/20">
                <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900 dark:text-white">
                  {{ entry.date ? new Date(entry.date).toLocaleDateString() : '—' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-primary-600 dark:text-primary-400 font-semibold">
                  {{ entry.reference_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  {{ entry.period?.name || '—' }}
                </td>
                <td class="px-6 py-4">
                  <div v-if="entry.application?.student">
                    <span class="font-medium text-slate-900 dark:text-white">
                      {{ entry.application.student.first_name }} {{ entry.application.student.last_name }}
                    </span>
                    <span class="block text-xs text-slate-400">App #{{ entry.application.id }}</span>
                  </div>
                  <span v-else class="text-xs text-slate-400 italic">General / Office</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-slate-900 dark:text-white">
                  ৳{{ Number(entry.total_amount || 0).toLocaleString() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 uppercase">
                    {{ entry.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <TableDropdown>
                    <Link
                      :href="route('admin.journal-entries.show', entry.id)"
                      class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                    >
                      <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View Voucher
                    </Link>
                    <button
                      @click="deleteEntry(entry)"
                      class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete Voucher
                    </button>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="entries.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                  No journal vouchers found matching current criteria.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="entries" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
  entries: Object,
  periods: Array,
  filters: Object,
})

const filterForm = reactive({
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
  reference_number: props.filters?.reference_number || '',
  period_id: props.filters?.period_id || '',
  student_name: props.filters?.student_name || '',
  status: props.filters?.status || '',
})

const currentFilters = computed(() => {
  return Object.fromEntries(
    Object.entries(filterForm).filter(([_, v]) => v !== '' && v !== null)
  )
})

const applyFilters = () => {
  router.get(route('admin.journal-entries.index'), currentFilters.value, { preserveState: true })
}

const resetFilters = () => {
  Object.keys(filterForm).forEach(k => filterForm[k] = '')
  router.get(route('admin.journal-entries.index'))
}

const deleteEntry = (entry) => {
  if (confirm(`Are you sure you want to delete journal voucher ${entry.reference_number}?`)) {
    router.delete(route('admin.journal-entries.destroy', entry.id), { preserveScroll: true })
  }
}
</script>
