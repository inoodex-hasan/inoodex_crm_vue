<template>
  <AdminLayout title="Create User">
    <div class="space-y-6 max-w-4xl">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Create New User</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Create a system operator account and assign their responsibilities.</p>
        </div>
        <Link :href="route('tyro-dashboard.users.index')" class="btn btn-outline-dark btn-sm">
          ← Back to Users
        </Link>
      </div>

      <div class="panel">
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Full Name <span class="text-danger">*</span></label>
              <input
                v-model="form.name"
                type="text"
                class="form-input text-xs"
                placeholder="e.g. Sarah Jenkins"
                required
              />
              <span v-if="form.errors.name" class="text-danger text-xs mt-1 block">{{ form.errors.name }}</span>
            </div>

            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Email Address <span class="text-danger">*</span></label>
              <input
                v-model="form.email"
                type="email"
                class="form-input text-xs"
                placeholder="s.jenkins@inoodex.com"
                required
              />
              <span v-if="form.errors.email" class="text-danger text-xs mt-1 block">{{ form.errors.email }}</span>
            </div>

            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Username <span class="text-danger">*</span></label>
              <input
                v-model="form.username"
                type="text"
                class="form-input text-xs font-mono"
                placeholder="sjenkins"
                required
              />
              <span v-if="form.errors.username" class="text-danger text-xs mt-1 block">{{ form.errors.username }}</span>
            </div>

            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Designation</label>
              <select v-model="form.designation" class="form-select text-xs">
                <option value="">None / Standard</option>
                <option value="junior">Junior Consultant / Staff</option>
                <option value="senior">Senior Consultant / Staff</option>
              </select>
              <span v-if="form.errors.designation" class="text-danger text-xs mt-1 block">{{ form.errors.designation }}</span>
            </div>

            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Password <span class="text-danger">*</span></label>
              <input
                v-model="form.password"
                type="password"
                class="form-input text-xs"
                placeholder="Minimum 8 characters"
                required
              />
              <span v-if="form.errors.password" class="text-danger text-xs mt-1 block">{{ form.errors.password }}</span>
            </div>

            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Confirm Password <span class="text-danger">*</span></label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="form-input text-xs"
                placeholder="Repeat password"
                required
              />
            </div>
          </div>

          <!-- Roles Selection -->
          <div class="border-t border-[#e0e6ed] dark:border-[#1b2e4b] pt-5">
            <h3 class="text-sm font-bold text-[#0e1726] dark:text-white mb-3">Assign Roles</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
              <div
                v-for="r in roles"
                :key="r.id"
                class="border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg p-3 hover:border-primary/50 transition bg-[#f9fafb] dark:bg-[#121e32]"
              >
                <label class="flex items-start gap-2.5 cursor-pointer">
                  <input
                    type="checkbox"
                    :value="r.id"
                    v-model="form.roles"
                    class="form-checkbox text-primary mt-0.5"
                  />
                  <div>
                    <span class="text-xs font-bold text-[#0e1726] dark:text-white block">{{ r.name }}</span>
                    <span class="text-[11px] font-mono text-primary block">{{ r.slug }}</span>
                  </div>
                </label>
              </div>
            </div>
            <span v-if="form.errors.roles" class="text-danger text-xs mt-2 block">{{ form.errors.roles }}</span>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
            <Link :href="route('tyro-dashboard.users.index')" class="btn btn-outline-dark btn-sm">
              Cancel
            </Link>
            <button type="submit" class="btn btn-primary btn-sm" :disabled="form.processing">
              {{ form.processing ? 'Creating...' : 'Create User' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
  roles: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  name: '',
  email: '',
  username: '',
  designation: '',
  password: '',
  password_confirmation: '',
  roles: [],
});

function submit() {
  form.post(route('tyro-dashboard.users.store'));
}
</script>
