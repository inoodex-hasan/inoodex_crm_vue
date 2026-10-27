<template>
  <AdminLayout title="Edit Account Head">
    <div class="max-w-xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Account Head</h2>
          <p class="text-sm text-slate-500">Update general ledger code, classification, or parent linkage.</p>
        </div>
        <Link :href="route('admin.chart-of-accounts.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
          Back to List
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
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
            <input v-model="form.code" type="text" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
            <p v-if="form.errors.code" class="mt-1 text-xs text-rose-500">{{ form.errors.code }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Account Name *</label>
            <input v-model="form.name" type="text" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-700">
            <Link :href="route('admin.chart-of-accounts.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50">
              Update Account
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
  account: Object,
  parentAccounts: Array,
})

const form = useForm({
  type: props.account.type,
  parent_id: props.account.parent_id,
  code: props.account.code,
  name: props.account.name,
})

const submit = () => {
  form.put(route('admin.chart-of-accounts.update', props.account.id))
}
</script>
