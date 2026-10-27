<template>
  <AdminLayout title="Journal Voucher Details">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center space-x-3">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ journalEntry.reference_number }}</h2>
            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 uppercase">
              {{ journalEntry.status }}
            </span>
          </div>
          <p class="text-sm text-slate-500">General ledger voucher transaction details.</p>
        </div>
        <div class="flex items-center space-x-3">
          <a
            :href="route('admin.journal-entries.report', { reference_number: journalEntry.reference_number })"
            target="_blank"
            class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700"
          >
            PDF Voucher
          </a>
          <Link
            :href="route('admin.journal-entries.index')"
            class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700"
          >
            Back to List
          </Link>
        </div>
      </div>

      <!-- Voucher Metadata -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
          <div>
            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Date</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ journalEntry.date ? new Date(journalEntry.date).toLocaleDateString() : '—' }}</span>
          </div>
          <div>
            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Accounting Period</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ journalEntry.period?.name || '—' }}</span>
          </div>
          <div>
            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Posted By</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ journalEntry.creator?.name || 'System' }}</span>
          </div>
          <div>
            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Linked Student</span>
            <span class="font-semibold text-slate-900 dark:text-white">
              {{ journalEntry.application?.student ? (journalEntry.application.student.first_name + ' ' + journalEntry.application.student.last_name) : 'None' }}
            </span>
          </div>
          <div v-if="journalEntry.note" class="col-span-2 md:col-span-4 pt-4 border-t border-slate-200 dark:border-slate-700">
            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase mb-1">Narration / Note</span>
            <p class="text-slate-700 dark:text-slate-300 italic">{{ journalEntry.note }}</p>
          </div>
        </div>
      </div>

      <!-- Items Table -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <h3 class="text-base font-semibold text-slate-900 dark:text-white">Debit / Credit Line Items</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
              <tr>
                <th class="px-6 py-3">Account Code</th>
                <th class="px-6 py-3">Account Head</th>
                <th class="px-6 py-3">Description</th>
                <th class="px-6 py-3 text-right">Debit (৳)</th>
                <th class="px-6 py-3 text-right">Credit (৳)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="item in journalEntry.items" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/20">
                <td class="px-6 py-4 font-mono text-xs font-semibold text-primary-600 dark:text-primary-400">
                  {{ item.chart_of_account?.code || '—' }}
                </td>
                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                  {{ item.chart_of_account?.name || '—' }}
                  <span class="text-xs text-slate-400 ml-1 uppercase">({{ item.chart_of_account?.type }})</span>
                </td>
                <td class="px-6 py-4">{{ item.description }}</td>
                <td class="px-6 py-4 text-right font-mono font-medium text-slate-900 dark:text-white">
                  {{ Number(item.debit) > 0 ? ('৳' + Number(item.debit).toLocaleString(undefined, { minimumFractionDigits: 2 })) : '—' }}
                </td>
                <td class="px-6 py-4 text-right font-mono font-medium text-slate-900 dark:text-white">
                  {{ Number(item.credit) > 0 ? ('৳' + Number(item.credit).toLocaleString(undefined, { minimumFractionDigits: 2 })) : '—' }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50 dark:bg-slate-700/50 border-t-2 border-slate-300 dark:border-slate-600 font-bold text-slate-900 dark:text-white text-sm">
              <tr>
                <td colspan="3" class="px-6 py-3 text-right uppercase text-xs">Total:</td>
                <td class="px-6 py-3 text-right font-mono text-emerald-600 dark:text-emerald-400">
                  ৳{{ Number(totalDebit).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                </td>
                <td class="px-6 py-3 text-right font-mono text-emerald-600 dark:text-emerald-400">
                  ৳{{ Number(totalCredit).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  journalEntry: Object,
})

const totalDebit = computed(() => {
  return props.journalEntry.items?.reduce((sum, i) => sum + (Number(i.debit) || 0), 0) || 0
})

const totalCredit = computed(() => {
  return props.journalEntry.items?.reduce((sum, i) => sum + (Number(i.credit) || 0), 0) || 0
})
</script>
