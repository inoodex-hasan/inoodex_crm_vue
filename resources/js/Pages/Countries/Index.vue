<template>
  <AdminLayout title="Countries">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Countries</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Global destination countries for study abroad applications.</p>
        </div>
        <button
          type="button"
          @click="openCreateModal"
          class="btn btn-primary gap-2 self-start sm:self-auto shadow-xs"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add Country
        </button>
      </div>

      <!-- Countries Panel -->
      <div class="panel">
        <!-- Search & Filter Toolbar -->
        <div class="mb-5">
          <form @submit.prevent="applySearch" class="flex flex-wrap items-center gap-2.5">
            <div class="relative w-64 sm:w-72">
              <input
                v-model="search"
                type="text"
                placeholder="Search country name or code..."
                class="form-input ltr:pr-9 rtl:pl-9 text-xs py-2"
              />
              <button type="submit" class="absolute inset-y-0 ltr:right-2.5 rtl:left-2.5 flex items-center text-gray-400 hover:text-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </button>
            </div>
            <div class="flex items-center gap-1.5">
              <button type="submit" class="btn btn-primary btn-sm text-xs py-2 px-3">Search</button>
              <button @click="resetSearch" type="button" class="btn btn-outline-danger btn-sm text-xs py-2 px-3">Reset</button>
            </div>
          </form>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
          <table class="table-hover whitespace-nowrap w-full">
            <thead>
              <tr>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Country Name</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">ISO Code</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Primary Currency</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Status</th>
                <th class="text-center font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
              <tr v-for="c in countries.data" :key="c.id" class="hover:bg-primary/5 transition">
                <td class="py-3.5 px-4 font-bold text-[#0e1726] dark:text-white text-sm">
                  <button type="button" @click="openShowModal(c)" class="hover:text-primary transition-colors text-left font-bold">
                    {{ c.name }}
                  </button>
                </td>
                <td class="py-3.5 px-4">
                  <span class="font-mono font-bold text-xs text-primary bg-primary/10 px-2 py-0.5 rounded uppercase">
                    {{ c.code || '—' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 font-semibold text-[#3b3f5c] dark:text-[#cbd5e1] text-sm">
                  {{ c.currency || '—' }}
                </td>
                <td class="py-3.5 px-4">
                  <span
                    v-if="c.status"
                    class="badge bg-success/20 text-success rounded-full px-3 py-1 font-bold text-xs uppercase tracking-wide"
                  >
                    Active
                  </span>
                  <span
                    v-else
                    class="badge bg-danger/20 text-danger rounded-full px-3 py-1 font-bold text-xs uppercase tracking-wide"
                  >
                    Inactive
                  </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <TableDropdown>
                    <button
                      type="button"
                      @click="openShowModal(c)"
                      class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                    >
                      <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                      View Details
                    </button>
                    <button
                      type="button"
                      @click="openEditModal(c)"
                      class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                    >
                      <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                      Edit Country
                    </button>
                    <button
                      type="button"
                      @click="deleteCountry(c)"
                      class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-danger hover:bg-[#fff5f5] dark:hover:bg-[#121e32] transition border-t border-gray-100 dark:border-gray-800"
                    >
                      <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                      Delete Country
                    </button>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="countries.data.length === 0">
                <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400 font-semibold text-sm">
                  No countries registered yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="countries" class="mt-4" />
      </div>
    </div>

    <!-- Create Country Modal -->
    <Modal :show="showCreateModal" @close="showCreateModal = false" title="Add Country" max-width="md">
      <form @submit.prevent="submitCreate" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Country Name *</label>
          <input
            v-model="createForm.name"
            type="text"
            placeholder="e.g. United Kingdom"
            required
            class="form-input text-sm"
          />
          <p v-if="createForm.errors.name" class="mt-1 text-xs text-danger">{{ createForm.errors.name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">ISO Code</label>
            <input
              v-model="createForm.code"
              type="text"
              placeholder="e.g. GB"
              class="form-input text-sm uppercase"
            />
            <p v-if="createForm.errors.code" class="mt-1 text-xs text-danger">{{ createForm.errors.code }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Currency Code</label>
            <input
              v-model="createForm.currency"
              type="text"
              placeholder="e.g. GBP"
              class="form-input text-sm uppercase"
            />
            <p v-if="createForm.errors.currency" class="mt-1 text-xs text-danger">{{ createForm.errors.currency }}</p>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Status *</label>
          <select v-model="createForm.status" required class="form-select text-sm">
            <option :value="1">Active</option>
            <option :value="0">Inactive</option>
          </select>
          <p v-if="createForm.errors.status" class="mt-1 text-xs text-danger">{{ createForm.errors.status }}</p>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
          <button type="button" @click="showCreateModal = false" class="btn btn-outline-dark text-xs">
            Cancel
          </button>
          <button type="submit" :disabled="createForm.processing" class="btn btn-primary text-xs">
            {{ createForm.processing ? 'Saving...' : 'Create Country' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Edit Country Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false" title="Edit Country" max-width="md">
      <form @submit.prevent="submitEdit" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Country Name *</label>
          <input
            v-model="editForm.name"
            type="text"
            required
            class="form-input text-sm"
          />
          <p v-if="editForm.errors.name" class="mt-1 text-xs text-danger">{{ editForm.errors.name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">ISO Code</label>
            <input
              v-model="editForm.code"
              type="text"
              class="form-input text-sm uppercase"
            />
            <p v-if="editForm.errors.code" class="mt-1 text-xs text-danger">{{ editForm.errors.code }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Currency Code</label>
            <input
              v-model="editForm.currency"
              type="text"
              class="form-input text-sm uppercase"
            />
            <p v-if="editForm.errors.currency" class="mt-1 text-xs text-danger">{{ editForm.errors.currency }}</p>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Status *</label>
          <select v-model="editForm.status" required class="form-select text-sm">
            <option :value="1">Active</option>
            <option :value="0">Inactive</option>
          </select>
          <p v-if="editForm.errors.status" class="mt-1 text-xs text-danger">{{ editForm.errors.status }}</p>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
          <button type="button" @click="showEditModal = false" class="btn btn-outline-dark text-xs">
            Cancel
          </button>
          <button type="submit" :disabled="editForm.processing" class="btn btn-primary text-xs">
            {{ editForm.processing ? 'Updating...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Show Country Modal -->
    <Modal :show="showViewModal" @close="showViewModal = false" :title="selectedCountry?.name || 'Country Details'" max-width="md">
      <div v-if="selectedCountry" class="space-y-4">
        <div class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b] rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] px-5 bg-white dark:bg-[#0e1726]">
          <div class="flex justify-between items-center py-3.5">
            <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Country Name</span>
            <span class="text-sm font-bold text-[#0e1726] dark:text-white">{{ selectedCountry.name }}</span>
          </div>
          <div class="flex justify-between items-center py-3.5">
            <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">ISO Code</span>
            <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2.5 py-1 rounded">{{ selectedCountry.code || '—' }}</span>
          </div>
          <div class="flex justify-between items-center py-3.5">
            <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Primary Currency</span>
            <span class="font-bold text-sm text-[#0e1726] dark:text-white">{{ selectedCountry.currency || '—' }}</span>
          </div>
          <div class="flex justify-between items-center py-3.5">
            <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Status</span>
            <span
              :class="selectedCountry.status ? 'badge bg-success/20 text-success' : 'badge bg-danger/20 text-danger'"
              class="rounded-full px-2.5 py-0.5 font-bold text-xs uppercase"
            >
              {{ selectedCountry.status ? 'Active' : 'Inactive' }}
            </span>
          </div>
          <div v-if="selectedCountry.created_at" class="flex justify-between items-center py-3.5">
            <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Created At</span>
            <span class="text-xs font-semibold text-[#0e1726] dark:text-[#cbd5e1]">{{ new Date(selectedCountry.created_at).toLocaleDateString() }}</span>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-2">
          <button type="button" @click="showViewModal = false" class="btn btn-outline-dark text-xs">
            Close
          </button>
          <button type="button" @click="showViewModal = false; openEditModal(selectedCountry)" class="btn btn-primary text-xs">
            Edit Country
          </button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import TableDropdown from '@/Components/TableDropdown.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  countries: Object,
  filters: Object,
})

const search = ref(props.filters?.search || '')

// Modals State
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const selectedCountry = ref(null)

// Forms
const createForm = useForm({
  name: '',
  code: '',
  currency: '',
  status: 1,
})

const editForm = useForm({
  id: null,
  name: '',
  code: '',
  currency: '',
  status: 1,
})

const openCreateModal = () => {
  createForm.reset()
  createForm.clearErrors()
  showCreateModal.value = true
}

const submitCreate = () => {
  createForm.post(route('admin.countries.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false
      createForm.reset()
    },
  })
}

const openEditModal = (c) => {
  selectedCountry.value = c
  editForm.clearErrors()
  editForm.id = c.id
  editForm.name = c.name
  editForm.code = c.code || ''
  editForm.currency = c.currency || ''
  editForm.status = c.status ? 1 : 0
  showViewModal.value = false
  showEditModal.value = true
}

const submitEdit = () => {
  editForm.put(route('admin.countries.update', editForm.id), {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false
    },
  })
}

const openShowModal = (c) => {
  selectedCountry.value = c
  showViewModal.value = true
}

const applySearch = () => {
  router.get(route('admin.countries.index'), { search: search.value }, { preserveState: true })
}

const resetSearch = () => {
  search.value = ''
  router.get(route('admin.countries.index'))
}

const deleteCountry = (c) => {
  if (confirm(`Are you sure you want to delete "${c.name}"?`)) {
    router.delete(route('admin.countries.destroy', c.id), { preserveScroll: true })
  }
}
</script>
