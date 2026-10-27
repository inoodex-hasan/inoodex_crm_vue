<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    salaries: Array,
    month: String,
})

const currentMonth = ref(props.month || new Date().toISOString().substring(0, 7))

function changeMonth() {
    router.get('/dashboard/salaries/generate', { month: currentMonth.value }, { preserveState: true })
}

const form = useForm({
    month: props.month || currentMonth.value,
    salaries: (props.salaries || []).map(s => ({
        id: s.id || null,
        user_id: s.user_id || null,
        employee_name: s.name || s.employee_name || '',
        designation: s.designation || 'Staff',
        basic_salary: Number(s.basic_salary || 0),
        bonus: Number(s.bonus || 0),
        deduction: Number(s.deduction || 0),
        account_number: s.account_number || '',
        selected: true,
    }))
})

function addRow() {
    form.salaries.push({
        id: null,
        user_id: null,
        employee_name: '',
        designation: 'Staff',
        basic_salary: 0,
        bonus: 0,
        deduction: 0,
        account_number: '',
        selected: true,
    })
}

function removeRow(index) {
    form.salaries.splice(index, 1)
}

const totalPayroll = computed(() => {
    return form.salaries
        .filter(s => s.selected)
        .reduce((sum, s) => {
            return sum + (Number(s.basic_salary || 0) + Number(s.bonus || 0) - Number(s.deduction || 0))
        }, 0)
})

function submit() {
    // Filter only selected rows for submission
    const selectedRows = form.salaries.filter(s => s.selected).map(s => ({
        id: s.id,
        user_id: s.user_id,
        employee_name: s.employee_name,
        designation: s.designation,
        basic_salary: s.basic_salary,
        bonus: s.bonus,
        deduction: s.deduction,
        account_number: s.account_number,
    }))

    router.post('/dashboard/salaries/bulk-store', {
        month: form.month,
        salaries: selectedRows,
    })
}
</script>

<template>
    <AdminLayout>
        <Head title="Generate Monthly Salaries" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Generate Salaries</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Batch compute and generate payroll records for {{ month }}.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard/salaries"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                    >
                        Back to Salaries
                    </Link>
                </div>
            </div>

            <!-- Month Selection Panel -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Target Month:</label>
                    <input
                        type="month"
                        v-model="currentMonth"
                        class="px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-white"
                    />
                    <button
                        type="button"
                        @click="changeMonth"
                        class="px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl transition"
                    >
                        Load Month
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="addRow"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-xl transition"
                    >
                        + Add Custom Employee
                    </button>
                </div>
            </div>

            <!-- Table of Salaries -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="w-12 px-4 py-3.5 text-center">Include</th>
                                <th class="px-4 py-3.5">Employee Name</th>
                                <th class="px-4 py-3.5">Basic (BDT)</th>
                                <th class="px-4 py-3.5">Bonus</th>
                                <th class="px-4 py-3.5">Deduction</th>
                                <th class="px-4 py-3.5">Net Salary</th>
                                <th class="px-4 py-3.5">Account / Bank</th>
                                <th class="w-16 px-4 py-3.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="(item, index) in form.salaries"
                                :key="index"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition"
                            >
                                <td class="px-4 py-3 text-center">
                                    <input
                                        type="checkbox"
                                        v-model="item.selected"
                                        class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4"
                                    />
                                </td>
                                <td class="px-4 py-3">
                                    <input
                                        type="text"
                                        v-model="item.employee_name"
                                        required
                                        placeholder="Employee Name"
                                        class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white"
                                    />
                                </td>
                                <td class="px-4 py-3 w-32">
                                    <input
                                        type="number"
                                        step="0.01"
                                        v-model="item.basic_salary"
                                        required
                                        class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white text-right"
                                    />
                                </td>
                                <td class="px-4 py-3 w-28">
                                    <input
                                        type="number"
                                        step="0.01"
                                        v-model="item.bonus"
                                        class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white text-right"
                                    />
                                </td>
                                <td class="px-4 py-3 w-28">
                                    <input
                                        type="number"
                                        step="0.01"
                                        v-model="item.deduction"
                                        class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white text-right"
                                    />
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white text-right">
                                    ${{ (Number(item.basic_salary || 0) + Number(item.bonus || 0) - Number(item.deduction || 0)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                </td>
                                <td class="px-4 py-3">
                                    <input
                                        type="text"
                                        v-model="item.account_number"
                                        placeholder="Account #"
                                        class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white"
                                    />
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        @click="removeRow(index)"
                                        class="text-rose-500 hover:text-rose-700 p-1"
                                    >
                                        &times;
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="form.salaries.length === 0">
                                <td colspan="8" class="text-center py-8 text-slate-400">
                                    No employees found for this month. Click "+ Add Custom Employee" to add one.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary and Action -->
                <div class="p-5 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                        Total Payroll Net: <span class="text-lg font-bold text-primary ml-1">${{ totalPayroll.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link
                            href="/dashboard/salaries"
                            class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="button"
                            @click="submit"
                            :disabled="form.processing || form.salaries.length === 0"
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                        >
                            Save & Generate All Salaries
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
