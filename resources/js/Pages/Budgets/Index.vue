<template>
  <AdminLayout title="Budget Allocations">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Budget Allocations</h2>
          <p class="text-sm text-slate-500">Plan, cap, and monitor department and operational expense limits.</p>
        </div>
        <Link
          :href="route('admin.budgets.create')"
          class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center gap-1.5 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Allocate New Budget
        </Link>
      </div>

      <!-- Filters Card -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-3">
        <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
          <div class="relative w-60 shrink-0">
            <input
              v-model="search"
              type="text"
              placeholder="Search expense category..."
              class="w-full ltr:pr-9 rtl:pl-9 px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs"
            />
            <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-slate-400">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" stroke-width="2"/>
                <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
              </svg>
            </span>
          </div>
          <select
            v-model="period"
            class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white"
          >
            <option value="">All Periods</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
          </select>
          <div class="flex items-center gap-2 shrink-0">
            <button type="submit" class="px-3 py-2 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 shrink-0">Filter</button>
            <button @click="resetFilters" type="button" class="px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 shrink-0">Reset</button>
          </div>
        </form>
      </div>

      <!-- Budgets Table -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
              <tr>
                <th class="px-6 py-3">Expense Head</th>
                <th class="px-6 py-3">Period</th>
                <th class="px-6 py-3">Valid Timeline</th>
                <th class="px-6 py-3">Allocated Amount</th>
                <th class="px-6 py-3">Allocated By</th>
                <th class="px-6 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="b in budgets.data" :key="b.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/20">
                <td class="px-6 py-4">
                  <div class="font-medium text-slate-900 dark:text-white">{{ b.chart_of_account?.name || '—' }}</div>
                  <div class="text-xs text-slate-400 font-mono">{{ b.chart_of_account?.code }}</div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase" :class="b.period === 'monthly' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'">
                    {{ b.period }}
                  </span>
                </td>
                <td class="px-6 py-4 text-xs">
                  {{ b.start_date ? new Date(b.start_date).toLocaleDateString() : '—' }} to
                  {{ b.end_date ? new Date(b.end_date).toLocaleDateString() : '—' }}
                </td>
                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                  ৳{{ Number(b.amount).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                </td>
                <td class="px-6 py-4 text-xs text-slate-500">
                  {{ b.creator?.name || 'Admin' }}
                </td>
                <td class="px-6 py-4 text-center">
                  <TableDropdown>
                    <Link
                      :href="route('admin.budgets.edit', b.id)"
                      class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                    >
                      <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Edit Budget
                    </Link>
                    <button
                      @click="deleteBudget(b)"
                      class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete Budget
                    </button>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="budgets.data.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                  No budgets allocated yet. Click "Allocate New Budget" to begin.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="budgets" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
  budgets: Object,
  filters: Object,
})

const search = ref(props.filters?.search || '')
const period = ref(props.filters?.period || '')

const applyFilters = () => {
  router.get(route('admin.budgets.index'), { search: search.value, period: period.value }, { preserveState: true })
}

const resetFilters = () => {
  search.value = ''
  period.value = ''
  router.get(route('admin.budgets.index'))
}

const deleteBudget = (b) => {
  if (confirm('Are you sure you want to delete this budget allocation?')) {
    router.delete(route('admin.budgets.destroy', b.id), { preserveScroll: true })
  }
}
</script>
