<template>
  <AdminLayout title="Edit Accounting Period">
    <div class="max-w-xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Accounting Period</h2>
          <p class="text-sm text-slate-500">Update fiscal period dates, classification, or notes.</p>
        </div>
        <Link :href="route('admin.accounting-periods.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
          Back to List
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Period Title *</label>
            <input v-model="form.name" type="text" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Period Classification *</label>
            <select v-model="form.type" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm">
              <option value="fiscal_year">Fiscal Year</option>
              <option value="quarterly">Quarterly</option>
              <option value="monthly">Monthly</option>
            </select>
            <p v-if="form.errors.type" class="mt-1 text-xs text-rose-500">{{ form.errors.type }}</p>
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
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Remarks</label>
            <textarea v-model="form.remarks" rows="2" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm"></textarea>
            <p v-if="form.errors.remarks" class="mt-1 text-xs text-rose-500">{{ form.errors.remarks }}</p>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-700">
            <Link :href="route('admin.accounting-periods.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50">
              Update Period
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
  period: Object,
})

const form = useForm({
  name: props.period.name,
  type: props.period.type,
  start_date: props.period.start_date?.substring(0, 10),
  end_date: props.period.end_date?.substring(0, 10),
  remarks: props.period.remarks || '',
})

const submit = () => {
  form.put(route('admin.accounting-periods.update', props.period.id))
}
</script>
