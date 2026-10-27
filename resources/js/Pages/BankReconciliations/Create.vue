<template>
  <AdminLayout title="Start Bank Reconciliation">
    <div class="max-w-xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Start Bank Reconciliation</h2>
          <p class="text-sm text-slate-500">Enter closing date and final statement balance from your bank statement.</p>
        </div>
        <Link :href="route('admin.bank-reconciliations.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
          Back to List
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Office Bank Account *</label>
            <select v-model="form.account_id" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm">
              <option value="">-- Select Bank Account --</option>
              <option v-for="a in accounts" :key="a.id" :value="a.id">
                {{ a.name }} ({{ a.account_number }}) - Current ৳{{ Number(a.remaining_balance || a.current_balance || 0).toLocaleString() }}
              </option>
            </select>
            <p v-if="form.errors.account_id" class="mt-1 text-xs text-rose-500">{{ form.errors.account_id }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bank Statement Closing Date *</label>
            <input v-model="form.statement_date" type="date" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
            <p v-if="form.errors.statement_date" class="mt-1 text-xs text-rose-500">{{ form.errors.statement_date }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ending Statement Balance (৳) *</label>
            <input v-model.number="form.statement_balance" type="number" step="0.01" placeholder="0.00" required class="w-full font-mono px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
            <p v-if="form.errors.statement_balance" class="mt-1 text-xs text-rose-500">{{ form.errors.statement_balance }}</p>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-700">
            <Link :href="route('admin.bank-reconciliations.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50">
              Start Matching Transactions
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  accounts: Array,
})

const form = useForm({
  account_id: props.accounts?.[0]?.id || '',
  statement_date: new Date().toISOString().substring(0, 10),
  statement_balance: '',
})

const submit = () => {
  form.post(route('admin.bank-reconciliations.store'))
}
</script>
