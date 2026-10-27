<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    salary: Object,
})
</script>

<template>
    <AdminLayout>
        <Head :title="`Salary Slip: ${salary.employee_name}`" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Salary Slip &bull; {{ salary.employee_name }}</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Payroll Month: {{ salary.salary_month }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="`/dashboard/salaries/${salary.id}/edit`"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        Edit
                    </Link>
                    <Link
                        href="/dashboard/salaries"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Back to Salaries
                    </Link>
                </div>
            </div>

            <!-- Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 shadow-xs space-y-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 border-b border-slate-100 dark:border-slate-800 pb-6 text-sm">
                    <div>
                        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Employee</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ salary.employee_name }}</span>
                    </div>
                    <div>
                        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Month</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ salary.salary_month }}</span>
                    </div>
                    <div>
                        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Status</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/10 text-primary">
                            {{ salary.payment_status }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Recorded By</span>
                        <span class="text-slate-700 dark:text-slate-300">{{ salary.creator?.name || 'System' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    <!-- Earnings -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Earnings</h3>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-600 dark:text-slate-400">Basic Salary</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">BDT {{ Number(salary.basic_salary || 0).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-600 dark:text-slate-400">House Rent</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">BDT {{ Number(salary.house_rent || 0).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-600 dark:text-slate-400">Medical Allowance</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">BDT {{ Number(salary.medical_allowance || 0).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-600 dark:text-slate-400">Conveyance & Other</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">BDT {{ Number((salary.conveyance || 0) + (salary.other_allowance || 0)).toLocaleString() }}</span>
                        </div>
                    </div>

                    <!-- Deductions & Payout -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Deductions & Net Pay</h3>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-600 dark:text-slate-400">Total Deductions</span>
                            <span class="font-mono font-semibold text-rose-500">BDT {{ Number(salary.deductions || 0).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b-2 border-slate-200 dark:border-slate-700 font-bold">
                            <span class="text-slate-900 dark:text-white">Net Salary</span>
                            <span class="font-mono text-lg text-primary">BDT {{ Number(salary.net_salary || 0).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-slate-600 dark:text-slate-400">Disbursed (Paid)</span>
                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">BDT {{ Number(salary.paid_amount || 0).toLocaleString() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
