<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    applications: Object,
    filters: Object,
    selectedApplication: Object,
    documents: Object,
    allApplications: Array,
})

const search = ref(props.filters?.search || '')
const sopStatus = ref(props.filters?.sop_status || '')
const cvStatus = ref(props.filters?.cv_status || '')
const clStatus = ref(props.filters?.cl_status || '')
const vfsDate = ref(props.filters?.vfs_date || '')

function applyFilters() {
    router.get('/dashboard/marketing/documents', {
        search: search.value || undefined,
        sop_status: sopStatus.value || undefined,
        cv_status: cvStatus.value || undefined,
        cl_status: clStatus.value || undefined,
        vfs_date: vfsDate.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    sopStatus.value = ''
    cvStatus.value = ''
    clStatus.value = ''
    vfsDate.value = ''
    applyFilters()
}

// Update Modal
const showUpdateModal = ref(false)
const targetApp = ref(null)
const updateForm = useForm({
    application_id: null,
    documents: {
        sop: { status: 'not_received' },
        cv: { status: 'not_received' },
        cl: { status: 'not_received' },
    }
})

function openUpdate(app) {
    targetApp.value = app
    updateForm.application_id = app.id
    
    // Find documents from app.documents array
    const docMap = {}
    if (app.documents) {
        app.documents.forEach(d => {
            docMap[d.document_type] = d.status
        })
    }
    
    updateForm.documents.sop.status = docMap.sop || 'not_received'
    updateForm.documents.cv.status = docMap.cv || 'not_received'
    updateForm.documents.cl.status = docMap.cl || 'not_received'

    showUpdateModal.value = true
}

function submitUpdate() {
    updateForm.post('/dashboard/marketing/documents', {
        preserveScroll: true,
        onSuccess: () => {
            showUpdateModal.value = false
        }
    })
}

const docBadgeClasses = {
    not_received: 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
    received: 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/50',
    ready: 'bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200/50 dark:border-sky-800/50',
    submitted: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50',
}

function getDocStatus(app, type) {
    if (!app.documents) return 'not_received'
    const found = app.documents.find(d => d.document_type === type)
    return found ? found.status : 'not_received'
}

function isOverdue(app) {
    if (!app.vfs_appointment_date) return false
    return new Date(app.vfs_appointment_date) < new Date() && !app.is_fully_submitted
}
</script>

<template>
    <AdminLayout>
        <Head title="Marketing Documents & SOPs" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Documents Pipeline</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Monitor Statement of Purpose (SOP), Curriculum Vitae (CV), and Cover Letter (CL) drafting and VFS deadlines.</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3 shadow-xs">
                <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-60 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="App ID or Student..."
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
                        v-model="sopStatus"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                        <option value="">SOP (All)</option>
                        <option value="not_received">Not Received</option>
                        <option value="received">Received</option>
                        <option value="ready">Ready</option>
                        <option value="submitted">Submitted</option>
                    </select>
                    <select
                        v-model="cvStatus"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                        <option value="">CV (All)</option>
                        <option value="not_received">Not Received</option>
                        <option value="received">Received</option>
                        <option value="ready">Ready</option>
                        <option value="submitted">Submitted</option>
                    </select>
                    <select
                        v-model="clStatus"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                        <option value="">CL (All)</option>
                        <option value="not_received">Not Received</option>
                        <option value="received">Received</option>
                        <option value="ready">Ready</option>
                        <option value="submitted">Submitted</option>
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
                                <th class="px-5 py-3.5">Application ID</th>
                                <th class="px-5 py-3.5">Student</th>
                                <th class="px-5 py-3.5 text-center">VFS Appointment</th>
                                <th class="px-5 py-3.5 text-center">SOP</th>
                                <th class="px-5 py-3.5 text-center">CV</th>
                                <th class="px-5 py-3.5 text-center">CL</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="app in applications.data"
                                :key="app.id"
                                :class="[
                                    'hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors',
                                    app.is_fully_submitted ? 'opacity-70 bg-slate-50/30 dark:bg-slate-900/30' : ''
                                ]"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <Link
                                            :href="`/dashboard/applications/${app.id}`"
                                            class="font-mono font-bold text-primary hover:underline"
                                        >
                                            {{ app.application_id }}
                                        </Link>
                                        <span
                                            v-if="app.is_fully_submitted"
                                            class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400"
                                        >
                                            Complete
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium text-slate-900 dark:text-white">
                                        {{ app.student ? (app.student.first_name + ' ' + (app.student.last_name || '')) : 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div :class="isOverdue(app) ? 'text-rose-600 font-bold' : 'text-slate-600 dark:text-slate-300'">
                                        {{ app.vfs_appointment_date ? new Date(app.vfs_appointment_date).toLocaleDateString() : '-' }}
                                        <span v-if="isOverdue(app)" class="block text-[10px] uppercase tracking-wider text-rose-500 font-black">
                                            Overdue
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold capitalize',
                                            docBadgeClasses[getDocStatus(app, 'sop')]
                                        ]"
                                    >
                                        {{ getDocStatus(app, 'sop').replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold capitalize',
                                            docBadgeClasses[getDocStatus(app, 'cv')]
                                        ]"
                                    >
                                        {{ getDocStatus(app, 'cv').replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold capitalize',
                                            docBadgeClasses[getDocStatus(app, 'cl')]
                                        ]"
                                    >
                                        {{ getDocStatus(app, 'cl').replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <button
                                            type="button"
                                            @click="openUpdate(app)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Update Documents
                                        </button>
                                        <Link
                                            :href="`/dashboard/applications/${app.id}`"
                                            class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Application
                                        </Link>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!applications.data || applications.data.length === 0">
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No application documents found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="applications.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="applications.links" />
                </div>
            </div>

            <!-- Update Documents Modal -->
            <Modal :show="showUpdateModal" @close="showUpdateModal = false" max-width="md">
                <div v-if="targetApp" class="p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Update Application Documents</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ targetApp.application_id }} &bull; {{ targetApp.student?.first_name }} {{ targetApp.student?.last_name }}
                        </p>
                    </div>

                    <form @submit.prevent="submitUpdate" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Statement of Purpose (SOP)</label>
                            <select
                                v-model="updateForm.documents.sop.status"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            >
                                <option value="not_received">Not Received</option>
                                <option value="received">Received</option>
                                <option value="ready">Ready</option>
                                <option value="submitted">Submitted</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Curriculum Vitae (CV)</label>
                            <select
                                v-model="updateForm.documents.cv.status"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            >
                                <option value="not_received">Not Received</option>
                                <option value="received">Received</option>
                                <option value="ready">Ready</option>
                                <option value="submitted">Submitted</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Cover Letter (CL)</label>
                            <select
                                v-model="updateForm.documents.cl.status"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            >
                                <option value="not_received">Not Received</option>
                                <option value="received">Received</option>
                                <option value="ready">Ready</option>
                                <option value="submitted">Submitted</option>
                            </select>
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                            <button
                                type="button"
                                @click="showUpdateModal = false"
                                class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="updateForm.processing"
                                class="px-5 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition disabled:opacity-50"
                            >
                                {{ updateForm.processing ? 'Saving...' : 'Save Statuses' }}
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    </AdminLayout>
</template>
