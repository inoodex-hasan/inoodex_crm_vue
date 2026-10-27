<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    users: Array,
    defaultMonth: String,
})

const form = useForm({
    user_id: '',
    employee_name: '',
    salary_month: props.defaultMonth || new Date().toISOString().substring(0, 7),
    basic_salary: 0,
    house_rent: 0,
    medical_allowance: 0,
    conveyance: 0,
    other_allowance: 0,
    deductions: 0,
    payment_status: 'pending',
    notes: '',
})

function onUserChange() {
    const found = props.users.find(u => u.id === form.user_id)
    if (found) {
        form.employee_name = found.name
    }
}

const netSalary = computed(() => {
    const gross = Number(form.basic_salary || 0) +
                  Number(form.house_rent || 0) +
                  Number(form.medical_allowance || 0) +
                  Number(form.conveyance || 0) +
                  Number(form.other_allowance || 0)
    return Math.max(0, gross - Number(form.deductions || 0))
})

function submit() {
    form.post('/dashboard/salaries')
}
</script>

<template>
    <AdminLayout>
        <Head title="Create Salary Record" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Create Salary Slip</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Compute employee compensation structure for the selected payroll month.</p>
                </div>
                <Link
                    href="/dashboard/salaries"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to Salaries
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Employee Profile
                        </label>
                        <select
                            v-model="form.user_id"
                            @change="onUserChange"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="">-- Manual or Select Employee --</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">
                                {{ u.name }} ({{ u.email }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Employee Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.employee_name"
                            type="text"
                            required
                            placeholder="Employee full name..."
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.employee_name" class="text-xs text-rose-500 mt-1.5">{{ form.errors.employee_name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Salary Month (YYYY-MM) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.salary_month"
                            type="month"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.salary_month" class="text-xs text-rose-500 mt-1.5">{{ form.errors.salary_month }}</p>
                    </div>
                </div>

                <!-- Earnings Breakdown -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Earnings & Allowances (BDT)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Basic Salary <span class="text-rose-500">*</span></label>
                            <input
                                v-model="form.basic_salary"
                                type="number"
                                step="0.01"
                                required
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">House Rent</label>
                            <input
                                v-model="form.house_rent"
                                type="number"
                                step="0.01"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Medical Allowance</label>
                            <input
                                v-model="form.medical_allowance"
                                type="number"
                                step="0.01"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Conveyance & Other</label>
                            <input
                                v-model="form.conveyance"
                                type="number"
                                step="0.01"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            />
                        </div>
                    </div>
                </div>

                <!-- Deductions & Net Display -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Total Deductions (BDT)</label>
                            <input
                                v-model="form.deductions"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            />
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-right">
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-0.5">Calculated Net Salary</span>
                            <span class="font-mono text-2xl font-black text-primary">BDT {{ netSalary.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <Link
                        href="/dashboard/salaries"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-xs disabled:opacity-50"
                    >
                        {{ form.processing ? 'Saving...' : 'Create Salary Record' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
