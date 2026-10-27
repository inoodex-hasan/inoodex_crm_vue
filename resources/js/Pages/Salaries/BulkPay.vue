<template>
  <AdminLayout title="Bulk Salary Disbursement">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Bulk Salary Disbursement</h2>
          <p class="text-sm text-slate-500">Pay multiple pending or partial salary vouchers simultaneously.</p>
        </div>
        <Link :href="route('admin.salaries.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
          Back to Salaries
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <h3 class="text-base font-semibold text-slate-900 dark:text-white">Selected Salaries ({{ salaries.length }})</h3>
          <p class="text-sm text-slate-500">Total disbursement required: <span class="font-semibold text-emerald-600 dark:text-emerald-400">৳{{ Number(totalAmount).toLocaleString() }}</span></p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400">
              <tr>
                <th class="px-6 py-3">Employee</th>
                <th class="px-6 py-3">Month</th>
                <th class="px-6 py-3">Net Salary</th>
                <th class="px-6 py-3">Paid So Far</th>
                <th class="px-6 py-3">Due to Pay</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="salary in salaries" :key="salary.id">
                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ salary.employee_name }}</td>
                <td class="px-6 py-4">{{ salary.month }}</td>
                <td class="px-6 py-4">৳{{ Number(salary.net_salary).toLocaleString() }}</td>
                <td class="px-6 py-4">৳{{ Number(salary.paid_amount || 0).toLocaleString() }}</td>
                <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400">
                  ৳{{ Number(salary.net_salary - (salary.paid_amount || 0)).toLocaleString() }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Disbursement Date *</label>
              <input v-model="form.payment_date" type="date" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 text-sm" />
              <p v-if="form.errors.payment_date" class="mt-1 text-xs text-rose-500">{{ form.errors.payment_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Payment Method *</label>
              <select v-model="form.payment_method" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 text-sm">
                <option value="bank_transfer">Bank Transfer</option>
                <option value="cash">Cash</option>
                <option value="mobile_banking">Mobile Banking</option>
                <option value="cheque">Cheque</option>
              </select>
              <p v-if="form.errors.payment_method" class="mt-1 text-xs text-rose-500">{{ form.errors.payment_method }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Debit From Office Account</label>
              <select v-model="form.office_account_id" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 text-sm">
                <option :value="null">-- Select Office Account --</option>
                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                  {{ acc.name }} ({{ acc.bank_name || 'Cash' }}) - ৳{{ Number(acc.current_balance || 0).toLocaleString() }}
                </option>
              </select>
              <p v-if="form.errors.office_account_id" class="mt-1 text-xs text-rose-500">{{ form.errors.office_account_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Expense Chart Category *</label>
              <select v-model="form.chart_of_account_id" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 text-sm">
                <option value="">-- Select Expense Category --</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.code }} - {{ cat.name }}
                </option>
              </select>
              <p v-if="form.errors.chart_of_account_id" class="mt-1 text-xs text-rose-500">{{ form.errors.chart_of_account_id }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Disbursement Notes</label>
              <textarea v-model="form.notes" rows="2" placeholder="e.g., Office staff salaries for the month via direct transfer" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 text-sm"></textarea>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200 dark:border-slate-700">
            <Link :href="route('admin.salaries.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="px-6 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50">
              Disburse ৳{{ Number(totalAmount).toLocaleString() }}
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
  salaries: Array,
  totalAmount: [Number, String],
  accounts: Array,
  categories: Array,
})

const form = useForm({
  salary_ids: props.salaries.map(s => s.id),
  payment_date: new Date().toISOString().substring(0, 10),
  payment_method: 'bank_transfer',
  office_account_id: props.accounts?.[0]?.id || null,
  chart_of_account_id: props.categories?.[0]?.id || '',
  notes: '',
})

const submit = () => {
  form.post(route('admin.salaries.bulk-pay'))
}
</script>
