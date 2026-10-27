<template>
  <AdminLayout title="Currency & Exchange Rates">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Currencies & Foreign Exchange</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">System operating currencies, base FX rates, and symbols for financial ledger conversions.</p>
        </div>
        <Link
          :href="route('admin.currencies.create')"
          class="btn btn-primary gap-2 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add Currency
        </Link>
      </div>

      <!-- Currencies Panel -->
      <div class="panel">
        <!-- Search Toolbar -->
        <div class="mb-5">
          <form @submit.prevent="applySearch" class="flex flex-wrap items-center gap-2.5">
            <div class="relative w-60 shrink-0">
              <input
                v-model="search"
                type="text"
                placeholder="Search currency name or code..."
                class="form-input ltr:pr-9 rtl:pl-9 text-xs py-2 w-full"
              />
              <button type="submit" class="absolute inset-y-0 ltr:right-2.5 rtl:left-2.5 flex items-center text-gray-400 hover:text-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </button>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button type="submit" class="btn btn-primary btn-sm text-xs py-2 px-3">Search</button>
              <button @click="resetSearch" type="button" class="btn btn-outline-danger btn-sm text-xs py-2 px-3">Reset</button>
            </div>
          </form>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
          <table class="table-hover whitespace-nowrap w-full">
            <thead>
              <tr>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Currency Name</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">ISO Code</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Symbol</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Exchange Rate (vs BDT)</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Default Base</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Status</th>
                <th class="text-right font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
              <tr v-for="c in currencies.data" :key="c.id" class="hover:bg-primary/5 transition">
                <td class="py-3.5 px-4 font-bold text-[#0e1726] dark:text-white text-sm">
                  {{ c.name }}
                </td>
                <td class="py-3.5 px-4">
                  <span class="font-mono font-bold text-xs text-primary bg-primary/10 px-2 py-0.5 rounded uppercase">
                    {{ c.code }}
                  </span>
                </td>
                <td class="py-3.5 px-4 font-bold text-base text-[#0e1726] dark:text-white">{{ c.symbol }}</td>
                <td class="py-3.5 px-4 font-mono font-bold text-sm text-[#0e1726] dark:text-white">
                  {{ Number(c.exchange_rate).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 4 }) }}
                </td>
                <td class="py-3.5 px-4">
                  <span v-if="c.is_default" class="badge bg-info/20 text-info rounded-full px-3 py-1 font-bold text-xs uppercase tracking-wide">
                    Base
                  </span>
                  <span v-else class="text-xs text-gray-400 font-mono">—</span>
                </td>
                <td class="py-3.5 px-4">
                  <span
                    v-if="c.is_active"
                    class="badge bg-success/20 text-success rounded-full px-3 py-1 font-bold text-xs uppercase tracking-wide"
                  >
                    Active
                  </span>
                  <span
                    v-else
                    class="badge bg-danger/20 text-danger rounded-full px-3 py-1 font-bold text-xs uppercase tracking-wide"
                  >
                    Disabled
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <TableDropdown>
                    <div class="py-1">
                      <Link
                        :href="route('admin.currencies.edit', c.id)"
                        class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit Currency
                      </Link>
                      <button
                        v-if="!c.is_default"
                        type="button"
                        @click="deleteCurrency(c)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-danger hover:bg-[#fff5f5] dark:hover:bg-[#121e32] transition"
                      >
                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Delete Currency
                      </button>
                    </div>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="currencies.data.length === 0">
                <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400 font-semibold text-sm">
                  No currencies registered yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="currencies" class="mt-4" />
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
  currencies: Object,
  filters: Object,
})

const search = ref(props.filters?.search || '')

const applySearch = () => {
  router.get(route('admin.currencies.index'), { search: search.value }, { preserveState: true })
}

const resetSearch = () => {
  search.value = ''
  router.get(route('admin.currencies.index'))
}

const deleteCurrency = (c) => {
  if (confirm(`Are you sure you want to delete currency "${c.name}" (${c.code})?`)) {
    router.delete(route('admin.currencies.destroy', c.id), { preserveScroll: true })
  }
}
</script>
