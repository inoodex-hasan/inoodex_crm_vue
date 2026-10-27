<template>
  <AdminLayout title="Universities">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Universities & Institutions</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Partner academic universities and colleges across destinations.</p>
        </div>
        <button
          type="button"
          @click="openCreateModal"
          class="btn btn-primary gap-2 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add University
        </button>
      </div>

      <!-- Universities Panel -->
      <div class="panel">
        <!-- Search & Filters Toolbar -->
        <div class="mb-5">
          <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
            <div class="relative w-60 shrink-0">
              <input
                v-model="search"
                type="text"
                placeholder="Search by university name..."
                class="form-input ltr:pr-9 rtl:pl-9 text-xs py-2 w-full"
              />
              <button type="submit" class="absolute inset-y-0 ltr:right-2.5 rtl:left-2.5 flex items-center text-gray-400 hover:text-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </button>
            </div>
            <select v-model="countryId" class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9">
              <option value="">All Countries</option>
              <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <div class="flex items-center gap-1.5 shrink-0">
              <button type="submit" class="btn btn-primary btn-sm text-xs py-2 px-3">Filter</button>
              <button @click="resetFilters" type="button" class="btn btn-outline-danger btn-sm text-xs py-2 px-3">Reset</button>
            </div>
          </form>
        </div>

        <!-- Universities Table -->
        <div class="table-responsive">
          <table class="table-hover whitespace-nowrap w-full">
            <thead>
              <tr>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Institution Name</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Country</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Website</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Contact</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Status</th>
                <th class="text-right font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
              <tr v-for="u in universities.data" :key="u.id" class="hover:bg-primary/5 transition">
                <td class="py-3.5 px-4">
                  <button
                    type="button"
                    @click="openShowModal(u)"
                    class="font-bold text-[#0e1726] dark:text-white text-sm hover:text-primary transition text-left"
                  >
                    {{ u.name }}
                  </button>
                  <div v-if="u.short_name" class="text-xs text-primary font-mono font-semibold">{{ u.short_name }}</div>
                </td>
                <td class="py-3.5 px-4 font-semibold text-[#0e1726] dark:text-[#d0d2d6] text-sm">
                  {{ u.country?.name || '—' }}
                </td>
                <td class="py-3.5 px-4">
                  <a v-if="u.website" :href="u.website" target="_blank" class="text-primary hover:underline text-xs font-semibold flex items-center gap-1">
                    Visit Website
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                  </a>
                  <span v-else class="text-xs text-gray-400 font-mono">—</span>
                </td>
                <td class="py-3.5 px-4 text-xs font-medium">
                  <div class="text-[#0e1726] dark:text-[#d0d2d6] font-semibold">{{ u.email || '—' }}</div>
                  <div v-if="u.phone" class="text-[#506690] dark:text-[#888ea8] mt-0.5">{{ u.phone }}</div>
                </td>
                <td class="py-3.5 px-4">
                  <span
                    v-if="u.status"
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
                <td class="py-3.5 px-4 text-right">
                  <TableDropdown>
                    <div class="py-1">
                      <button
                        type="button"
                        @click="openShowModal(u)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        View Details
                      </button>
                      <button
                        type="button"
                        @click="openEditModal(u)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit University
                      </button>
                      <button
                        type="button"
                        @click="deleteUniversity(u)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-danger hover:bg-[#fff5f5] dark:hover:bg-[#121e32] transition"
                      >
                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Delete University
                      </button>
                    </div>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="universities.data.length === 0">
                <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-400 font-semibold text-sm">
                  No universities found matching criteria.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="universities" class="mt-4" />
      </div>
    </div>

    <!-- Create University Modal -->
    <Modal :show="showCreateModal" @close="showCreateModal = false" title="Add University" max-width="lg">
      <form @submit.prevent="submitCreate" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">University Name *</label>
            <input
              v-model="createForm.name"
              type="text"
              placeholder="e.g. University of Manchester"
              required
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.name" class="mt-1 text-xs text-danger">{{ createForm.errors.name }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Country *</label>
            <select v-model="createForm.country_id" required class="form-select text-sm">
              <option value="">-- Select Country --</option>
              <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <p v-if="createForm.errors.country_id" class="mt-1 text-xs text-danger">{{ createForm.errors.country_id }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Short Code</label>
            <input
              v-model="createForm.short_name"
              type="text"
              placeholder="e.g. UoM"
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.short_name" class="mt-1 text-xs text-danger">{{ createForm.errors.short_name }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Website URL</label>
            <input
              v-model="createForm.website"
              type="url"
              placeholder="https://..."
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.website" class="mt-1 text-xs text-danger">{{ createForm.errors.website }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Contact Email</label>
            <input
              v-model="createForm.email"
              type="email"
              placeholder="admissions@university.edu"
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.email" class="mt-1 text-xs text-danger">{{ createForm.errors.email }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Contact Phone</label>
            <input
              v-model="createForm.phone"
              type="text"
              placeholder="+44 ..."
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.phone" class="mt-1 text-xs text-danger">{{ createForm.errors.phone }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Status *</label>
            <select v-model="createForm.status" required class="form-select text-sm">
              <option :value="1">Active</option>
              <option :value="0">Inactive</option>
            </select>
            <p v-if="createForm.errors.status" class="mt-1 text-xs text-danger">{{ createForm.errors.status }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Campus Address</label>
            <textarea
              v-model="createForm.address"
              rows="2"
              placeholder="Full physical or mailing address..."
              class="form-textarea text-sm"
            ></textarea>
            <p v-if="createForm.errors.address" class="mt-1 text-xs text-danger">{{ createForm.errors.address }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
          <button type="button" @click="showCreateModal = false" class="btn btn-outline-dark text-xs">
            Cancel
          </button>
          <button type="submit" :disabled="createForm.processing" class="btn btn-primary text-xs">
            {{ createForm.processing ? 'Saving...' : 'Create University' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Edit University Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false" title="Edit University" max-width="lg">
      <form @submit.prevent="submitEdit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">University Name *</label>
            <input
              v-model="editForm.name"
              type="text"
              required
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.name" class="mt-1 text-xs text-danger">{{ editForm.errors.name }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Country *</label>
            <select v-model="editForm.country_id" required class="form-select text-sm">
              <option value="">-- Select Country --</option>
              <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <p v-if="editForm.errors.country_id" class="mt-1 text-xs text-danger">{{ editForm.errors.country_id }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Short Code</label>
            <input
              v-model="editForm.short_name"
              type="text"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.short_name" class="mt-1 text-xs text-danger">{{ editForm.errors.short_name }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Website URL</label>
            <input
              v-model="editForm.website"
              type="url"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.website" class="mt-1 text-xs text-danger">{{ editForm.errors.website }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Contact Email</label>
            <input
              v-model="editForm.email"
              type="email"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.email" class="mt-1 text-xs text-danger">{{ editForm.errors.email }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Contact Phone</label>
            <input
              v-model="editForm.phone"
              type="text"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.phone" class="mt-1 text-xs text-danger">{{ editForm.errors.phone }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Status *</label>
            <select v-model="editForm.status" required class="form-select text-sm">
              <option :value="1">Active</option>
              <option :value="0">Inactive</option>
            </select>
            <p v-if="editForm.errors.status" class="mt-1 text-xs text-danger">{{ editForm.errors.status }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Campus Address</label>
            <textarea
              v-model="editForm.address"
              rows="2"
              class="form-textarea text-sm"
            ></textarea>
            <p v-if="editForm.errors.address" class="mt-1 text-xs text-danger">{{ editForm.errors.address }}</p>
          </div>
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

    <!-- Show University Modal -->
    <Modal :show="showViewModal" @close="showViewModal = false" :title="selectedUniversity?.name || 'University Details'" max-width="lg">
      <div v-if="selectedUniversity" class="space-y-4">
        <div class="rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] p-5 bg-white dark:bg-[#0e1726] space-y-4">
          <div class="flex justify-between items-start pb-3 border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Institution</span>
              <div class="text-base font-bold text-[#0e1726] dark:text-white">{{ selectedUniversity.name }}</div>
              <div v-if="selectedUniversity.short_name" class="text-xs text-primary font-mono font-semibold mt-0.5">
                {{ selectedUniversity.short_name }}
              </div>
            </div>
            <span
              v-if="selectedUniversity.status"
              class="badge bg-success/20 text-success rounded-full px-3 py-1 font-bold text-xs uppercase"
            >
              Active
            </span>
            <span
              v-else
              class="badge bg-danger/20 text-danger rounded-full px-3 py-1 font-bold text-xs uppercase"
            >
              Inactive
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-sm">
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block">Country</span>
              <span class="font-bold text-[#0e1726] dark:text-white">{{ selectedUniversity.country?.name || '—' }}</span>
            </div>
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block">Website</span>
              <a
                v-if="selectedUniversity.website"
                :href="selectedUniversity.website"
                target="_blank"
                class="text-primary hover:underline text-xs font-semibold inline-flex items-center gap-1"
              >
                {{ selectedUniversity.website }}
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
              </a>
              <span v-else class="text-xs text-gray-400 font-mono">—</span>
            </div>
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block">Email</span>
              <span class="text-xs font-semibold text-[#0e1726] dark:text-white">{{ selectedUniversity.email || '—' }}</span>
            </div>
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block">Phone</span>
              <span class="text-xs font-semibold text-[#0e1726] dark:text-white">{{ selectedUniversity.phone || '—' }}</span>
            </div>
          </div>

          <div v-if="selectedUniversity.address" class="pt-3 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
            <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block mb-1">Campus Address</span>
            <p class="text-xs text-[#0e1726] dark:text-[#cbd5e1] leading-relaxed whitespace-pre-line">{{ selectedUniversity.address }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-2">
          <button type="button" @click="showViewModal = false" class="btn btn-outline-dark text-xs">
            Close
          </button>
          <button
            type="button"
            @click="showViewModal = false; openEditModal(selectedUniversity)"
            class="btn btn-primary text-xs"
          >
            Edit University
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
  universities: Object,
  countries: Array,
  filters: Object,
})

const search = ref(props.filters?.search || '')
const countryId = ref(props.filters?.country_id || '')

const applyFilters = () => {
  router.get(route('admin.universities.index'), { search: search.value, country_id: countryId.value }, { preserveState: true })
}

const resetFilters = () => {
  search.value = ''
  countryId.value = ''
  router.get(route('admin.universities.index'))
}

// Modal states
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const selectedUniversity = ref(null)

const createForm = useForm({
  country_id: props.countries?.[0]?.id || '',
  name: '',
  short_name: '',
  website: '',
  email: '',
  phone: '',
  address: '',
  status: 1,
})

const editForm = useForm({
  id: null,
  country_id: '',
  name: '',
  short_name: '',
  website: '',
  email: '',
  phone: '',
  address: '',
  status: 1,
})

const openCreateModal = () => {
  createForm.reset()
  createForm.clearErrors()
  createForm.country_id = props.countries?.[0]?.id || ''
  createForm.status = 1
  showCreateModal.value = true
}

const openEditModal = (u) => {
  editForm.clearErrors()
  editForm.id = u.id
  editForm.country_id = u.country_id || ''
  editForm.name = u.name || ''
  editForm.short_name = u.short_name || ''
  editForm.website = u.website || ''
  editForm.email = u.email || ''
  editForm.phone = u.phone || ''
  editForm.address = u.address || ''
  editForm.status = u.status ? 1 : 0
  showEditModal.value = true
}

const openShowModal = (u) => {
  selectedUniversity.value = u
  showViewModal.value = true
}

const submitCreate = () => {
  createForm.post(route('admin.universities.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false
      createForm.reset()
    },
  })
}

const submitEdit = () => {
  editForm.put(route('admin.universities.update', editForm.id), {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false
    },
  })
}

const deleteUniversity = (u) => {
  if (confirm(`Are you sure you want to delete "${u.name}"?`)) {
    router.delete(route('admin.universities.destroy', u.id), { preserveScroll: true })
  }
}
</script>
