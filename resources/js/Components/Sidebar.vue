<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    collapsed: {
        type: Boolean,
        default: false,
    },
    mobileOpen: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['close-sidebar'])

const page = usePage()
const currentUrl = computed(() => page.url)

function isCurrent(path) {
    if (path === '/dashboard') {
        return currentUrl.value === '/dashboard'
    }
    return currentUrl.value.startsWith(path)
}

function navClass(path) {
    return [
        'nav-link group flex items-center px-3 py-2 rounded-lg font-medium transition-all duration-200 text-xs',
        isCurrent(path)
            ? 'bg-primary text-white font-semibold shadow-xs'
            : 'text-[#506690] dark:text-[#888ea8] hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20 dark:hover:text-white hover:translate-x-1'
    ]
}
</script>

<template>
    <div>
        <nav
            :class="[
                'sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-50 transition-all duration-300 bg-white dark:bg-[#0e1726]',
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                collapsed ? 'lg:-translate-x-full' : 'lg:translate-x-0'
            ]"
        >
            <div class="bg-white dark:bg-[#0e1726] h-full flex flex-col">
                <!-- Brand / Logo Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                    <Link href="/dashboard" class="main-logo flex items-center shrink-0">
                        <img class="w-8 flex-none" src="/assets/images/logo.svg" alt="Logo" />
                        <span class="text-xl ltr:ml-2 rtl:mr-2 font-bold align-middle text-[#4361ee] dark:text-white transition-all duration-300 tracking-tight">
                            Inoodex <span class="text-gray-800 dark:text-white font-extrabold">CRM</span>
                        </span>
                    </Link>

                    <button
                        type="button"
                        class="collapse-icon w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-500/10 dark:hover:bg-dark-light/10 dark:text-white-light lg:hidden"
                        @click="$emit('close-sidebar')"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links List (Vristo Scrollbar) -->
                <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1 custom-scroll text-sm">
                    <!-- Dashboard -->
                    <Link
                        href="/dashboard"
                        :class="navClass('/dashboard')"
                    >
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </Link>

                    <!-- Section: Admissions & Pipeline -->
                    <div class="pt-3 pb-1">
                        <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-3">
                            Admissions & CRM
                        </span>
                    </div>

                    <Link href="/dashboard/leads" :class="navClass('/dashboard/leads')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span>Leads</span>
                    </Link>

                    <Link href="/dashboard/students" :class="navClass('/dashboard/students')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
                        <span>Students</span>
                    </Link>

                    <Link href="/dashboard/applications" :class="navClass('/dashboard/applications')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>Applications</span>
                    </Link>

                    <Link href="/dashboard/vfs-checklist" :class="navClass('/dashboard/vfs-checklist')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                        <span>VFS Visa Checklist</span>
                    </Link>

                    <!-- Section: Academic Catalog -->
                    <div class="pt-3 pb-1">
                        <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-3">
                            Academic Catalog
                        </span>
                    </div>

                    <Link href="/dashboard/countries" :class="navClass('/dashboard/countries')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Countries</span>
                    </Link>

                    <Link href="/dashboard/universities" :class="navClass('/dashboard/universities')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <span>Universities</span>
                    </Link>

                    <Link href="/dashboard/courses" :class="navClass('/dashboard/courses')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        <span>Courses & Programs</span>
                    </Link>

                    <Link href="/dashboard/course-intakes" :class="navClass('/dashboard/course-intakes')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span>Admission Intakes</span>
                    </Link>

                    <!-- Section: Marketing Pipeline -->
                    <div class="pt-3 pb-1">
                        <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-3">
                            Marketing Pipeline
                        </span>
                    </div>

                    <Link href="/dashboard/marketing/campaigns" :class="navClass('/dashboard/marketing/campaigns')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                        <span>Campaigns</span>
                    </Link>

                    <Link href="/dashboard/marketing/videos" :class="navClass('/dashboard/marketing/videos')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        <span>Video Assets</span>
                    </Link>

                    <Link href="/dashboard/marketing/posters" :class="navClass('/dashboard/marketing/posters')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span>Creative Posters</span>
                    </Link>

                    <Link href="/dashboard/marketing/documents" :class="navClass('/dashboard/marketing/documents')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>Marketing Docs</span>
                    </Link>

                    <!-- Section: Accounting & Ledger -->
                    <div class="pt-3 pb-1">
                        <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-3">
                            Accounting & Ledger
                        </span>
                    </div>

                    <Link href="/dashboard/invoices" :class="navClass('/dashboard/invoices')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" /></svg>
                        <span>Invoices</span>
                    </Link>

                    <Link href="/dashboard/payments" :class="navClass('/dashboard/payments')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span>Payments & Receipts</span>
                    </Link>

                    <Link href="/dashboard/expenses" :class="navClass('/dashboard/expenses')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Expenses</span>
                    </Link>

                    <Link href="/dashboard/office-accounts" :class="navClass('/dashboard/office-accounts')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                        <span>Office Accounts</span>
                    </Link>

                    <Link href="/dashboard/salaries" :class="navClass('/dashboard/salaries')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Payroll & Salaries</span>
                    </Link>

                    <Link href="/dashboard/journal-entries" :class="navClass('/dashboard/journal-entries')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        <span>Journal Entries</span>
                    </Link>

                    <Link href="/dashboard/chart-of-accounts" :class="navClass('/dashboard/chart-of-accounts')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <span>Chart of Accounts</span>
                    </Link>

                    <Link href="/dashboard/budgets" :class="navClass('/dashboard/budgets')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        <span>Budgets</span>
                    </Link>

                    <Link href="/dashboard/accounting-periods" :class="navClass('/dashboard/accounting-periods')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span>Accounting Periods</span>
                    </Link>

                    <Link href="/dashboard/bank-reconciliations" :class="navClass('/dashboard/bank-reconciliations')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        <span>Bank Reconciliation</span>
                    </Link>

                    <Link href="/dashboard/reports/summary" :class="navClass('/dashboard/reports/summary')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>Financial Summary</span>
                    </Link>

                    <Link href="/dashboard/reports/balance-sheet" :class="navClass('/dashboard/reports/balance-sheet')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
                        <span>Balance Sheet</span>
                    </Link>

                    <!-- Section: Commissions -->
                    <div class="pt-3 pb-1">
                        <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-3">
                            Commissions
                        </span>
                    </div>

                    <Link href="/dashboard/commissions" :class="navClass('/dashboard/commissions')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Commission Claims</span>
                    </Link>

                    <Link href="/dashboard/my-commissions" :class="navClass('/dashboard/my-commissions')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span>My Commission Earnings</span>
                    </Link>

                    <!-- Section: Administration -->
                    <div class="pt-3 pb-1">
                        <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-3">
                            Administration
                        </span>
                    </div>

                    <Link href="/dashboard/currencies" :class="navClass('/dashboard/currencies')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Currencies & FX</span>
                    </Link>

                    <Link href="/dashboard/users" :class="navClass('/dashboard/users')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <span>Users & Impersonation</span>
                    </Link>

                    <Link href="/dashboard/roles" :class="navClass('/dashboard/roles')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        <span>Roles & Permissions</span>
                    </Link>

                    <Link href="/dashboard/settings" :class="navClass('/dashboard/settings')">
                        <svg class="w-5 h-5 shrink-0 ltr:mr-2.5 rtl:ml-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>System Settings</span>
                    </Link>
                </div>
            </div>
        </nav>
    </div>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 4px;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.4);
    border-radius: 4px;
}
</style>
