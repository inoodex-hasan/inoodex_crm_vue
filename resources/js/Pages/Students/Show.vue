<template>
    <Head :title="`Student: ${student.first_name} ${student.last_name}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <Link href="/dashboard/students" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                        &larr; Back to Students
                    </Link>
                    <div class="flex items-center space-x-3 mt-1">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ student.first_name }} {{ student.last_name }}
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                            {{ student.current_stage || 'Admissions' }}
                        </span>
                        <span class="text-xs font-mono text-gray-400">ID: #{{ student.id }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <Link
                        :href="`/dashboard/applications/create?student_id=${student.id}`"
                        class="px-4 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs flex items-center space-x-1"
                    >
                        <span>+ New Application</span>
                    </Link>

                    <Link
                        :href="`/dashboard/students/${student.id}/edit`"
                        class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-dark/40 transition"
                    >
                        Edit Profile
                    </Link>

                    <a
                        :href="`/dashboard/students/${student.id}/pdf`"
                        target="_blank"
                        class="px-4 py-2 text-xs font-semibold rounded-lg border border-emerald-500 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition flex items-center space-x-1"
                    >
                        <span>📄 Download PDF</span>
                    </a>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="flex space-x-2 border-b border-gray-200 dark:border-gray-800 text-xs font-bold">
                <button
                    type="button"
                    @click="activeTab = 'profile'"
                    :class="[
                        'pb-3 px-4 border-b-2 transition uppercase tracking-wider',
                        activeTab === 'profile'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                    ]"
                >
                    Profile & Details
                </button>
                <button
                    type="button"
                    @click="activeTab = 'applications'"
                    :class="[
                        'pb-3 px-4 border-b-2 transition uppercase tracking-wider flex items-center space-x-1.5',
                        activeTab === 'applications'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                    ]"
                >
                    <span>Applications</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-gray-100 dark:bg-dark text-gray-600 dark:text-gray-400">
                        {{ student.applications?.length || 0 }}
                    </span>
                </button>
                <button
                    type="button"
                    @click="activeTab = 'documents'"
                    :class="[
                        'pb-3 px-4 border-b-2 transition uppercase tracking-wider',
                        activeTab === 'documents'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                    ]"
                >
                    Uploaded Documents
                </button>
            </div>

            <!-- Tab 1: Profile & Demographics -->
            <div v-if="activeTab === 'profile'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Info Card -->
                <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 pb-2 border-b border-gray-100 dark:border-gray-800">
                        Contact & Identity
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Phone:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ student.phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Email:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ student.email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Passport Number:</span>
                            <span class="font-mono font-bold text-primary">{{ student.passport_number || 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Passport Expiry:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ student.passport_expiry_date || 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Date of Birth:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ student.date_of_birth || 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Gender:</span>
                            <span class="font-semibold capitalize text-gray-800 dark:text-gray-200">{{ student.gender || 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Target Destination & Package Card -->
                <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 pb-2 border-b border-gray-100 dark:border-gray-800">
                        Academic Target & Accounts
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Destination Country:</span>
                            <span class="font-bold text-primary">{{ student.country?.name || 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Target University:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ student.university?.name || '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Agreed Package:</span>
                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ student.package_amount ? '$' + Number(student.package_amount).toLocaleString() : 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Assigned Counselor:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ student.consultant_assignee?.name || 'Unassigned' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Registered By:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ student.creator?.name || 'Staff' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Applications List -->
            <div v-if="activeTab === 'applications'" class="bg-white dark:bg-[#1b2e4b] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">
                        Applications Submitted for {{ student.first_name }}
                    </h3>
                    <Link
                        :href="`/dashboard/applications/create?student_id=${student.id}`"
                        class="px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary/90 transition shadow-xs"
                    >
                        + Add Application
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-[#0e1726]/60 text-gray-400 uppercase font-semibold">
                                <th class="py-3 px-4">Application ID</th>
                                <th class="py-3 px-4">University & Course</th>
                                <th class="py-3 px-4">Intake</th>
                                <th class="py-3 px-4 text-center">Stage</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                            <tr v-for="app in student.applications" :key="app.id" class="hover:bg-gray-50/50 dark:hover:bg-dark/20 transition">
                                <td class="py-3 px-4 font-mono font-bold text-primary">
                                    {{ app.application_id }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ app.university?.name || 'N/A' }}</div>
                                    <div class="text-[11px] text-gray-400">{{ app.course?.name || 'General Admission' }}</div>
                                </td>
                                <td class="py-3 px-4">{{ app.intake?.intake_name || app.intake_month || '—' }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ app.status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link
                                            :href="`/dashboard/vfs-checklist/${app.id}`"
                                            class="px-2.5 py-1 text-xs font-semibold rounded bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 hover:bg-purple-100"
                                        >
                                            VFS Checklist &rarr;
                                        </Link>
                                        <Link
                                            :href="`/dashboard/applications/${app.id}`"
                                            class="px-2.5 py-1 text-xs font-semibold rounded bg-primary/10 text-primary hover:bg-primary hover:text-white transition"
                                        >
                                            View
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!student.applications?.length">
                                <td colspan="5" class="p-8 text-center text-gray-400">
                                    No applications submitted for this student yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Documents -->
            <div v-if="activeTab === 'documents'" class="bg-white dark:bg-[#1b2e4b] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 pb-2 border-b border-gray-100 dark:border-gray-800">
                    Academic Certificates & Translations
                </h3>

                <div v-if="student.documents?.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <div
                        v-for="(doc, idx) in student.documents"
                        :key="idx"
                        class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark/30 flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-2 truncate">
                            <span class="text-lg">📄</span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">
                                {{ doc.name || 'Document #' + (idx + 1) }}
                            </span>
                        </div>
                        <a
                            :href="`/files/download/${doc.path}`"
                            target="_blank"
                            class="text-xs text-primary font-bold hover:underline shrink-0 ml-2"
                        >
                            Download
                        </a>
                    </div>
                </div>
                <div v-else class="text-xs text-gray-400 py-6 text-center">
                    No academic or passport files uploaded yet.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    student: {
        type: Object,
        required: true,
    },
});

const activeTab = ref('profile');
</script>
