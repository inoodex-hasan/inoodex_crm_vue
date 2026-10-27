<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    countryGroups: Array,
    totalApplications: Number,
    uniqueStudents: Number,
    totalCountries: Number,
    totalUniversities: Number,
    filterCountries: Array,
    filterUniversities: Array,
    filters: Object,
})

const selectedCountry = ref(props.filters?.country_id || '')
const selectedUniversity = ref(props.filters?.university_id || '')
const dateFrom = ref(props.filters?.date_from || '')
const dateTo = ref(props.filters?.date_to || '')

function applyFilters() {
    router.get('/dashboard/applications/country-report', {
        country_id: selectedCountry.value,
        university_id: selectedUniversity.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, { preserveState: true })
}

function clearFilters() {
    selectedCountry.value = ''
    selectedUniversity.value = ''
    dateFrom.value = ''
    dateTo.value = ''
    router.get('/dashboard/applications/country-report')
}
</script>

<template>
    <AdminLayout>
        <Head title="Country-wise Applications Report" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Country-wise Student Applications Report
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Breakdown of admissions and enrollments distributed across destinations and universities.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        href="/dashboard/applications"
                        class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"
                    >
                        Back to Applications
                    </Link>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total Applications</div>
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ totalApplications }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1">Unique Students</div>
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ uniqueStudents }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
                    <div class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-1">Destination Countries</div>
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ totalCountries }}</div>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
                    <div class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1">Partner Universities</div>
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ totalUniversities }}</div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
                <form @submit.prevent="applyFilters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Country</label>
                        <select
                            v-model="selectedCountry"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm"
                        >
                            <option value="">All Countries</option>
                            <option v-for="c in filterCountries" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">University</label>
                        <select
                            v-model="selectedUniversity"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm"
                        >
                            <option value="">All Universities</option>
                            <option v-for="u in filterUniversities" :key="u.id" :value="u.id">{{ u.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">From Date</label>
                        <input
                            type="date"
                            v-model="dateFrom"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">To Date</label>
                        <input
                            type="date"
                            v-model="dateTo"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl transition"
                        >
                            Filter
                        </button>
                        <button
                            type="button"
                            @click="clearFilters"
                            class="px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl"
                        >
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Country Grouped Accordion / Panels -->
            <div class="space-y-6">
                <div
                    v-for="country in countryGroups"
                    :key="country.country_id || 'unassigned'"
                    class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden"
                >
                    <!-- Country Header -->
                    <div class="p-5 bg-slate-50/70 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ country.country_name }}
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary">
                                {{ country.total_applications }} Applications
                            </span>
                        </div>
                        <div class="text-xs font-medium text-slate-500">
                            {{ country.unique_students }} Unique Students &bull; {{ Object.keys(country.universities || {}).length }} Universities
                        </div>
                    </div>

                    <!-- Universities Under this Country -->
                    <div class="p-5 space-y-6">
                        <div
                            v-for="(uni, uniName) in country.universities"
                            :key="uniName"
                            class="space-y-3"
                        >
                            <div class="flex items-center justify-between text-sm font-semibold text-slate-800 dark:text-slate-200">
                                <span>{{ uniName }}</span>
                                <span class="text-xs text-slate-500 font-normal">
                                    {{ uni.total_applications }} apps &bull; {{ uni.unique_students }} students
                                </span>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                                    <thead class="bg-slate-50 dark:bg-slate-800 text-[11px] uppercase tracking-wider text-slate-500">
                                        <tr>
                                            <th class="px-4 py-2.5">Application ID</th>
                                            <th class="px-4 py-2.5">Student</th>
                                            <th class="px-4 py-2.5">Course</th>
                                            <th class="px-4 py-2.5">Intake</th>
                                            <th class="px-4 py-2.5 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        <tr
                                            v-for="app in uni.applications"
                                            :key="app.id"
                                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30"
                                        >
                                            <td class="px-4 py-2 font-mono font-semibold text-primary">
                                                <Link :href="`/dashboard/applications/${app.id}`" class="hover:underline">
                                                    {{ app.application_id }}
                                                </Link>
                                            </td>
                                            <td class="px-4 py-2 font-medium text-slate-900 dark:text-white">
                                                {{ app.student?.first_name }} {{ app.student?.last_name }}
                                            </td>
                                            <td class="px-4 py-2">{{ app.course?.name || '-' }}</td>
                                            <td class="px-4 py-2">{{ app.intake?.name || '-' }}</td>
                                            <td class="px-4 py-2 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                                    {{ app.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="countryGroups.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-12 text-center text-slate-400">
                    No application data matches the specified filters.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
