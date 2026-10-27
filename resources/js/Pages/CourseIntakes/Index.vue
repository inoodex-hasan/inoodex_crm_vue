<template>
  <AdminLayout title="Course Admission Intakes">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Admission Intakes</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Semesters and intake dates (e.g., Fall, Spring, Winter) per academic program.</p>
        </div>
        <button
          type="button"
          @click="openCreateModal"
          class="btn btn-primary gap-2 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add Intake
        </button>
      </div>

      <!-- Intakes Panel -->
      <div class="panel">
        <!-- Filters Toolbar -->
        <div class="mb-5">
          <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
            <div class="relative w-60 shrink-0">
              <input
                v-model="search"
                type="text"
                placeholder="Search by intake term..."
                class="form-input ltr:pr-9 rtl:pl-9 text-xs py-2 w-full"
              />
              <button type="submit" class="absolute inset-y-0 ltr:right-2.5 rtl:left-2.5 flex items-center text-gray-400 hover:text-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </button>
            </div>
            <select v-model="courseId" class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9">
              <option value="">All Courses</option>
              <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <div class="flex items-center gap-1.5 shrink-0">
              <button type="submit" class="btn btn-primary btn-sm text-xs py-2 px-3">Filter</button>
              <button @click="resetFilters" type="button" class="btn btn-outline-danger btn-sm text-xs py-2 px-3">Reset</button>
            </div>
          </form>
        </div>

        <!-- Intakes Table -->
        <div class="table-responsive">
          <table class="table-hover whitespace-nowrap w-full">
            <thead>
              <tr>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Intake Name</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Course / University</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Start Date</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Application Deadline</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Class Start</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Status</th>
                <th class="text-right font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
              <tr v-for="intake in intakes.data" :key="intake.id" class="hover:bg-primary/5 transition">
                <td class="py-3.5 px-4">
                  <button
                    type="button"
                    @click="openShowModal(intake)"
                    class="font-bold text-[#0e1726] dark:text-white text-sm hover:text-primary transition text-left"
                  >
                    {{ intake.intake_name }}
                  </button>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-bold text-[#0e1726] dark:text-white text-sm">{{ intake.course?.name || '—' }}</div>
                  <div class="text-xs text-[#506690] dark:text-[#888ea8] mt-0.5">{{ intake.course?.university?.name }}</div>
                </td>
                <td class="py-3.5 px-4 text-xs font-semibold text-[#3b3f5c] dark:text-[#cbd5e1]">{{ intake.application_start_date ? new Date(intake.application_start_date).toLocaleDateString() : '—' }}</td>
                <td class="py-3.5 px-4 text-xs font-bold text-danger">
                  {{ intake.application_deadline ? new Date(intake.application_deadline).toLocaleDateString() : '—' }}
                </td>
                <td class="py-3.5 px-4 text-xs font-semibold text-[#3b3f5c] dark:text-[#cbd5e1]">{{ intake.class_start_date ? new Date(intake.class_start_date).toLocaleDateString() : '—' }}</td>
                <td class="py-3.5 px-4">
                  <span
                    v-if="intake.status"
                    class="badge bg-success/20 text-success rounded-full px-3 py-1 font-bold text-xs uppercase tracking-wide"
                  >
                    Open
                  </span>
                  <span
                    v-else
                    class="badge bg-danger/20 text-danger rounded-full px-3 py-1 font-bold text-xs uppercase tracking-wide"
                  >
                    Closed
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <TableDropdown>
                    <div class="py-1">
                      <button
                        type="button"
                        @click="openShowModal(intake)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        View Details
                      </button>
                      <button
                        type="button"
                        @click="openEditModal(intake)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit Intake
                      </button>
                      <button
                        type="button"
                        @click="deleteIntake(intake)"
                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-danger hover:bg-[#fff5f5] dark:hover:bg-[#121e32] transition"
                      >
                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Delete Intake
                      </button>
                    </div>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="intakes.data.length === 0">
                <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400 font-semibold text-sm">
                  No intakes defined yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="intakes" class="mt-4" />
      </div>
    </div>

    <!-- Create Intake Modal -->
    <Modal :show="showCreateModal" @close="showCreateModal = false" title="Add Course Intake" max-width="md">
      <form @submit.prevent="submitCreate" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Target Course *</label>
          <select v-model="createForm.course_id" required class="form-select text-sm">
            <option value="">-- Select Course --</option>
            <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <p v-if="createForm.errors.course_id" class="mt-1 text-xs text-danger">{{ createForm.errors.course_id }}</p>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Intake Term / Name *</label>
          <input
            v-model="createForm.intake_name"
            type="text"
            placeholder="e.g. September 2026 or Spring 2027"
            required
            class="form-input text-sm"
          />
          <p v-if="createForm.errors.intake_name" class="mt-1 text-xs text-danger">{{ createForm.errors.intake_name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Application Opens</label>
            <input
              v-model="createForm.application_start_date"
              type="date"
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.application_start_date" class="mt-1 text-xs text-danger">{{ createForm.errors.application_start_date }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Application Deadline</label>
            <input
              v-model="createForm.application_deadline"
              type="date"
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.application_deadline" class="mt-1 text-xs text-danger">{{ createForm.errors.application_deadline }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Classes Commence</label>
            <input
              v-model="createForm.class_start_date"
              type="date"
              class="form-input text-sm"
            />
            <p v-if="createForm.errors.class_start_date" class="mt-1 text-xs text-danger">{{ createForm.errors.class_start_date }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Status *</label>
            <select v-model="createForm.status" required class="form-select text-sm">
              <option :value="1">Open</option>
              <option :value="0">Closed</option>
            </select>
            <p v-if="createForm.errors.status" class="mt-1 text-xs text-danger">{{ createForm.errors.status }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
          <button type="button" @click="showCreateModal = false" class="btn btn-outline-dark text-xs">
            Cancel
          </button>
          <button type="submit" :disabled="createForm.processing" class="btn btn-primary text-xs">
            {{ createForm.processing ? 'Saving...' : 'Create Intake' }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Edit Intake Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false" title="Edit Course Intake" max-width="md">
      <form @submit.prevent="submitEdit" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Target Course *</label>
          <select v-model="editForm.course_id" required class="form-select text-sm">
            <option value="">-- Select Course --</option>
            <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <p v-if="editForm.errors.course_id" class="mt-1 text-xs text-danger">{{ editForm.errors.course_id }}</p>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Intake Term / Name *</label>
          <input
            v-model="editForm.intake_name"
            type="text"
            required
            class="form-input text-sm"
          />
          <p v-if="editForm.errors.intake_name" class="mt-1 text-xs text-danger">{{ editForm.errors.intake_name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Application Opens</label>
            <input
              v-model="editForm.application_start_date"
              type="date"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.application_start_date" class="mt-1 text-xs text-danger">{{ editForm.errors.application_start_date }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Application Deadline</label>
            <input
              v-model="editForm.application_deadline"
              type="date"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.application_deadline" class="mt-1 text-xs text-danger">{{ editForm.errors.application_deadline }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Classes Commence</label>
            <input
              v-model="editForm.class_start_date"
              type="date"
              class="form-input text-sm"
            />
            <p v-if="editForm.errors.class_start_date" class="mt-1 text-xs text-danger">{{ editForm.errors.class_start_date }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#0e1726] dark:text-white mb-1.5">Status *</label>
            <select v-model="editForm.status" required class="form-select text-sm">
              <option :value="1">Open</option>
              <option :value="0">Closed</option>
            </select>
            <p v-if="editForm.errors.status" class="mt-1 text-xs text-danger">{{ editForm.errors.status }}</p>
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

    <!-- Show Intake Modal -->
    <Modal :show="showViewModal" @close="showViewModal = false" :title="selectedIntake?.intake_name || 'Intake Details'" max-width="md">
      <div v-if="selectedIntake" class="space-y-4">
        <div class="rounded-xl border border-[#e0e6ed] dark:border-[#1b2e4b] p-5 bg-white dark:bg-[#0e1726] space-y-3.5">
          <div class="flex justify-between items-start pb-3 border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
            <div>
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Intake Term</span>
              <div class="text-base font-bold text-[#0e1726] dark:text-white">{{ selectedIntake.intake_name }}</div>
              <div class="text-xs text-primary font-semibold mt-0.5">
                {{ selectedIntake.course?.name }}
              </div>
              <div v-if="selectedIntake.course?.university?.name" class="text-xs text-[#506690] dark:text-[#888ea8]">
                {{ selectedIntake.course.university.name }}
              </div>
            </div>
            <span
              v-if="selectedIntake.status"
              class="badge bg-success/20 text-success rounded-full px-3 py-1 font-bold text-xs uppercase"
            >
              Open
            </span>
            <span
              v-else
              class="badge bg-danger/20 text-danger rounded-full px-3 py-1 font-bold text-xs uppercase"
            >
              Closed
            </span>
          </div>

          <div class="space-y-3 text-sm">
            <div class="flex justify-between items-center pb-2.5 border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Application Window Opens</span>
              <span class="font-bold text-[#0e1726] dark:text-white text-xs">
                {{ selectedIntake.application_start_date ? new Date(selectedIntake.application_start_date).toLocaleDateString() : '—' }}
              </span>
            </div>
            <div class="flex justify-between items-center pb-2.5 border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
              <span class="text-xs uppercase font-bold tracking-wider text-danger">Application Deadline</span>
              <span class="font-bold text-danger text-xs">
                {{ selectedIntake.application_deadline ? new Date(selectedIntake.application_deadline).toLocaleDateString() : '—' }}
              </span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs uppercase font-bold tracking-wider text-[#506690] dark:text-[#888ea8]">Classes Commence</span>
              <span class="font-bold text-[#0e1726] dark:text-white text-xs">
                {{ selectedIntake.class_start_date ? new Date(selectedIntake.class_start_date).toLocaleDateString() : '—' }}
              </span>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-2">
          <button type="button" @click="showViewModal = false" class="btn btn-outline-dark text-xs">
            Close
          </button>
          <button
            type="button"
            @click="showViewModal = false; openEditModal(selectedIntake)"
            class="btn btn-primary text-xs"
          >
            Edit Intake
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
  intakes: Object,
  courses: Array,
  filters: Object,
})

const search = ref(props.filters?.search || '')
const courseId = ref(props.filters?.course_id || '')

const applyFilters = () => {
  router.get(route('admin.course-intakes.index'), { search: search.value, course_id: courseId.value }, { preserveState: true })
}

const resetFilters = () => {
  search.value = ''
  courseId.value = ''
  router.get(route('admin.course-intakes.index'))
}

// Modal states
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const selectedIntake = ref(null)

const createForm = useForm({
  course_id: props.courses?.[0]?.id || '',
  intake_name: '',
  application_start_date: '',
  application_deadline: '',
  class_start_date: '',
  status: 1,
})

const editForm = useForm({
  id: null,
  course_id: '',
  intake_name: '',
  application_start_date: '',
  application_deadline: '',
  class_start_date: '',
  status: 1,
})

const openCreateModal = () => {
  createForm.reset()
  createForm.clearErrors()
  createForm.course_id = props.courses?.[0]?.id || ''
  createForm.status = 1
  showCreateModal.value = true
}

// Format date string to YYYY-MM-DD for date input
const formatDateForInput = (val) => {
  if (!val) return ''
  try {
    const d = new Date(val)
    if (isNaN(d.getTime())) return ''
    return d.toISOString().split('T')[0]
  } catch (e) {
    return ''
  }
}

const openEditModal = (intake) => {
  editForm.clearErrors()
  editForm.id = intake.id
  editForm.course_id = intake.course_id || ''
  editForm.intake_name = intake.intake_name || ''
  editForm.application_start_date = formatDateForInput(intake.application_start_date)
  editForm.application_deadline = formatDateForInput(intake.application_deadline)
  editForm.class_start_date = formatDateForInput(intake.class_start_date)
  editForm.status = intake.status ? 1 : 0
  showEditModal.value = true
}

const openShowModal = (intake) => {
  selectedIntake.value = intake
  showViewModal.value = true
}

const submitCreate = () => {
  createForm.post(route('admin.course-intakes.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false
      createForm.reset()
    },
  })
}

const submitEdit = () => {
  editForm.put(route('admin.course-intakes.update', editForm.id), {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false
    },
  })
}

const deleteIntake = (intake) => {
  if (confirm(`Are you sure you want to delete intake "${intake.intake_name}"?`)) {
    router.delete(route('admin.course-intakes.destroy', intake.id), { preserveScroll: true })
  }
}
</script>
