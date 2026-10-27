<template>
  <AdminLayout title="Edit User">
    <div class="space-y-6 max-w-4xl">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Edit User: {{ editUser.name }}</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Update operator details, designation, password, and security policies.</p>
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
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">
                New Password <span class="text-gray-400 font-normal">(Leave blank to keep existing)</span>
              </label>
              <input
                v-model="form.password"
                type="password"
                class="form-input text-xs"
                placeholder="Enter new password"
              />
              <span v-if="form.errors.password" class="text-danger text-xs mt-1 block">{{ form.errors.password }}</span>
            </div>

            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Confirm New Password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="form-input text-xs"
                placeholder="Repeat new password"
              />
            </div>
          </div>

          <div v-if="editUser.plain_password" class="p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded text-xs flex items-center justify-between">
            <span class="text-amber-800 dark:text-amber-300">
              Current Recorded Password: <strong class="font-mono">{{ editUser.plain_password }}</strong>
            </span>
          </div>

          <!-- Roles Selection -->
          <div class="border-t border-[#e0e6ed] dark:border-[#1b2e4b] pt-5">
            <h3 class="text-sm font-bold text-[#0e1726] dark:text-white mb-3">Assigned Roles</h3>
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

          <div class="flex items-center justify-between pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
            <div class="flex items-center gap-2">
              <button
                v-if="editUser.two_factor_confirmed_at"
                type="button"
                @click="reset2FA"
                class="btn btn-outline-warning btn-sm"
              >
                Reset 2FA
              </button>
            </div>

            <div class="flex items-center gap-3">
              <Link :href="route('tyro-dashboard.users.index')" class="btn btn-outline-dark btn-sm">
                Cancel
              </Link>
              <button type="submit" class="btn btn-primary btn-sm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Update User' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  editUser: Object,
  roles: {
    type: Array,
    default: () => [],
  },
  protectedUsers: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  name: props.editUser.name || '',
  email: props.editUser.email || '',
  username: props.editUser.username || '',
  designation: props.editUser.designation || '',
  password: '',
  password_confirmation: '',
  roles: (props.editUser.roles || []).map((r) => r.id),
});

function submit() {
  form.put(route('tyro-dashboard.users.update', props.editUser.id));
}

function reset2FA() {
  Swal.fire({
    title: 'Reset 2FA?',
    text: `Reset Two-Factor Authentication for ${props.editUser.name}? They will need to re-configure it next time.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, reset',
    confirmButtonColor: '#e2a03f',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('tyro-dashboard.users.2fa.reset', props.editUser.id), {
        preserveScroll: true,
      });
    }
  });
}
</script>
