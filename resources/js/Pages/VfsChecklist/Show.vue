<template>
    <Head :title="`VFS Checklist - ${application.application_id}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2">
                        <Link href="/dashboard/vfs-checklist" class="text-xs text-gray-500 hover:text-primary transition font-medium">
                            &larr; Back to Applications
                        </Link>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">
                        {{ application.application_id }} — Document Checklist
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Student: <strong class="text-gray-800 dark:text-gray-200">{{ application.student_name }}</strong>
                        <span v-if="application.university_name"> | University: {{ application.university_name }}</span>
                        <span v-if="application.country_name" class="text-primary font-semibold"> ({{ application.country_name }})</span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="showAddModal = true"
                        class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs flex items-center space-x-1"
                    >
                        <span>+ Add Item</span>
                    </button>
                    <button
                        type="button"
                        @click="syncTemplates"
                        :disabled="syncing"
                        class="px-3.5 py-2 text-xs font-semibold rounded-lg border border-amber-500 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/20 transition flex items-center space-x-1"
                    >
                        <span>{{ syncing ? 'Syncing...' : '↻ Sync List' }}</span>
                    </button>
                </div>
            </div>

            <!-- Main Layout: 2 Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Sidebar: Progress & Bulk Actions -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Progress Card -->
                    <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            Completion Progress
                        </h3>

                        <div class="flex items-baseline justify-between">
                            <span class="text-3xl font-extrabold font-mono text-gray-900 dark:text-white">
                                {{ progressPercentage }}%
                            </span>
                            <span class="text-xs font-mono font-bold text-gray-500">
                                <span class="text-emerald-600 dark:text-emerald-400">{{ checkedCount }}</span> / {{ items.length }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5 overflow-hidden">
                            <div
                                :class="progressPercentage === 100 ? 'bg-emerald-500' : 'bg-primary'"
                                class="h-full rounded-full transition-all duration-300"
                                :style="{ width: progressPercentage + '%' }"
                            />
                        </div>

                        <!-- 100% Celebration Banner -->
                        <div
                            v-if="progressPercentage === 100"
                            class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-center animate__animated animate__fadeIn"
                        >
                            <span class="text-emerald-700 dark:text-emerald-300 font-bold text-xs">
                                ✓ All Documents Collected!
                            </span>
                        </div>
                    </div>

                    <!-- Bulk Actions Card -->
                    <div class="bg-white dark:bg-[#1b2e4b] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            Batch Selection
                        </h3>

                        <button
                            type="button"
                            @click="selectAll"
                            class="w-full py-1.5 px-3 text-xs font-semibold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark/30 transition text-left flex justify-between items-center"
                        >
                            <span>Select All</span>
                            <span class="text-[10px] text-gray-400">{{ items.length }}</span>
                        </button>

                        <button
                            type="button"
                            @click="selectUnchecked"
                            class="w-full py-1.5 px-3 text-xs font-semibold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark/30 transition text-left flex justify-between items-center"
                        >
                            <span>Select Unchecked</span>
                            <span class="text-[10px] text-gray-400">{{ uncheckedCount }}</span>
                        </button>

                        <div v-if="selectedIds.length > 0" class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-2">
                            <button
                                type="button"
                                @click="bulkCheck"
                                class="w-full py-2 px-3 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-xs text-center"
                            >
                                ✓ Check Selected ({{ selectedIds.length }})
                            </button>

                            <button
                                type="button"
                                @click="bulkUncheck"
                                class="w-full py-2 px-3 text-xs font-bold rounded-lg bg-amber-600 hover:bg-amber-700 text-white transition shadow-xs text-center"
                            >
                                ✕ Uncheck Selected ({{ selectedIds.length }})
                            </button>

                            <button
                                type="button"
                                @click="selectedIds = []"
                                class="w-full text-center text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                            >
                                Clear Selection
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Area: Checklist Items Table -->
                <div class="lg:col-span-3 space-y-4">
                    <div class="bg-white dark:bg-[#1b2e4b] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            <div
                                v-for="item in items"
                                :key="item.id"
                                class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/50 dark:hover:bg-dark/20 transition"
                            >
                                <!-- Checkbox & Item Label -->
                                <div class="flex items-start space-x-3 flex-1">
                                    <!-- Multi-select checkbox -->
                                    <input
                                        type="checkbox"
                                        :value="item.id"
                                        v-model="selectedIds"
                                        class="mt-1 w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/20"
                                    />

                                    <!-- Verification toggle button -->
                                    <button
                                        type="button"
                                        @click="toggleItem(item)"
                                        class="mt-0.5 w-5 h-5 rounded flex items-center justify-center transition border shrink-0"
                                        :class="item.is_checked ? 'bg-emerald-500 border-emerald-500 text-white shadow-xs' : 'border-gray-300 dark:border-gray-600 hover:border-primary text-transparent'"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    <!-- Title & Notes -->
                                    <div class="flex-1">
                                        <p
                                            class="text-sm font-semibold transition"
                                            :class="item.is_checked ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-800 dark:text-gray-200'"
                                        >
                                            {{ item.checklist_item }}
                                        </p>

                                        <!-- Verification Details -->
                                        <div v-if="item.is_checked && item.checked_by" class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5">
                                            Verified by {{ item.checked_by?.name || 'Staff' }}
                                        </div>

                                        <!-- Inline Note Display / Edit -->
                                        <div class="mt-2 text-xs">
                                            <div v-if="editingNoteId === item.id" class="flex items-center space-x-2">
                                                <input
                                                    v-model="currentNoteText"
                                                    @keydown.enter="saveNote(item)"
                                                    @keydown.esc="editingNoteId = null"
                                                    placeholder="Add verification notes, document copy #, or remarks..."
                                                    class="flex-1 px-3 py-1 text-xs rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-1 focus:ring-primary"
                                                    autofocus
                                                />
                                                <button
                                                    type="button"
                                                    @click="saveNote(item)"
                                                    class="px-2.5 py-1 text-[11px] font-bold rounded bg-primary text-white"
                                                >
                                                    Save
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="editingNoteId = null"
                                                    class="px-2 py-1 text-[11px] text-gray-500"
                                                >
                                                    Cancel
                                                </button>
                                            </div>

                                            <div v-else class="flex items-center space-x-2">
                                                <span
                                                    v-if="item.notes"
                                                    @click="startEditNote(item)"
                                                    class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-dark/40 px-2.5 py-1 rounded border border-gray-200 dark:border-gray-800 cursor-pointer hover:border-primary transition inline-block text-[11px]"
                                                >
                                                    📝 {{ item.notes }}
                                                </span>
                                                <button
                                                    v-else
                                                    type="button"
                                                    @click="startEditNote(item)"
                                                    class="text-gray-400 hover:text-primary transition text-[11px]"
                                                >
                                                    + Add note
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete item -->
                                <button
                                    type="button"
                                    @click="deleteItem(item)"
                                    class="text-gray-300 hover:text-red-500 transition p-1 self-start sm:self-center"
                                    title="Delete Item"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <div v-if="!items.length" class="p-12 text-center text-gray-400 text-sm">
                                No checklist items recorded for this application.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Add Item -->
            <Modal :show="showAddModal" title="Add Custom Checklist Item" @close="showAddModal = false">
                <form @submit.prevent="submitAddItem" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-500 mb-1">
                            Document / Requirement Title
                        </label>
                        <input
                            v-model="newItemName"
                            type="text"
                            placeholder="e.g. Police Clearance Certificate (Attested)"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            required
                        />
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button
                            type="button"
                            @click="showAddModal = false"
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary/90 transition shadow-xs"
                        >
                            Add Document
                        </button>
                    </div>
                </form>
            </Modal>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
    checklistItems: {
        type: Array,
        default: () => [],
    },
    countryId: {
        type: [Number, String, null],
        default: null,
    },
});

const items = ref([...props.checklistItems]);
const selectedIds = ref([]);
const editingNoteId = ref(null);
const currentNoteText = ref('');
const showAddModal = ref(false);
const newItemName = ref('');
const syncing = ref(false);

const checkedCount = computed(() => items.value.filter(i => i.is_checked).length);
const uncheckedCount = computed(() => items.value.filter(i => !i.is_checked).length);
const progressPercentage = computed(() => {
    if (!items.value.length) return 0;
    return Math.round((checkedCount.value / items.value.length) * 100);
});

async function toggleItem(item) {
    // Optimistic toggle
    item.is_checked = !item.is_checked;
    try {
        const response = await axios.post(`/dashboard/vfs-checklist/items/${item.id}/toggle`);
        item.is_checked = response.data.is_checked;
    } catch (e) {
        // Revert on failure
        item.is_checked = !item.is_checked;
    }
}

function startEditNote(item) {
    editingNoteId.value = item.id;
    currentNoteText.value = item.notes || '';
}

async function saveNote(item) {
    const text = currentNoteText.value;
    try {
        await axios.put(`/dashboard/vfs-checklist/items/${item.id}/notes`, { notes: text });
        item.notes = text;
        editingNoteId.value = null;
    } catch (e) {
        console.error('Failed to save note');
    }
}

function selectAll() {
    selectedIds.value = items.value.map(i => i.id);
}

function selectUnchecked() {
    selectedIds.value = items.value.filter(i => !i.is_checked).map(i => i.id);
}

async function bulkCheck() {
    if (!selectedIds.value.length) return;
    try {
        await axios.post(`/dashboard/vfs-checklist/${props.application.id}/bulk-check`, {
            item_ids: selectedIds.value,
        });
        items.value.forEach(i => {
            if (selectedIds.value.includes(i.id)) {
                i.is_checked = true;
            }
        });
        selectedIds.value = [];
    } catch (e) {
        console.error('Bulk check error');
    }
}

async function bulkUncheck() {
    if (!selectedIds.value.length) return;
    try {
        await axios.post(`/dashboard/vfs-checklist/${props.application.id}/bulk-uncheck`, {
            item_ids: selectedIds.value,
        });
        items.value.forEach(i => {
            if (selectedIds.value.includes(i.id)) {
                i.is_checked = false;
            }
        });
        selectedIds.value = [];
    } catch (e) {
        console.error('Bulk uncheck error');
    }
}

function syncTemplates() {
    syncing.value = true;
    router.post(
        `/dashboard/vfs-checklist/${props.application.id}/sync-templates`,
        {},
        {
            onFinish: () => {
                syncing.value = false;
            },
        }
    );
}

function submitAddItem() {
    if (!newItemName.value) return;
    router.post(
        `/dashboard/vfs-checklist/${props.application.id}/items`,
        { checklist_item: newItemName.value },
        {
            onSuccess: () => {
                showAddModal.value = false;
                newItemName.value = '';
            },
        }
    );
}

function deleteItem(item) {
    if (confirm(`Remove "${item.checklist_item}" from this application?`)) {
        router.delete(`/dashboard/vfs-checklist/items/${item.id}`);
    }
}
</script>
