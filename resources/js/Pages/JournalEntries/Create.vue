<template>
  <AdminLayout title="New Journal Voucher">
    <div class="max-w-5xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Post Journal Voucher</h2>
          <p class="text-sm text-slate-500">Record a double-entry financial voucher with balancing debit and credit entries.</p>
        </div>
        <Link :href="route('admin.journal-entries.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
          Back to Entries
        </Link>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Voucher Header Information -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
          <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Voucher Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Voucher Date *</label>
              <input v-model="form.date" type="date" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.date" class="mt-1 text-xs text-rose-500">{{ form.errors.date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Accounting Period *</label>
              <select v-model="form.period_id" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm">
                <option value="">-- Select Open Period --</option>
                <option v-for="p in periods" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
              <p v-if="form.errors.period_id" class="mt-1 text-xs text-rose-500">{{ form.errors.period_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reference Number</label>
              <input v-model="form.reference_number" type="text" placeholder="Auto-generated if empty" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm" />
              <p v-if="form.errors.reference_number" class="mt-1 text-xs text-rose-500">{{ form.errors.reference_number }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Related Application (Optional)</label>
              <select v-model="form.application_id" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm">
                <option :value="null">-- None (General Voucher) --</option>
                <option v-for="app in applications" :key="app.id" :value="app.id">
                  #{{ app.id }} - {{ app.student ? (app.student.first_name + ' ' + app.student.last_name) : 'Student' }}
                </option>
              </select>
              <p v-if="form.errors.application_id" class="mt-1 text-xs text-rose-500">{{ form.errors.application_id }}</p>
            </div>

            <div class="md:col-span-3">
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Voucher Narration / Note</label>
              <textarea v-model="form.note" rows="2" placeholder="Explain the rationale or background of this transaction..." class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 text-sm"></textarea>
              <p v-if="form.errors.note" class="mt-1 text-xs text-rose-500">{{ form.errors.note }}</p>
            </div>
          </div>
        </div>

        <!-- Double-entry Line Items -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
          <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
              <h3 class="text-base font-semibold text-slate-900 dark:text-white">Debit / Credit Line Items</h3>
              <p class="text-xs text-slate-500">Every voucher requires at least one debit and one credit entry equal in amount.</p>
            </div>
            <button
              @click.prevent="addItem"
              type="button"
              class="px-3 py-1.5 text-xs font-medium text-primary-600 bg-primary-50 dark:bg-primary-900/30 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/50 border border-primary-200 dark:border-primary-800 flex items-center gap-1"
            >
              + Add Item Row
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-600 dark:text-slate-300">
              <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                <tr>
                  <th class="px-4 py-3 w-1/3">Account Head *</th>
                  <th class="px-4 py-3">Description *</th>
                  <th class="px-4 py-3 w-36 text-right">Debit (৳)</th>
                  <th class="px-4 py-3 w-36 text-right">Credit (৳)</th>
                  <th class="px-4 py-3 w-12 text-center"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <tr v-for="(item, index) in form.items" :key="index" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/10">
                  <td class="px-4 py-3">
                    <select v-model="item.chart_of_account_id" required class="w-full px-2.5 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
                      <option value="">-- Select Account Head --</option>
                      <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                        {{ acc.code }} - {{ acc.name }} ({{ acc.type }})
                      </option>
                    </select>
                  </td>
                  <td class="px-4 py-3">
                    <input v-model="item.description" type="text" placeholder="Line item description" required class="w-full px-2.5 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
                  </td>
                  <td class="px-4 py-3 text-right">
                    <input
                      v-model.number="item.debit"
                      @input="onDebitChange(item)"
                      type="number"
                      step="0.01"
                      min="0"
                      placeholder="0.00"
                      class="w-full text-right px-2.5 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-mono"
                    />
                  </td>
                  <td class="px-4 py-3 text-right">
                    <input
                      v-model.number="item.credit"
                      @input="onCreditChange(item)"
                      type="number"
                      step="0.01"
                      min="0"
                      placeholder="0.00"
                      class="w-full text-right px-2.5 py-1.5 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-mono"
                    />
                  </td>
                  <td class="px-4 py-3 text-center">
                    <button
                      v-if="form.items.length > 2"
                      @click.prevent="removeItem(index)"
                      type="button"
                      class="text-rose-500 hover:text-rose-700"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-slate-50 dark:bg-slate-700/50 border-t-2 border-slate-300 dark:border-slate-600 font-semibold text-slate-900 dark:text-white text-xs">
                <tr>
                  <td colspan="2" class="px-4 py-3 text-right uppercase">Totals:</td>
                  <td class="px-4 py-3 text-right font-mono text-sm">৳{{ Number(totalDebit).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                  <td class="px-4 py-3 text-right font-mono text-sm">৳{{ Number(totalCredit).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="5" class="px-4 py-2 text-center text-xs">
                    <span v-if="isBalanced && totalDebit > 0" class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      Voucher is Balanced (Difference: ৳0.00)
                    </span>
                    <span v-else class="text-rose-600 dark:text-rose-400 font-bold flex items-center justify-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                      Out of Balance by ৳{{ Number(Math.abs(totalDebit - totalCredit)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                    </span>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div v-if="form.errors.msg" class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-lg">
          {{ form.errors.msg }}
        </div>

        <div class="flex items-center justify-end space-x-3">
          <Link :href="route('admin.journal-entries.index')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="form.processing || !isBalanced || totalDebit <= 0"
            class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50"
          >
            Post Journal Voucher
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  accounts: Array,
  periods: Array,
  applications: Array,
})

const form = useForm({
  date: new Date().toISOString().substring(0, 10),
  period_id: props.periods?.[0]?.id || '',
  application_id: null,
  reference_number: '',
  note: '',
  items: [
    { chart_of_account_id: '', description: '', debit: null, credit: null },
    { chart_of_account_id: '', description: '', debit: null, credit: null },
  ],
})

const onDebitChange = (item) => {
  if (item.debit > 0) item.credit = null
}

const onCreditChange = (item) => {
  if (item.credit > 0) item.debit = null
}

const addItem = () => {
  form.items.push({ chart_of_account_id: '', description: '', debit: null, credit: null })
}

const removeItem = (index) => {
  if (form.items.length > 2) {
    form.items.splice(index, 1)
  }
}

const totalDebit = computed(() => {
  return form.items.reduce((sum, item) => sum + (Number(item.debit) || 0), 0)
})

const totalCredit = computed(() => {
  return form.items.reduce((sum, item) => sum + (Number(item.credit) || 0), 0)
})

const isBalanced = computed(() => {
  return Math.abs(totalDebit.value - totalCredit.value) <= 0.01
})

const submit = () => {
  form.post(route('admin.journal-entries.store'))
}
</script>
