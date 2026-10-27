<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    stats: Object,
    leads: Object,
    students: Object,
    applications: Object,
    payments: Object,
    activeCurrencies: Array,
})

const page = usePage()
const user = computed(() => page.props.auth?.user || {})
const userRoles = computed(() => (user.value.roles || []).map(r => String(r).toLowerCase().replace(/[-_ ]/g, '')))

const hasRole = (role) => {
    const norm = String(role).toLowerCase().replace(/[-_ ]/g, '')
    return userRoles.value.includes(norm)
}
const isAdmin = computed(() => {
    if (userRoles.value.length === 0) return true
    return hasRole('superadmin') || hasRole('administrator') || hasRole('admin')
})
const isEditor = computed(() => hasRole('editor') || isAdmin.value)
const isMarketing = computed(() => hasRole('marketing') || hasRole('digitalmarketing') || isAdmin.value)
const isConsultant = computed(() => hasRole('consultant') || isAdmin.value)
const isApplication = computed(() => hasRole('application') || isAdmin.value)
const isAccountant = computed(() => hasRole('accountant') || isAdmin.value)

// Active Tab for data tables
const activeTab = ref(
    hasRole('marketing') && !isAdmin.value ? 'leads' :
    hasRole('consultant') && !isAdmin.value ? 'students' :
    hasRole('application') && !isAdmin.value ? 'applications' :
    hasRole('accountant') && !isAdmin.value ? 'payments' : 'applications'
)

function formatNumber(val, decimals = 0) {
    if (val === null || val === undefined) return '0'
    return Number(val).toLocaleString(undefined, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    })
}
</script>

<template>
    <AdminLayout :currencies="activeCurrencies">
        <Head title="Vristo Dashboard" />

        <div class="space-y-6">
            <!-- 1. Administration Section (Admin / Super Admin) -->
            <div v-if="isAdmin">
                <h5 class="text-xs font-semibold text-[#506690] uppercase tracking-wider">Administration</h5>
                <div class="pt-5">
                    <div class="mb-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                        <!-- Total Roles -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-success">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_roles) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Roles</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total Privileges -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-info">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_privileges) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Privileges</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total Users -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-warning">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="6" r="4" stroke="currentColor" stroke-width="1.5" />
                                            <ellipse opacity="0.5" cx="12" cy="17" rx="7" ry="4" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_users) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Users</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Education Data Section (Editor / Admin / Super Admin) -->
            <div v-if="isEditor">
                <h5 class="text-xs font-semibold text-[#506690] uppercase tracking-wider">Education Data</h5>
                <div class="pt-5 pb-2">
                    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        <!-- Countries -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-info">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 3C7.03 3 3 7.03 3 12C3 16.97 7.03 21 12 21C16.97 21 21 16.97 21 12C21 7.03 16.97 3 12 3Z" stroke="currentColor" stroke-width="1.5" />
                                            <path d="M3 12H21" stroke="currentColor" stroke-width="1.5" />
                                            <path d="M12 3C14.5 6 14.5 18 12 21C9.5 18 9.5 6 12 3Z" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_countries) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Countries</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Universities -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-success">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 10L12 4L21 10" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M6 10V18M10 10V18M14 10V18M18 10V18" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" />
                                            <path d="M4 18H20" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_universities) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Universities</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Courses -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div>
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 6C5 5.45 5.45 5 6 5H11C12.1 5 13 5.9 13 7V18C13 16.9 12.1 16 11 16H6C5.45 16 5 16.45 5 17V6Z" stroke="#84cc16" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M19 6C19 5.45 18.55 5 18 5H13C11.9 5 11 5.9 11 7V18C11 16.9 11.9 16 13 16H18C18.55 16 19 16.45 19 17V6Z" stroke="#84cc16" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_courses) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Courses</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Overall Overview Section (Admin / Super Admin) -->
            <div v-if="isAdmin">
                <h5 class="text-xs font-semibold text-[#506690] uppercase tracking-wider">Overall Overview</h5>
                <div class="pt-5 pb-2">
                    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                        <!-- Total Leads -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-warning">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" x="2" y="5" width="20" height="14" rx="2" />
                                            <circle stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" cx="8" cy="12" r="2.5" />
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M14 10h4M14 14h4" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_leads) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Leads</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Active Students -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-info">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M22 10l-10-5-10 5 10 5 10-5z" />
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M6 12v5c3 3 9 3 12 0v-5" />
                                            <line stroke="currentColor" stroke-width="1.5" stroke-linecap="round" x1="22" y1="10" x2="22" y2="16" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_students) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Active Students</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total Applications -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-primary">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" points="14 2 14 8 20 8" />
                                            <line stroke="currentColor" stroke-width="1.5" stroke-linecap="round" x1="16" y1="13" x2="8" y2="13" />
                                            <line stroke="currentColor" stroke-width="1.5" stroke-linecap="round" x1="16" y1="17" x2="8" y2="17" />
                                            <line stroke="currentColor" stroke-width="1.5" stroke-linecap="round" x1="10" y1="9" x2="8" y2="9" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_applications) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Applications</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Applications -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-warning">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.pending_applications) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Pending Applications</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total Invoices -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-primary">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h10M7 16h6" />
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9l-6-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_invoices) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Invoices</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total Payments -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-success">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 11h18" />
                                            <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.total_payments) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Payments</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Payments -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-warning">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                                            <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">{{ formatNumber(stats?.pending_payments) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Pending Payments</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Total Revenue -->
                        <div class="panel h-full sm:col-span-2 xl:col-span-1">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="text-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full ltr:ml-3 rtl:mr-3">
                                    <p class="dark:text-white-light text-2xl font-bold">${{ formatNumber(stats?.total_revenue, 2) }}</p>
                                    <h5 class="text-xs font-semibold text-[#506690] uppercase mt-0.5">Total Revenue</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Role-Based Dynamic Data Tables (Vristo Table Styles) -->
            <div class="pt-2">
                <!-- Navigation Tabs if Admin, or contextual view -->
                <div class="flex items-center justify-between border-b border-[#e0e6ed] dark:border-[#1b2e4b] pb-3 mb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            v-if="isApplication"
                            type="button"
                            @click="activeTab = 'applications'"
                            :class="activeTab === 'applications' ? 'btn btn-primary' : 'btn btn-outline-primary'"
                            class="text-xs uppercase"
                        >
                            Applications
                        </button>
                        <button
                            v-if="isMarketing"
                            type="button"
                            @click="activeTab = 'leads'"
                            :class="activeTab === 'leads' ? 'btn btn-primary' : 'btn btn-outline-primary'"
                            class="text-xs uppercase"
                        >
                            Leads
                        </button>
                        <button
                            v-if="isConsultant"
                            type="button"
                            @click="activeTab = 'students'"
                            :class="activeTab === 'students' ? 'btn btn-primary' : 'btn btn-outline-primary'"
                            class="text-xs uppercase"
                        >
                            Students
                        </button>
                        <button
                            v-if="isAccountant"
                            type="button"
                            @click="activeTab = 'payments'"
                            :class="activeTab === 'payments' ? 'btn btn-primary' : 'btn btn-outline-primary'"
                            class="text-xs uppercase"
                        >
                            Payments
                        </button>
                    </div>

                    <div class="text-xs text-[#506690]">
                        Showing live records from database
                    </div>
                </div>

                <!-- Applications Table -->
                <div v-if="activeTab === 'applications'" class="panel p-0 overflow-hidden">
                    <div class="p-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between">
                        <h5 class="font-bold text-sm uppercase text-gray-800 dark:text-white">Recent Applications</h5>
                        <Link href="/dashboard/applications" class="text-xs text-primary hover:underline font-semibold">View All &rarr;</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-hover w-full text-xs">
                            <thead>
                                <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b] text-[#506690] uppercase">
                                    <th class="py-3 px-4">App ID</th>
                                    <th class="py-3 px-4">Student</th>
                                    <th class="py-3 px-4">Destination</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="app in applications?.data || []" :key="app.id" class="border-b border-[#e0e6ed]/50 dark:border-[#1b2e4b]/50">
                                    <td class="py-3 px-4 font-mono font-bold text-primary">{{ app.application_id }}</td>
                                    <td class="py-3 px-4 font-semibold text-gray-800 dark:text-white">
                                        {{ app.student?.first_name }} {{ app.student?.last_name }}
                                    </td>
                                    <td class="py-3 px-4">{{ app.university?.name || '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="badge bg-primary/10 text-primary border border-primary/20 uppercase text-[10px] px-2 py-0.5 rounded">
                                            {{ app.status }}
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
                                        </TableDropdown>
                                    </td>
                                </tr>
                                <tr v-if="(!applications?.data || applications.data.length === 0)">
                                    <td colspan="5" class="text-center py-6 text-gray-400">No applications found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Leads Table -->
                <div v-if="activeTab === 'leads'" class="panel p-0 overflow-hidden">
                    <div class="p-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between">
                        <h5 class="font-bold text-sm uppercase text-gray-800 dark:text-white">Recent Leads</h5>
                        <Link href="/dashboard/leads" class="text-xs text-primary hover:underline font-semibold">View All &rarr;</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-hover w-full text-xs">
                            <thead>
                                <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b] text-[#506690] uppercase">
                                    <th class="py-3 px-4">Lead Name</th>
                                    <th class="py-3 px-4">Email / Phone</th>
                                    <th class="py-3 px-4">Channel</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="lead in leads?.data || []" :key="lead.id" class="border-b border-[#e0e6ed]/50 dark:border-[#1b2e4b]/50">
                                    <td class="py-3 px-4 font-semibold text-gray-800 dark:text-white">{{ lead.name }}</td>
                                    <td class="py-3 px-4">{{ lead.phone || lead.email }}</td>
                                    <td class="py-3 px-4 uppercase text-[11px]">{{ lead.lead_source || 'Inbound' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="badge bg-warning/10 text-warning border border-warning/20 uppercase text-[10px] px-2 py-0.5 rounded">
                                            {{ lead.status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <TableDropdown>
                                            <Link
                                                :href="`/dashboard/leads/${lead.id}`"
                                                class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Details
                                            </Link>
                                        </TableDropdown>
                                    </td>
                                </tr>
                                <tr v-if="(!leads?.data || leads.data.length === 0)">
                                    <td colspan="5" class="text-center py-6 text-gray-400">No leads found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Students Table -->
                <div v-if="activeTab === 'students'" class="panel p-0 overflow-hidden">
                    <div class="p-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between">
                        <h5 class="font-bold text-sm uppercase text-gray-800 dark:text-white">Active Students</h5>
                        <Link href="/dashboard/students" class="text-xs text-primary hover:underline font-semibold">View All &rarr;</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-hover w-full text-xs">
                            <thead>
                                <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b] text-[#506690] uppercase">
                                    <th class="py-3 px-4">Student Name</th>
                                    <th class="py-3 px-4">Email</th>
                                    <th class="py-3 px-4">Phone</th>
                                    <th class="py-3 px-4">Passport</th>
                                    <th class="py-3 px-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="std in students?.data || []" :key="std.id" class="border-b border-[#e0e6ed]/50 dark:border-[#1b2e4b]/50">
                                    <td class="py-3 px-4 font-semibold text-gray-800 dark:text-white">
                                        {{ std.first_name }} {{ std.last_name }}
                                    </td>
                                    <td class="py-3 px-4">{{ std.email }}</td>
                                    <td class="py-3 px-4">{{ std.phone }}</td>
                                    <td class="py-3 px-4 font-mono">{{ std.passport_number || 'N/A' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <TableDropdown>
                                            <Link
                                                :href="`/dashboard/students/${std.id}`"
                                                class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                View Profile
                                            </Link>
                                            <Link
                                                :href="`/dashboard/students/${std.id}/edit`"
                                                class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit Student
                                            </Link>
                                        </TableDropdown>
                                    </td>
                                </tr>
                                <tr v-if="(!students?.data || students.data.length === 0)">
                                    <td colspan="5" class="text-center py-6 text-gray-400">No students found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payments Table -->
                <div v-if="activeTab === 'payments'" class="panel p-0 overflow-hidden">
                    <div class="p-4 border-b border-[#e0e6ed] dark:border-[#1b2e4b] flex items-center justify-between">
                        <h5 class="font-bold text-sm uppercase text-gray-800 dark:text-white">Recent Payments</h5>
                        <Link href="/dashboard/payments" class="text-xs text-primary hover:underline font-semibold">View All &rarr;</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-hover w-full text-xs">
                            <thead>
                                <tr class="border-b border-[#e0e6ed] dark:border-[#1b2e4b] text-[#506690] uppercase">
                                    <th class="py-3 px-4">Receipt #</th>
                                    <th class="py-3 px-4">Student</th>
                                    <th class="py-3 px-4">Amount</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pay in payments?.data || []" :key="pay.id" class="border-b border-[#e0e6ed]/50 dark:border-[#1b2e4b]/50">
                                    <td class="py-3 px-4 font-mono font-bold text-primary">{{ pay.receipt_number || pay.id }}</td>
                                    <td class="py-3 px-4 font-semibold text-gray-800 dark:text-white">
                                        {{ pay.student?.first_name }} {{ pay.student?.last_name }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-success">${{ formatNumber(pay.amount, 2) }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span
                                            :class="pay.payment_status === 'completed' ? 'bg-success/10 text-success border-success/20' : 'bg-warning/10 text-warning border-warning/20'"
                                            class="badge border uppercase text-[10px] px-2 py-0.5 rounded"
                                        >
                                            {{ pay.payment_status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <TableDropdown>
                                            <Link
                                                :href="`/dashboard/payments/${pay.id}/edit`"
                                                class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit Payment
                                            </Link>
                                            <a
                                                :href="`/dashboard/payments/${pay.id}/invoice`"
                                                target="_blank"
                                                class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors border-t border-gray-100 dark:border-gray-700"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                Receipt PDF
                                            </a>
                                        </TableDropdown>
                                    </td>
                                </tr>
                                <tr v-if="(!payments?.data || payments.data.length === 0)">
                                    <td colspan="5" class="text-center py-6 text-gray-400">No payment receipts found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
