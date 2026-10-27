<template>
  <tr :class="[
    depth === 0 ? 'bg-slate-50/70 dark:bg-slate-800/40 font-semibold text-slate-900 dark:text-white' : 'hover:bg-slate-50/50 dark:hover:bg-slate-800/20 text-slate-700 dark:text-slate-300',
    'transition-colors text-sm'
  ]">
    <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400">
      {{ account.code }}
    </td>
    <td class="px-4 py-3">
      <div class="flex items-center">
        <!-- Indentation indicators -->
        <template v-if="depth > 0">
          <div v-for="i in depth" :key="i" class="w-4 h-4 border-l border-slate-300 dark:border-slate-700 ml-1"></div>
          <div class="w-2 border-b border-slate-300 dark:border-slate-700 mr-2"></div>
        </template>
        <span :class="account.is_default ? 'text-blue-600 dark:text-blue-400 font-medium' : ''">
          {{ account.name }}
        </span>
      </div>
    </td>
    <td class="px-4 py-3">
      <span :class="typeBadgeClass(account.type)" class="px-2 py-0.5 text-xs font-semibold rounded-full uppercase">
        {{ account.type }}
      </span>
    </td>
    <td class="px-4 py-3">
      <span :class="account.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border border-rose-200 dark:border-rose-800'" class="px-2 py-0.5 text-xs font-medium rounded-full">
        {{ account.is_active ? 'Active' : 'Disabled' }}
      </span>
    </td>
    <td class="px-4 py-3 text-center">
      <TableDropdown>
        <Link
          :href="route('admin.chart-of-accounts.edit', account.id)"
          class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
        >
          <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Edit Account
        </Link>
        <button
          @click="toggleStatus(account)"
          class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
          :class="account.is_active ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          {{ account.is_active ? 'Disable Account' : 'Enable Account' }}
        </button>
        <button
          v-if="!account.is_default"
          @click="deleteAccount(account)"
          class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Delete Account
        </button>
      </TableDropdown>
    </td>
  </tr>

  <!-- Recursive Child Rows -->
  <template v-if="account.children && account.children.length > 0">
    <AccountTreeRow
      v-for="child in account.children"
      :key="child.id"
      :account="child"
      :depth="depth + 1"
    />
  </template>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
  account: Object,
  depth: {
    type: Number,
    default: 0
  }
})

const typeBadgeClass = (type) => {
  switch (type) {
    case 'asset':
      return 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
    case 'liability':
      return 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
    case 'equity':
      return 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'
    case 'revenue':
      return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
    case 'expense':
      return 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
    default:
      return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
  }
}

const toggleStatus = (account) => {
  router.post(route('admin.chart-of-accounts.status', account.id), {}, { preserveScroll: true })
}

const deleteAccount = (account) => {
  if (confirm(`Deleting "${account.name}" (${account.code}) is irreversible. Continue?`)) {
    router.delete(route('admin.chart-of-accounts.destroy', account.id), { preserveScroll: true })
  }
}
</script>
