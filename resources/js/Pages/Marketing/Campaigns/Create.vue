<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const form = useForm({
    name: '',
    start_date: '',
    end_date: '',
    boosting_status: 'off',
    notes: '',
})

function submit() {
    form.post('/dashboard/marketing/campaigns')
}
</script>

<template>
    <AdminLayout>
        <Head title="Create Marketing Campaign" />

        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">New Campaign</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Launch a new marketing campaign and configure advertising settings.</p>
                </div>
                <Link
                    href="/dashboard/marketing/campaigns"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition shadow-xs"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Campaigns
                </Link>
            </div>

            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Campaign Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g. Autumn Intake 2026 Facebook Ads"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1.5">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Start Date
                        </label>
                        <input
                            v-model="form.start_date"
                            type="date"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.start_date" class="text-xs text-rose-500 mt-1.5">{{ form.errors.start_date }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            End Date
                        </label>
                        <input
                            v-model="form.end_date"
                            type="date"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <p v-if="form.errors.end_date" class="text-xs text-rose-500 mt-1.5">{{ form.errors.end_date }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Initial Boosting Status
                        </label>
                        <select
                            v-model="form.boosting_status"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        >
                            <option value="off">Boosting OFF</option>
                            <option value="on">Boosting ON</option>
                        </select>
                        <p v-if="form.errors.boosting_status" class="text-xs text-rose-500 mt-1.5">{{ form.errors.boosting_status }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Campaign Strategy & Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            placeholder="Target demographics, budget constraints, platforms (FB, IG, Google), and KPIs..."
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        ></textarea>
                        <p v-if="form.errors.notes" class="text-xs text-rose-500 mt-1.5">{{ form.errors.notes }}</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <Link
                        href="/dashboard/marketing/campaigns"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-xs disabled:opacity-50"
                    >
                        {{ form.processing ? 'Creating...' : 'Create Campaign' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
