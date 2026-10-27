<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TableDropdown from '@/Components/TableDropdown.vue'

const props = defineProps({
    templates: Array,
    countries: Array,
})

const createForm = useForm({
    item_name: '',
    country_id: '',
})

function submitCreate() {
    createForm.post('/dashboard/vfs-checklist/templates', {
        onSuccess: () => createForm.reset()
    })
}

const editingTemplate = ref(null)
const editForm = useForm({
    item_name: '',
    country_id: '',
    is_active: true,
})

function startEdit(template) {
    editingTemplate.value = template
    editForm.item_name = template.item_name
    editForm.country_id = template.country_id || ''
    editForm.is_active = Boolean(template.is_active)
}

function submitEdit() {
    editForm.put(`/dashboard/vfs-checklist/templates/${editingTemplate.value.id}`, {
        onSuccess: () => {
            editingTemplate.value = null
        }
    })
}

function deleteTemplate(id) {
    if (confirm('Are you sure you want to delete this checklist template item?')) {
        router.delete(`/dashboard/vfs-checklist/templates/${id}`)
    }
}

function seedDefaults() {
    router.post('/dashboard/vfs-checklist/seed-templates')
}
</script>

<template>
    <AdminLayout>
        <Head title="VFS Checklist Templates" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">VFS Checklist Items</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Master templates for student VFS visa submission checklists.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard/vfs-checklist"
                        class="px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl"
                    >
                        Checklist Index
                    </Link>
                    <button
                        type="button"
                        @click="seedDefaults"
                        class="px-3.5 py-2 text-sm font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl hover:bg-amber-100 transition"
                    >
                        Restore Missing Defaults
                    </button>
                </div>
            </div>

            <!-- Add Item Panel -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3">Add Checklist Item</h2>
                <form @submit.prevent="submitCreate" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="flex-1 w-full">
                        <input
                            type="text"
                            v-model="createForm.item_name"
                            required
                            placeholder="Checklist Item name (e.g., Valid Passport, Bank Solvency)..."
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        />
                    </div>
                    <div class="w-full sm:w-64">
                        <select
                            v-model="createForm.country_id"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white"
                        >
                            <option value="">All Countries (Global)</option>
                            <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <button
                        type="submit"
                        :disabled="createForm.processing"
                        class="w-full sm:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-xl transition shadow-xs whitespace-nowrap"
                    >
                        Add Item
                    </button>
                </form>
            </div>

            <!-- Templates List -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="w-12 px-5 py-4 text-center">#</th>
                                <th class="px-5 py-4">Item Name</th>
                                <th class="px-5 py-4">Country Scope</th>
                                <th class="px-5 py-4 text-center">Active</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="(template, idx) in templates"
                                :key="template.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition"
                            >
                                <td class="px-5 py-4 text-center text-xs font-mono text-slate-400">{{ idx + 1 }}</td>
                                <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">
                                    <template v-if="editingTemplate?.id === template.id">
                                        <input
                                            type="text"
                                            v-model="editForm.item_name"
                                            class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm"
                                        />
                                    </template>
                                    <template v-else>
                                        {{ template.item_name }}
                                    </template>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <template v-if="editingTemplate?.id === template.id">
                                        <select
                                            v-model="editForm.country_id"
                                            class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs"
                                        >
                                            <option value="">Global (All Countries)</option>
                                            <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                                        </select>
                                    </template>
                                    <template v-else>
                                        <span :class="template.country ? 'text-primary font-semibold' : 'text-slate-400'">
                                            {{ template.country?.name || 'All Countries' }}
                                        </span>
                                    </template>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <template v-if="editingTemplate?.id === template.id">
                                        <input
                                            type="checkbox"
                                            v-model="editForm.is_active"
                                            class="rounded border-slate-300 text-primary"
                                        />
                                    </template>
                                    <template v-else>
                                        <span
                                            :class="template.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                        >
                                            {{ template.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </template>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <template v-if="editingTemplate?.id === template.id">
                                        <div class="flex items-center justify-center gap-2">
                                            <button
                                                type="button"
                                                @click="submitEdit"
                                                class="px-2.5 py-1 text-xs font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700"
                                            >
                                                Save
                                            </button>
                                            <button
                                                type="button"
                                                @click="editingTemplate = null"
                                                class="px-2.5 py-1 text-xs font-medium text-slate-500 bg-slate-100 rounded-lg hover:bg-slate-200"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <TableDropdown>
                                            <button
                                                type="button"
                                                @click="startEdit(template)"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit Item
                                            </button>
                                            <button
                                                type="button"
                                                @click="deleteTemplate(template.id)"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition-colors border-t border-gray-100 dark:border-gray-700"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete Item
                                            </button>
                                        </TableDropdown>
                                    </template>
                                </td>
                            </tr>
                            <tr v-if="templates.length === 0">
                                <td colspan="5" class="text-center py-8 text-slate-400">
                                    No checklist templates created. Click "Restore Missing Defaults" above.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
