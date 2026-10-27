<template>
    <Head title="Applications Tracking" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        University Applications
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Track institutional admission progress, offer letters, visa decisions, and fee milestones.
                    </p>
                </div>
                <div>
                    <Link
                        href="/dashboard/applications/create"
                        class="px-4 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs flex items-center space-x-1"
                    >
                        <span>+ New Application</span>
                    </Link>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="bg-white dark:bg-[#1b2e4b] p-3.5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="handleSearch" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-64 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search by ID or Student Name..."
                            class="w-full ltr:pr-9 rtl:pl-9 px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary"
                        />
                        <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button
                            type="submit"
                            class="px-3.5 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary/90 transition shadow-xs"
                        >
                            Search
                        </button>
                        <button
                            v-if="search"
                            type="button"
                            @click="clearSearch"
                            class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-dark/40 transition"
                        >
                            Clear
                        </button>
                    </div>
                </form>
            </div>

            <!-- Applications Table -->
            <div class="bg-white dark:bg-[#1b2e4b] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-[#0e1726]/60 border-b border-gray-100 dark:border-gray-800 text-gray-400 font-semibold uppercase tracking-wider">
                                <th class="py-3 px-4">Application ID</th>
                                <th class="py-3 px-4">Student</th>
                                <th class="py-3 px-4">University & Course</th>
                                <th class="py-3 px-4">Intake</th>
                                <th class="py-3 px-4 text-center">Admission Stage</th>
                                <th class="py-3 px-4 text-center">Visa Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                            <tr
                                v-for="app in applications.data"
                                :key="app.id"
                                class="hover:bg-gray-50/60 dark:hover:bg-dark/20 transition"
                            >
                                <td class="py-3 px-4 font-mono font-bold text-primary">
                                    {{ app.application_id }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ app.student ? (app.student.first_name + ' ' + app.student.last_name) : 'Student #' + app.student_id }}
                                    </div>
                                    <div v-if="app.student?.phone" class="text-[11px] text-gray-400">
                                        {{ app.student.phone }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-800 dark:text-gray-200">
                                        {{ app.university?.name || '—' }}
                                    </div>
                                    <div class="text-[11px] text-primary">
                                        {{ app.course?.name || 'General Course' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    {{ app.intake?.intake_name || app.intake_month || '—' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                                            app.status === 'enrolled' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                            app.status === 'offer' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' :
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                        ]"
                                    >
                                        {{ app.status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                                            app.visa_status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                            app.visa_status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' :
                                            app.visa_status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' :
                                            'bg-gray-100 text-gray-600 dark:bg-dark dark:text-gray-400'
                                        ]"
                                    >
                                        {{ app.visa_status || 'Not applied' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <TableDropdown>
                                        <Link
                                            :href="`/dashboard/applications/${app.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </Link>
                                        <Link
                                            :href="`/dashboard/applications/${app.id}/edit`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Application
                                        </Link>
                                        <Link
                                            :href="`/dashboard/vfs-checklist/${app.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/30 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            VFS Checklist
                                        </Link>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!applications?.data?.length">
                                <td colspan="7" class="py-12 text-center text-gray-400 text-sm">
                                    No applications found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    <Pagination
                        :links="applications.links"
                        :from="applications.from"
                        :to="applications.to"
                        :total="applications.total"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TableDropdown from '@/Components/TableDropdown.vue';

const props = defineProps({
    applications: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const search = ref(props.filters.search || '');

function handleSearch() {
    router.get('/dashboard/applications', { search: search.value }, {
        preserveState: true,
        replace: true,
    });
}

function clearSearch() {
    search.value = '';
    handleSearch();
}
</script>
