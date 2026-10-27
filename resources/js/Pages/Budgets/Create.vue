<template>
  <AdminLayout title="Allocate Budget">
    <div class="max-w-xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Allocate Budget</h2>
          <p class="text-sm text-slate-500">Define an authorized expense cap for an operational account head.</p>
        </div>
        <Link :href="route('admin.budgets.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
          Back to List
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Expense Account Head *</label>
            <select v-model="form.chart_of_account_id" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm">
              <option value="">-- Select Expense Head --</option>
              <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                {{ acc.code }} - {{ acc.name }}
              </option>
            </select>
            <p v-if="form.errors.chart_of_account_id" class="mt-1 text-xs text-rose-500">{{ form.errors.chart_of_account_id }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Budget Amount (৳) *</label>
              <input v-model.number="form.amount" type="number" step="0.01" min="0.01" required placeholder="0.00" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.amount" class="mt-1 text-xs text-rose-500">{{ form.errors.amount }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Periodicity *</label>
              <select v-model="form.period" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm">
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
              </select>
              <p v-if="form.errors.period" class="mt-1 text-xs text-rose-500">{{ form.errors.period }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start Date *</label>
              <input v-model="form.start_date" type="date" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.start_date" class="mt-1 text-xs text-rose-500">{{ form.errors.start_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">End Date *</label>
              <input v-model="form.end_date" type="date" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.end_date" class="mt-1 text-xs text-rose-500">{{ form.errors.end_date }}</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Budget Notes</label>
            <textarea v-model="form.notes" rows="2" placeholder="Optional background or allocation justification..." class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm"></textarea>
            <p v-if="form.errors.notes" class="mt-1 text-xs text-rose-500">{{ form.errors.notes }}</p>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-700">
            <Link :href="route('admin.budgets.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50">
              Allocate Budget
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
  chart_of_account_id: '',
  amount: '',
  period: 'monthly',
  start_date: new Date().toISOString().substring(0, 10),
  end_date: new Date(new Date().setMonth(new Date().getMonth() + 1)).toISOString().substring(0, 10),
  notes: '',
})

const submit = () => {
  form.post(route('admin.budgets.store'))
}
</script>
