<script setup>
import { ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    applications: Array,
    users: Array,
})

const form = useForm({
    application_id: '',
    user_id: '',
    amount: '',
    status: 'pending',
    notes: '',
})

watch(() => form.application_id, (newAppId) => {
    if (newAppId) {
        const found = props.applications.find(a => a.id === Number(newAppId))
        if (found && found.created_by) {
            form.user_id = found.created_by
        }
    }
})

function submit() {
    form.post('/dashboard/commissions')
}
</script>

<template>
    <AdminLayout>
        <Head title="Create Commission Claim" />

        <div class="max-w-3xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Add Commission</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Record a manual commission claim for a counsellor/employee.</p>
                </div>
                <Link
                    href="/dashboard/commissions"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    Back to Commissions
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-5">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Select Application <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.application_id"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        >
                            <option value="">-- Select Application --</option>
                            <option v-for="app in applications" :key="app.id" :value="app.id">
                                {{ app.application_id }} &bull; {{ app.student?.first_name }} {{ app.student?.last_name }}
                            </option>
                        </select>
                        <p v-if="form.errors.application_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.application_id }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Commission Payee / Employee <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.user_id"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        >
                            <option value="">-- Select Staff Member --</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">
                                {{ u.name }} ({{ u.email }})
                            </option>
                        </select>
                        <p v-if="form.errors.user_id" class="text-xs text-rose-500 mt-1.5">{{ form.errors.user_id }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                                Amount (BDT) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                v-model="form.amount"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                            />
                            <p v-if="form.errors.amount" class="text-xs text-rose-500 mt-1.5">{{ form.errors.amount }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                            >
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                            </select>
                            <p v-if="form.errors.status" class="text-xs text-rose-500 mt-1.5">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Notes / Comments
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            placeholder="Optional commission remarks..."
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        ></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <Link
                        href="/dashboard/commissions"
                        class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-xs transition"
                    >
                        {{ form.processing ? 'Saving...' : 'Save Commission' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
