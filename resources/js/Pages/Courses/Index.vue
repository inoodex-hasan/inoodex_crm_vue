<template>
  <AdminLayout title="Degree Programs & Courses">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Degree Programs & Courses</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Academic course catalogue across partner universities and degree levels.</p>
        </div>
        <button
          type="button"
          @click="openCreateModal"
          class="btn btn-primary gap-2 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add Course
        </button>
      </div>

      <!-- Courses Panel -->
      <div class="panel">
        <!-- Filters Toolbar -->
        <div class="mb-5">
          <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
            <div class="relative w-60 shrink-0">
              <input
                v-model="search"
                type="text"
                placeholder="Search by course title..."
                class="form-input ltr:pr-9 rtl:pl-9 text-xs py-2 w-full"
              />
              <button type="submit" class="absolute inset-y-0 ltr:right-2.5 rtl:left-2.5 flex items-center text-gray-400 hover:text-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </button>
            </div>
            <select v-model="universityId" class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9">
              <option value="">All Universities</option>
              <option v-for="u in universities" :key="u.id" :value="u.id">{{ u.name }}</option>
            </select>
            <div class="flex items-center gap-1.5 shrink-0">
              <button type="submit" class="btn btn-primary btn-sm text-xs py-2 px-3">Filter</button>
              <button @click="resetFilters" type="button" class="btn btn-outline-danger btn-sm text-xs py-2 px-3">Reset</button>
            </div>
          </form>
        </div>

        <!-- Courses Table -->
        <div class="table-responsive">
          <table class="table-hover whitespace-nowrap w-full">
            <thead>
              <tr>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Course Title</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">University</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Degree Level</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Duration</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Tuition Fee</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Status</th>
                <th class="text-right font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
              <tr v-for="course in courses.data" :key="course.id" class="hover:bg-primary/5 transition">
                <td class="py-3.5 px-4">
                  <button
                    type="button"
                    @click="openShowModal(course)"
                    class="font-bold text-[#0e1726] dark:text-white text-sm hover:text-primary transition text-left"
                  >
                    {{ course.name }}
                  </button>
                </td>
                <td class="py-3.5 px-4 font-semibold text-[#0e1726] dark:text-[#d0d2d6] text-sm">
                  {{ course.university?.name || '—' }}
                </td>
                <td class="py-3.5 px-4">
                  <span class="badge bg-primary/10 text-primary font-bold text-xs uppercase tracking-wide px-2.5 py-1 rounded">
                    {{ course.degree_level || '—' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 font-medium text-[#3b3f5c] dark:text-[#cbd5e1] text-sm">{{ course.duration || '—' }}</td>
                <td class="py-3.5 px-4 font-bold text-[#0e1726] dark:text-white text-sm">
                  {{ course.tuition_fee ? ('৳' + Number(course.tuition_fee).toLocaleString()) : '—' }}
                </td>
                <td class="py-3.5 px-4">
                  <span
                    v-if="course.status"
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
                        @click="openShowModal(course)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        View Details
                      </button>
                      <button
                        type="button"
                        @click="openEditModal(course)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit Course
                      </button>
                      <button
                        type="button"
                        @click="deleteCourse(course)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-danger hover:bg-[#fff5f5] dark:hover:bg-[#121e32] transition"
                      >
                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Delete Course
                      </button>
                    </div>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="courses.data.length === 0">
                <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400 font-semibold text-sm">
                  No courses found matching criteria.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="courses" class="mt-4" />
      </div>
    </div>

    <!-- Create Course Modal -->
    <Modal :show="showCreateModal" @close="showCreateModal = false" title="Add Course" max-width="lg">
      <form @submit.prevent="submitCreate" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">University / Institution *</label>
            <select v-model="createForm.university_id" required class="form-select text-sm">
              <option value="">-- Select University --</option>
              <option v-for="u in universities" :key="u.id" :value="u.id">{{ u.name }}</option>
            </select>
            <p v-if="createForm.errors.university_id" class="mt-1 text-xs text-danger">{{ createForm.errors.university_id }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Course Title *</label>
            <input
              v-model="createForm.name"
              type="text"
              placeholder="e.g. MSc Data Science & Artificial Intelligence"
              required
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.name" class="mt-1 text-xs text-danger">{{ createForm.errors.name }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Degree Level</label>
            <input
              v-model="createForm.degree_level"
              type="text"
              placeholder="e.g. Undergraduate, Master's, PhD"
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.degree_level" class="mt-1 text-xs text-danger">{{ createForm.errors.degree_level }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Duration</label>
            <input
              v-model="createForm.duration"
              type="text"
              placeholder="e.g. 1 Year Full-Time"
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.duration" class="mt-1 text-xs text-danger">{{ createForm.errors.duration }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Estimated Tuition Fee (৳)</label>
            <input
              v-model.number="createForm.tuition_fee"
              type="number"
              step="0.01"
              min="0"
              placeholder="0.00"
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.tuition_fee" class="mt-1 text-xs text-danger">{{ createForm.errors.tuition_fee }}</p>
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
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Curriculum & Description</label>
            <textarea
              v-model="createForm.description"
              rows="3"
              placeholder="Course highlights, entry requirements, modules..."
              class="form-textarea text-sm"
            ></textarea>
            <p v-if="createForm.errors.description" class="mt-1 text-xs text-danger">{{ createForm.errors.description }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
          <button type="button" @click="showCreateModal = false" class="btn btn-outline-dark text-xs">
            Cancel
          </button>
          <button type="submit" :disabled="createForm.processing" class="btn btn-primary text-xs">
            {{ createForm.processing ? 'Saving...' : 'Create Course' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Edit Course Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false" title="Edit Course" max-width="lg">
      <form @submit.prevent="submitEdit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">University / Institution *</label>
            <select v-model="editForm.university_id" required class="form-select text-sm">
              <option value="">-- Select University --</option>
              <option v-for="u in universities" :key="u.id" :value="u.id">{{ u.name }}</option>
            </select>
            <p v-if="editForm.errors.university_id" class="mt-1 text-xs text-danger">{{ editForm.errors.university_id }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Course Title *</label>
            <input
              v-model="editForm.name"
              type="text"
              required
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.name" class="mt-1 text-xs text-danger">{{ editForm.errors.name }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Degree Level</label>
            <input
              v-model="editForm.degree_level"
              type="text"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.degree_level" class="mt-1 text-xs text-danger">{{ editForm.errors.degree_level }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Duration</label>
            <input
              v-model="editForm.duration"
              type="text"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.duration" class="mt-1 text-xs text-danger">{{ editForm.errors.duration }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Estimated Tuition Fee (৳)</label>
            <input
              v-model.number="editForm.tuition_fee"
              type="number"
              step="0.01"
              min="0"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.tuition_fee" class="mt-1 text-xs text-danger">{{ editForm.errors.tuition_fee }}</p>
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
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Curriculum & Description</label>
            <textarea
              v-model="editForm.description"
              rows="3"
              class="form-textarea text-sm"
            ></textarea>
            <p v-if="editForm.errors.description" class="mt-1 text-xs text-danger">{{ editForm.errors.description }}</p>
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

    <!-- Show Course Modal -->
    <Modal :show="showViewModal" @close="showViewModal = false" :title="selectedCourse?.name || 'Course Details'" max-width="lg">
      <div v-if="selectedCourse" class="space-y-4">
        <div class="rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] p-5 bg-white dark:bg-[#0e1726] space-y-4">
          <div class="flex justify-between items-start pb-3 border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Degree Program</span>
              <div class="text-base font-bold text-[#0e1726] dark:text-white">{{ selectedCourse.name }}</div>
              <div class="text-xs text-primary font-semibold mt-0.5">
                {{ selectedCourse.university?.name }}
              </div>
            </div>
            <span
              v-if="selectedCourse.status"
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

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-sm">
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block">Degree Level</span>
              <span class="font-bold text-[#0e1726] dark:text-white">{{ selectedCourse.degree_level || '—' }}</span>
            </div>
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block">Duration</span>
              <span class="font-bold text-[#0e1726] dark:text-white">{{ selectedCourse.duration || '—' }}</span>
            </div>
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block">Tuition Fee</span>
              <span class="font-bold text-[#0e1726] dark:text-white font-mono">
                {{ selectedCourse.tuition_fee ? ('৳' + Number(selectedCourse.tuition_fee).toLocaleString()) : '—' }}
              </span>
            </div>
          </div>

          <div v-if="selectedCourse.description" class="pt-3 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
            <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8] block mb-1">Program Overview</span>
            <p class="text-xs text-[#0e1726] dark:text-[#cbd5e1] leading-relaxed whitespace-pre-line">{{ selectedCourse.description }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-2">
          <button type="button" @click="showViewModal = false" class="btn btn-outline-dark text-xs">
            Close
          </button>
          <button
            type="button"
            @click="showViewModal = false; openEditModal(selectedCourse)"
            class="btn btn-primary text-xs"
          >
            Edit Course
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
  courses: Object,
  universities: Array,
  filters: Object,
})

const search = ref(props.filters?.search || '')
const universityId = ref(props.filters?.university_id || '')

const applyFilters = () => {
  router.get(route('admin.courses.index'), { search: search.value, university_id: universityId.value }, { preserveState: true })
}

const resetFilters = () => {
  search.value = ''
  universityId.value = ''
  router.get(route('admin.courses.index'))
}

// Modal states
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const selectedCourse = ref(null)

const createForm = useForm({
  university_id: props.universities?.[0]?.id || '',
  name: '',
  degree_level: '',
  duration: '',
  tuition_fee: null,
  description: '',
  status: 1,
})

const editForm = useForm({
  id: null,
  university_id: '',
  name: '',
  degree_level: '',
  duration: '',
  tuition_fee: null,
  description: '',
  status: 1,
})

const openCreateModal = () => {
  createForm.reset()
  createForm.clearErrors()
  createForm.university_id = props.universities?.[0]?.id || ''
  createForm.status = 1
  showCreateModal.value = true
}

const openEditModal = (course) => {
  editForm.clearErrors()
  editForm.id = course.id
  editForm.university_id = course.university_id || ''
  editForm.name = course.name || ''
  editForm.degree_level = course.degree_level || ''
  editForm.duration = course.duration || ''
  editForm.tuition_fee = course.tuition_fee ?? null
  editForm.description = course.description || ''
  editForm.status = course.status ? 1 : 0
  showEditModal.value = true
}

const openShowModal = (course) => {
  selectedCourse.value = course
  showViewModal.value = true
}

const submitCreate = () => {
  createForm.post(route('admin.courses.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false
      createForm.reset()
    },
  })
}

const submitEdit = () => {
  editForm.put(route('admin.courses.update', editForm.id), {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false
    },
  })
}

const deleteCourse = (course) => {
  if (confirm(`Are you sure you want to delete "${course.name}"?`)) {
    router.delete(route('admin.courses.destroy', course.id), { preserveScroll: true })
  }
}
</script>
