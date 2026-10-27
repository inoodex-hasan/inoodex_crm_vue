<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    invoice: Object,
})
</script>

<template>
    <AdminLayout>
        <Head :title="`Invoice: ${invoice.invoice_number}`" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Invoice {{ invoice.invoice_number }}</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Issued to {{ invoice.student ? (invoice.student.first_name + ' ' + (invoice.student.last_name || '')) : 'Student' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        :href="`/dashboard/invoices/${invoice.id}/pdf`"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Download PDF
                    </a>
                    <Link
                        href="/dashboard/invoices"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Back to Invoices
                    </Link>
                </div>
            </div>

            <!-- Invoice Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 shadow-xs space-y-8">
                <!-- Top metadata -->
                <div class="flex flex-col md:flex-row justify-between gap-6 border-b border-slate-100 dark:border-slate-800 pb-8">
                    <div>
                        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Billed To</span>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                            {{ invoice.student ? (invoice.student.first_name + ' ' + (invoice.student.last_name || '')) : '-' }}
                        </h2>
                        <p class="text-sm text-slate-500">{{ invoice.student?.email }}</p>
                        <p class="text-sm text-slate-500">{{ invoice.student?.phone }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Invoice Date</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ invoice.date ? new Date(invoice.date).toLocaleDateString() : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Due Date</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ invoice.due_date ? new Date(invoice.due_date).toLocaleDateString() : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Status</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/10 text-primary">
                                {{ invoice.status }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Application ID</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ invoice.application?.application_id || '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-400 font-semibold">
                                <th class="pb-3">Item Description</th>
                                <th class="pb-3 text-center">Qty</th>
                                <th class="pb-3 text-right">Unit Price</th>
                                <th class="pb-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="item in invoice.items" :key="item.id">
                                <td class="py-3.5">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ item.description }}</span>
                                    <span class="text-xs text-slate-400">{{ item.chart_of_account?.name }}</span>
                                </td>
                                <td class="py-3.5 text-center font-mono">{{ Number(item.quantity) }}</td>
                                <td class="py-3.5 text-right font-mono">BDT {{ Number(item.unit_price).toLocaleString() }}</td>
                                <td class="py-3.5 text-right font-mono font-bold text-slate-900 dark:text-white">BDT {{ Number(item.total).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-200 dark:border-slate-700">
                                <td colspan="3" class="pt-4 text-right font-bold text-slate-700 dark:text-slate-300">Total Amount</td>
                                <td class="pt-4 text-right font-mono text-xl font-black text-primary">
                                    BDT {{ Number(invoice.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div v-if="invoice.notes" class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Notes & Payment Instructions</span>
                    <p class="text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ invoice.notes }}</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
