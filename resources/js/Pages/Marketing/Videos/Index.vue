<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    videos: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status || '')

function applyFilters() {
    router.get('/dashboard/marketing/videos', {
        search: search.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    search.value = ''
    status.value = ''
    applyFilters()
}

// Add New Video Form
const createForm = useForm({
    video_name: '',
    status: 'pending',
})

function submitCreate() {
    createForm.post('/dashboard/marketing/videos', {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset()
        }
    })
}

// Edit Modal
const showEditModal = ref(false)
const editForm = useForm({
    id: null,
    video_name: '',
    status: 'pending',
})

function openEdit(v) {
    editForm.id = v.id
    editForm.video_name = v.video_name
    editForm.status = v.status
    showEditModal.value = true
}

function submitEdit() {
    editForm.put(`/dashboard/marketing/videos/${editForm.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false
            editForm.reset()
        }
    })
}

function deleteVideo(id, name) {
    if (confirm(`Delete video "${name}"?`)) {
        router.delete(`/dashboard/marketing/videos/${id}`, {
            preserveScroll: true,
        })
    }
}

const statusBadgeClasses = {
    pending: 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
    not_edited: 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50',
    editing: 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/50',
    ready: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50',
    uploaded: 'bg-primary/10 text-primary border border-primary/20',
}
</script>

<template>
    <AdminLayout>
        <Head title="Marketing Videos" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Marketing Videos</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Track video creative production pipeline from ideation to upload.</p>
                </div>
            </div>

            <!-- Add Video Quick Panel -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3">Add Video Creative</h2>
                <form @submit.prevent="submitCreate" class="flex flex-col sm:flex-row gap-3">
                    <input
                        v-model="createForm.video_name"
                        type="text"
                        required
                        placeholder="Enter video title or concept..."
                        class="flex-1 px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                    />
                    <select
                        v-model="createForm.status"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                        <option value="pending">Pending</option>
                        <option value="not_edited">Not Edited</option>
                        <option value="editing">Editing</option>
                        <option value="ready">Ready</option>
                        <option value="uploaded">Uploaded</option>
                    </select>
                    <button
                        type="submit"
                        :disabled="createForm.processing"
                        class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition shadow-xs disabled:opacity-50 shrink-0"
                    >
                        {{ createForm.processing ? 'Adding...' : 'Add Video' }}
                    </button>
                </form>
            </div>

            <!-- Filter Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3 shadow-xs">
                <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative w-60 shrink-0">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search video name..."
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
                        v-model="status"
                        class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="not_edited">Not Edited</option>
                        <option value="editing">Editing</option>
                        <option value="ready">Ready</option>
                        <option value="uploaded">Uploaded</option>
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
                                <th class="px-5 py-3.5">Video Name</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Created Date</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <tr
                                v-for="v in videos.data"
                                :key="v.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">
                                    {{ v.video_name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider',
                                            statusBadgeClasses[v.status] || 'bg-slate-100 text-slate-600'
                                        ]"
                                    >
                                        {{ v.status ? v.status.replace('_', ' ') : '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400">
                                    {{ v.created_at ? new Date(v.created_at).toLocaleDateString() : '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <TableDropdown>
                                        <button
                                            type="button"
                                            @click="openEdit(v)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Video
                                        </button>
                                        <button
                                            type="button"
                                            @click="deleteVideo(v.id, v.video_name)"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Video
                                        </button>
                                    </TableDropdown>
                                </td>
                            </tr>
                            <tr v-if="!videos.data || videos.data.length === 0">
                                <td colspan="4" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                    No marketing videos found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="videos.links" class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    <Pagination :links="videos.links" />
                </div>
            </div>

            <!-- Edit Modal -->
            <Modal :show="showEditModal" @close="showEditModal = false" max-width="md">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Video</h3>
                    <form @submit.prevent="submitEdit" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Video Name</label>
                            <input
                                v-model="editForm.video_name"
                                type="text"
                                required
                                class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Status</label>
                            <select
                                v-model="editForm.status"
                                class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white"
                            >
                                <option value="pending">Pending</option>
                                <option value="not_edited">Not Edited</option>
                                <option value="editing">Editing</option>
                                <option value="ready">Ready</option>
                                <option value="uploaded">Uploaded</option>
                            </select>
                        </div>
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                            <button
                                type="button"
                                @click="showEditModal = false"
                                class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="editForm.processing"
                                class="px-5 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition disabled:opacity-50"
                            >
                                {{ editForm.processing ? 'Saving...' : 'Update' }}
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    </AdminLayout>
</template>
