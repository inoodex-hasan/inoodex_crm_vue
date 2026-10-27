<template>
  <AdminLayout title="Chart of Accounts">
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Chart of Accounts</h2>
          <p class="text-sm text-slate-500">General ledger accounts structure, classification, and hierarchy.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Account Head Panel -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 h-fit">
          <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Define New Account Head</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Account Type *</label>
              <select v-model="form.type" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm">
                <option value="asset">Asset</option>
                <option value="liability">Liability</option>
                <option value="equity">Equity</option>
                <option value="revenue">Revenue</option>
                <option value="expense">Expense</option>
              </select>
              <p v-if="form.errors.type" class="mt-1 text-xs text-rose-500">{{ form.errors.type }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Parent Head (Optional)</label>
              <select v-model="form.parent_id" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm">
                <option :value="null">No Parent (Top Level)</option>
                <option v-for="p in parentAccounts" :key="p.id" :value="p.id">
                  {{ p.code }} - {{ p.name }} ({{ p.type }})
                </option>
              </select>
              <p v-if="form.errors.parent_id" class="mt-1 text-xs text-rose-500">{{ form.errors.parent_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Account Code *</label>
              <input v-model="form.code" type="text" placeholder="e.g. 1000-01" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.code" class="mt-1 text-xs text-rose-500">{{ form.errors.code }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Account Name *</label>
              <input v-model="form.name" type="text" placeholder="e.g. Cash in Hand" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
            </div>

            <button type="submit" :disabled="form.processing" class="w-full py-2.5 px-4 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50">
              Register Account
            </button>
          </form>
        </div>

        <!-- Ledger Hierarchy Structure -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
          <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Ledger Structure</h3>
            <span class="text-xs text-slate-500">Hierarchical Tree</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                <tr>
                  <th class="px-4 py-3">Code</th>
                  <th class="px-4 py-3">Head Name</th>
                  <th class="px-4 py-3">Type</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <template v-if="accounts.length > 0">
                  <AccountTreeRow
                    v-for="account in accounts"
                    :key="account.id"
                    :account="account"
                    :depth="0"
                  />
                </template>
                <tr v-else>
                  <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                    No accounts defined yet. Start adding heads using the form on the left.
                  </td>
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
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AccountTreeRow from './Partials/AccountTreeRow.vue'

const props = defineProps({
  accounts: Array,
  parentAccounts: Array,
})

const form = useForm({
  type: 'asset',
  parent_id: null,
  code: '',
  name: '',
})

const submit = () => {
  form.post(route('admin.chart-of-accounts.store'), {
    onSuccess: () => form.reset('code', 'name'),
  })
}
</script>
