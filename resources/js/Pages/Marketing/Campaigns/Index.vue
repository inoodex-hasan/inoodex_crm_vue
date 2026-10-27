<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    campaigns: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const boostingStatus = ref(props.filters?.boosting_status || '')

function applyFilters() {
    router.get('/dashboard/marketing/campaigns', {
        search: search.value || undefined,
        boosting_status: boostingStatus.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    boostingStatus.value = ''
    applyFilters()
}

function toggleBoosting(campaign) {
    router.post(`/dashboard/marketing/campaigns/${campaign.id}/toggle-boosting`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            campaign.boosting_status = campaign.boosting_status === 'on' ? 'off' : 'on'
        }
    })
}

function deleteCampaign(id, name) {
    if (confirm(`Delete campaign "${name}" and all associated metadata?`)) {
        router.delete(`/dashboard/marketing/campaigns/${id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Marketing Campaigns" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Marketing Campaigns</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Track and manage ad campaigns, boosting statuses, and lead generation initiatives.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard/marketing/campaigns/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl shadow-sm transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Campaign
                    </Link>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3 shadow-xs">
                <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-60 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search campaign name..."
                            class="w-full ltr:pr-9 rtl:pl-9 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                        />
                        <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                            </svg>
                        </span>
                    </div>
                    <select
                        v-model="boostingStatus"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                        <option value="">All Statuses</option>
                        <option value="on">Boosting ON</option>
                        <option value="off">Boosting OFF</option>
                    </select>
                    <div class="flex items-center gap-2 shrink-0">
                        <button
                            type="submit"
                            class="px-3 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-xs shrink-0"
                        >
                            Filter
                        </button>
                        <button
                            type="button"
                            @click="resetFilters"
                            class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition shrink-0"
                        >
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                                <th class="px-5 py-3.5">Campaign Name</th>
                                <th class="px-5 py-3.5">Start Date</th>
                                <th class="px-5 py-3.5">End Date</th>
                                <th class="px-5 py-3.5">Created By</th>
                                <th class="px-5 py-3.5">Boosting</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="c in campaigns.data"
                                :key="c.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4">
                                    <Link
                                        :href="`/dashboard/marketing/campaigns/${c.id}`"
                                        class="font-semibold text-primary hover:underline block"
                                    >
                                        {{ c.name }}
                                    </Link>
                                    <span class="text-xs text-slate-400 dark:text-slate-500">
                                        Created {{ c.created_at ? new Date(c.created_at).toLocaleDateString() : '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ c.start_date ? new Date(c.start_date).toLocaleDateString() : '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'text-sm',
                                            c.end_date && new Date(c.end_date) < new Date() ? 'text-rose-500 font-medium' : 'text-slate-600 dark:text-slate-300'
                                        ]"
                                    >
                                        {{ c.end_date ? new Date(c.end_date).toLocaleDateString() : '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ c.creator?.name || 'System' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="toggleBoosting(c)"
                                            :class="[
                                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden',
                                                c.boosting_status === 'on' ? 'bg-primary' : 'bg-slate-200 dark:bg-slate-700'
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out',
                                                    c.boosting_status === 'on' ? 'translate-x-5' : 'translate-x-0'
                                                ]"
                                            />
                                        </button>
                                        <span
                                            :class="[
                                                'text-xs font-bold uppercase tracking-wider',
                                                c.boosting_status === 'on' ? 'text-primary' : 'text-slate-400'
                                            ]"
                                        >
                                            {{ c.boosting_status === 'on' ? 'ON' : 'OFF' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/marketing/campaigns/${c.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </Link>
                                        <Link
                                            :href="`/dashboard/marketing/campaigns/${c.id}/edit`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Campaign
                                        </Link>
                                        <button
                                            type="button"
                                            @click="deleteCampaign(c.id, c.name)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Campaign
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!campaigns.data || campaigns.data.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No marketing campaigns found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="campaigns.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="campaigns.links" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
