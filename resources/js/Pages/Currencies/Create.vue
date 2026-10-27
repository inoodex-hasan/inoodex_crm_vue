<template>
  <AdminLayout title="Add Currency">
    <div class="max-w-xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Add Currency</h2>
          <p class="text-sm text-slate-500">Configure a currency and define its base conversion exchange rate.</p>
        </div>
        <Link :href="route('admin.currencies.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
          Back to List
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Currency Name *</label>
            <input v-model="form.name" type="text" placeholder="e.g. US Dollar or British Pound" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">ISO Code (3 letters) *</label>
              <input v-model="form.code" type="text" maxlength="3" placeholder="e.g. USD, GBP" required class="w-full uppercase font-mono px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.code" class="mt-1 text-xs text-rose-500">{{ form.errors.code }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Symbol *</label>
              <input v-model="form.symbol" type="text" placeholder="e.g. $, £, ৳" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.symbol" class="mt-1 text-xs text-rose-500">{{ form.errors.symbol }}</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Exchange Rate (vs Local Base) *</label>
            <input v-model.number="form.exchange_rate" type="number" step="0.0001" min="0" placeholder="1.0000" required class="w-full font-mono px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
            <p v-if="form.errors.exchange_rate" class="mt-1 text-xs text-rose-500">{{ form.errors.exchange_rate }}</p>
          </div>

          <div class="space-y-3 pt-2">
            <label class="flex items-center space-x-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
              <span>Enable currency in transactions</span>
            </label>

            <label class="flex items-center space-x-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
              <input v-model="form.is_default" type="checkbox" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
              <span>Set as default primary system base currency</span>
            </label>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-700">
            <Link :href="route('admin.currencies.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50">
              Create Currency
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

const form = useForm({
  name: '',
  code: '',
  symbol: '',
  exchange_rate: 1.0000,
  is_active: true,
  is_default: false,
})

const submit = () => {
  form.post(route('admin.currencies.store'))
}
</script>
