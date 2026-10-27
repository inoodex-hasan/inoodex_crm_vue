<template>
    <Head title="Student Profiles" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Student Directory
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Active enrolled student records, passport documentation, and institutional applications.
                    </p>
                </div>
                <div>
                    <Link
                        href="/dashboard/students/create"
                        class="px-4 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs flex items-center space-x-1"
                    >
                        <span>+ Register Student</span>
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
                            placeholder="Search student, phone, email..."
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

            <!-- Table Card -->
            <div class="bg-white dark:bg-[#1b2e4b] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-[#0e1726]/60 border-b border-gray-100 dark:border-gray-800 text-gray-400 font-semibold uppercase tracking-wider">
                                <th class="py-3 px-4">Student</th>
                                <th class="py-3 px-4">Contact & Passport</th>
                                <th class="py-3 px-4">Preferred Destination</th>
                                <th class="py-3 px-4 text-center">Current Stage</th>
                                <th class="py-3 px-4">Counselor</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                            <tr
                                v-for="student in students.data"
                                :key="student.id"
                                class="hover:bg-gray-50/60 dark:hover:bg-dark/20 transition"
                            >
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center shrink-0">
                                            {{ (student.first_name?.[0] || 'S') + (student.last_name?.[0] || '') }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">
                                                {{ student.first_name }} {{ student.last_name }}
                                            </div>
                                            <div class="text-[11px] text-gray-400">
                                                ID: #{{ student.id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ student.phone }}
                                    </div>
                                    <div v-if="student.passport_number" class="text-[11px] font-mono text-gray-500">
                                        PP: {{ student.passport_number }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ student.university?.name || student.preferred_university || '—' }}
                                    </div>
                                    <div v-if="student.country" class="text-[11px] text-primary font-semibold">
                                        {{ student.country?.name }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider',
                                            student.current_stage === 'enrolled' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                            student.current_stage === 'visa' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' :
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                        ]"
                                    >
                                        {{ student.current_stage || 'Admissions' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                    {{ student.consultant_assignee?.name || student.creator?.name || 'Staff' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <TableDropdown>
                                        <div class="py-1">
                                            <Link
                                                :href="`/dashboard/students/${student.id}`"
                                                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                                            >
                                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                View Profile
                                            </Link>
                                            <Link
                                                :href="`/dashboard/students/${student.id}/edit`"
                                                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                                            >
                                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                Edit Student
                                            </Link>
                                            <a
                                                :href="`/dashboard/students/${student.id}/pdf`"
                                                target="_blank"
                                                class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-success transition"
                                            >
                                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                Download PDF
                                            </a>
                                        </div>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!students?.data?.length">
                                <td colspan="6" class="py-12 text-center text-gray-400 text-sm">
                                    No student profiles found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    <Pagination
                        :links="students.links"
                        :from="students.from"
                        :to="students.to"
                        :total="students.total"
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
    students: {
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
    router.get('/dashboard/students', { search: search.value }, {
        preserveState: true,
        replace: true,
    });
}

function clearSearch() {
    search.value = '';
    handleSearch();
}
</script>
